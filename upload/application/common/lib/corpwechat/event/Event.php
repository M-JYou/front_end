<?php

namespace app\common\lib\corpwechat\event;

use app\common\lib\corpwechat\User;
use think\Log;

/** 成员类型[1:企业成员;2:外部联系人;] */
define('USER_TYPE_ALL', 0);
define('USER_TYPE_STAFF', 1);
define('USER_TYPE_EXTERNAL', 2);

class Event {
  /**
   * @Purpose
   * 企业ID
   * @var string
   */
  protected $corpId = '';

  /**
   * @Purpose
   * 应用ID
   * @var string
   */
  protected $agentId = '';

  /**
   * @Purpose
   * 应用的凭证密钥
   * @var string
   */
  protected $corpSecret = '';

  /**
   * @Purpose
   * 通讯录的凭证秘钥
   * @var string
   */
  protected $customerContactSecret = '';

  /**
   * @Purpose
   * 错误信息
   * @var string
   */
  public $errorMessage = '';


  public function __construct() {
    Log::init([
      'type' => 'File',
      'path' => LOG_PATH . 'corpwechat_event/',
      'level' => ['error']
    ]);

    $apiConfig = config('global_config.corpwechat_api');
    if (empty($apiConfig) || !isset($apiConfig) || !is_array($apiConfig)) {
      Log::error('企微服务未配置');
      die('请先完成企业微信配置');
    }

    $is_open = $apiConfig['is_open'] ? intval($apiConfig['is_open']) : -1;

    switch ($is_open) {
      case 1:
        $this->corpId = $apiConfig['corpid'] ? $apiConfig['corpid'] : '';
        $this->agentId = $apiConfig['agentid'] ? $apiConfig['agentid'] : '';
        $this->corpSecret = $apiConfig['corpsecret'] ? $apiConfig['corpsecret'] : '';
        $this->customerContactSecret = $apiConfig['customer_contact_secret'] ? $apiConfig['customer_contact_secret'] : '';
        break;

      case -1:
      default:
        Log::error('企微服务状态异常');
        die('企微服务状态异常');
    }
  }


  public function getError() {
    return $this->errorMessage;
  }


  /**
   * @Purpose:
   * 判断企业微信UserID是否存在
   * @Method userIDIsSet()
   *
   * @param $userID
   * @param int $userType 成员类型[1:企业成员;2:外部联系人;]
   *
   * @return bool
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/17
   */
  protected function userIDIsSet($userID, $userType = 2) {
    $isSet = model('corpwechat.CorpwechatUserAll')
      ->where('userid', $userID)
      ->where('user_type', $userType)
      ->find();

    if (null === $isSet) {
      return false;
    } else {
      return true;
    }
  }


  /**
   * @Purpose:
   * 根据头像获取头像缩略图
   * @Method getThumbAvatarByAvatar()
   *
   * @param $avatar
   *
   * @return string
   *
   * @throws null
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/17
   */
  protected function getThumbAvatarByAvatar($avatar) {
    if (false !== strpos($avatar, 'wework.qpic.cn')) {
      // 企微获取头像缩略图
      $thumb_avatar = substr($avatar, 0, -1);
      $thumb_avatar .= 100;
    } elseif (false !== strpos($avatar, 'wx.qlogo.cn')) {
      // 微信获取头像缩略图
      $thumb_avatar = substr($avatar, 0, -1);
      $thumb_avatar .= 96;
    } else {
      $thumb_avatar = $avatar;
    }
    return $thumb_avatar;
  }

  /**
   * @Purpose:
   * 判断企业微信成员UserID是否存在
   * @Method staffIsSet()
   *
   * @param $userID
   *
   * @return bool
   *
   * @throws null
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/18
   */
  public function staffIsSet($userID) {
    $isSet = model('corpwechat.CorpwechatStaff')
      ->where('userid', $userID)
      ->find();

    if (null === $isSet) {
      return false;
    } else {
      return true;
    }
  }

  public function externalUserIsSet($userID, $externalUserID) {
    $isSet = model('corpwechat.CorpwechatExternalUser')
      ->where('userid', $userID)
      ->where('external_user_id', $externalUserID)
      ->find();

    if (null === $isSet) {
      return false;
    } else {
      return true;
    }
  }


  public function getUserNameByUserId($userID) {
    $name = model('corpwechat.CorpwechatUserAll')
      ->where('userid', $userID)
      ->value('name');

    if (null === $name) {
      return $userID;
    } else {
      return $name;
    }
  }

  public function isBindAdminByUserId($userID) {
    $name = model('Admin')
      ->where('qy_userid', $userID)
      ->where('bind_qywx', 1)
      ->find();

    if (null === $name) {
      return false;
    } else {
      return true;
    }
  }

  public function getAdminInfoByUserId($userID) {
    $info = model('Admin')
      ->where('qy_userid', $userID)
      ->where('bind_qywx', 1)
      ->find();

    if (null === $info) {
      return false;
    } else {
      return $info;
    }
  }

  public function groupChatIsSet($chatID) {
    $isSet = model('corpwechat.CorpwechatGroupChat')
      ->where('chat_id', $chatID)
      ->find();

    if (null === $isSet) {
      return false;
    } else {
      return true;
    }
  }

  public function checkStaff($userID) {
    /**
     * 1.判断企业微信成员UserID
     * 1.）不存在，写入
     * 2.）存在，跳过
     */
    $user_id_is_set = $this->userIDIsSet($userID, USER_TYPE_STAFF);
    if (false === $user_id_is_set) {
      model('corpwechat.CorpwechatUserAll')
        ->data(
          [
            'userid' => $userID,
            'user_type' => USER_TYPE_STAFF,
            'register' => 4
          ]
        )
        ->allowField(true)
        ->isUpdate(false)
        ->save();
    }

    /**
     * 2.判断企业微信成员是否进入员工表`corpwechat_staff`
     * 1.）不存在，调用接口获取员工详情，并写入
     * 2.）存在，跳过
     */
    $staff_is_set = $this->staffIsSet($userID);
    if (false === $staff_is_set) {
      // 调用企业微信【读取成员】API
      $corp_user = new User($this->corpId, $this->corpSecret);
      $user_get = $corp_user->UserGet($userID);
      if (false === $user_get) {
        Log::error('[user_get]API请求失败。' . $corp_user->getError());
        return false;
      }

      // 写入`corpwechat_staff`表
      model('corpwechat.CorpwechatStaff')
        ->data(
          [
            'userid' => $userID,
            'department' => isset($user_get['department']) ? $user_get['department'] : [],
            'mobile' => isset($user_get['mobile']) ? $user_get['mobile'] : '',
            'email' => isset($user_get['email']) ? $user_get['email'] : '',
            'status' => isset($user_get['status']) ? $user_get['status'] : '',
            'qr_code' => isset($user_get['qr_code']) ? $user_get['qr_code'] : '',
            'alias' => isset($user_get['alias']) ? $user_get['alias'] : '',
            'biz_mail' => isset($user_get['biz_mail']) ? $user_get['biz_mail'] : ''
          ]
        )
        ->allowField(true)
        ->isUpdate(false)
        ->save();

      // 更新`corpwechat_user_all`表
      model('corpwechat.CorpwechatUserAll')
        ->allowField(true)
        ->isUpdate(true)
        ->save(
          [
            'name' => isset($user_get['name']) ? $user_get['name'] : '',
            'gender' => isset($user_get['gender']) ? $user_get['gender'] : 0,
            'avatar' => isset($user_get['avatar']) ? $user_get['avatar'] : '',
            'thumb_avatar' => isset($user_get['thumb_avatar']) ? $user_get['thumb_avatar'] : '',
          ],
          [
            'userid' => $userID,
          ]
        );
    } else {
      return true;
    }
  }
}
