<?php

/** 通讯录回调通知 */

namespace app\common\lib\corpwechat\event;

use app\common\lib\corpwechat\User;
use think\Cache;
use think\Log;

class ChangeContactEvent extends Event {
  /**
   * @Purpose:
   * 新增成员事件
   * @Method createUser()
   *
   * @param $decryMsg
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/18
   */
  public function createUser($decryMsg) {
    /**
     * 1.判断企业微信成员UserID
     * 1.）不存在，写入
     * 2.）存在，跳过
     */
    $user_id_is_set = $this->userIDIsSet($decryMsg['UserID'], USER_TYPE_STAFF);
    if (false === $user_id_is_set) {
      model('corpwechat.CorpwechatUserAll')
        ->data(
          [
            'userid' => $decryMsg['UserID'],
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
    $staff_is_set = $this->staffIsSet($decryMsg['UserID']);
    if (false === $staff_is_set) {
      // 调用企业微信【读取成员】API
      $corp_user = new User($this->corpId, $this->corpSecret);
      $user_get = $corp_user->UserGet($decryMsg['UserID']);
      if (false === $user_get) {
        Log::error('[user_get]API请求失败。' . $corp_user->getError());
        return;
      }

      // 写入`corpwechat_staff`表
      model('corpwechat.CorpwechatStaff')
        ->allowField(true)
        ->isUpdate(false)
        ->save(
          [
            'userid' => $decryMsg['UserID'],
            'department' => isset($user_get['department']) ? $user_get['department'] : [],
            'mobile' => isset($user_get['mobile']) ? $user_get['mobile'] : '',
            'email' => isset($user_get['email']) ? $user_get['email'] : '',
            'status' => isset($user_get['status']) ? $user_get['status'] : '',
            'qr_code' => isset($user_get['qr_code']) ? $user_get['qr_code'] : '',
            'alias' => isset($user_get['alias']) ? $user_get['alias'] : '',
            'biz_mail' => isset($user_get['biz_mail']) ? $user_get['biz_mail'] : ''
          ]
        );

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
            'userid' => $decryMsg['UserID'],
          ]
        );
    }
  }


  /**
   * @Purpose:
   * 更新成员事件
   * @Method updateUser()
   *
   * @param $decryMsg
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/8
   */
  public function updateUser($decryMsg) {
    /**
     * 1.判断是否修改UserID
     * 1.）不存在，调用接口获取员工详情，并写入
     * 2.）不存在，正常更新
     */
    if (isset($decryMsg['NewUserID']) && !empty($decryMsg['NewUserID'])) {
      /**
       * 存在新的UserID
       * 特殊更新逻辑
       */
      // 调用企业微信【读取成员】API
      $corp_user = new User($this->corpId, $this->corpSecret);
      $user_get = $corp_user->UserGet($decryMsg['NewUserID']);
      if (false === $user_get) {
        Log::error('[user_get]API请求失败。' . $corp_user->getError());
        return;
      }

      model('corpwechat.CorpwechatUseridChangeLog')->log($decryMsg['UserID'], $decryMsg['NewUserID']);

      /**
       * 1.判断企业微信成员UserID
       * 1.）不存在，写入
       * 2.）存在，更新
       */
      $user_id_is_set = $this->userIDIsSet($decryMsg['UserID'], USER_TYPE_STAFF);
      if (false === $user_id_is_set) {
        model('corpwechat.CorpwechatUserAll')
          ->data(
            [
              'userid' => $decryMsg['NewUserID'],
              'user_type' => USER_TYPE_STAFF,
              'register' => 4,
              'name' => isset($user_get['name']) ? $user_get['name'] : '',
              'gender' => isset($user_get['gender']) ? $user_get['gender'] : 0,
              'avatar' => isset($user_get['avatar']) ? $user_get['avatar'] : '',
              'thumb_avatar' => isset($user_get['thumb_avatar']) ? $user_get['thumb_avatar'] : '',
            ]
          )
          ->allowField(true)
          ->isUpdate(false)
          ->save();
      } else {
        // 更新`corpwechat_user_all`表
        model('corpwechat.CorpwechatUserAll')
          ->allowField(true)
          ->isUpdate(true)
          ->save(
            [
              'userid' => $decryMsg['NewUserID'],
              'name' => isset($user_get['name']) ? $user_get['name'] : '',
              'gender' => isset($user_get['gender']) ? $user_get['gender'] : 0,
              'avatar' => isset($user_get['avatar']) ? $user_get['avatar'] : '',
              'thumb_avatar' => isset($user_get['thumb_avatar']) ? $user_get['thumb_avatar'] : '',
            ],
            [
              'userid' => $decryMsg['UserID'],
            ]
          );
      }

      /**
       * 2.判断员工是否存在
       * 1）.不存在，写入
       * 2）.存在，更新
       */
      $staff_is_set = $this->staffIsSet($decryMsg['UserID']);
      if (false === $staff_is_set) {
        // 写入`corpwechat_staff`表
        model('corpwechat.CorpwechatStaff')
          ->allowField(true)
          ->isUpdate(false)
          ->save(
            [
              'userid' => $decryMsg['NewUserID'],
              'department' => isset($user_get['department']) ? $user_get['department'] : [],
              'mobile' => isset($user_get['mobile']) ? $user_get['mobile'] : '',
              'email' => isset($user_get['email']) ? $user_get['email'] : '',
              'status' => isset($user_get['status']) ? $user_get['status'] : '',
              'qr_code' => isset($user_get['qr_code']) ? $user_get['qr_code'] : '',
              'alias' => isset($user_get['alias']) ? $user_get['alias'] : '',
              'biz_mail' => isset($user_get['biz_mail']) ? $user_get['biz_mail'] : ''
            ]
          );
      } else {
        // 更新`corpwechat_staff`表
        model('corpwechat.CorpwechatStaff')
          ->allowField(true)
          ->isUpdate(true)
          ->save(
            [
              'userid' => $decryMsg['NewUserID'],
              'department' => isset($user_get['department']) ? $user_get['department'] : [],
              'mobile' => isset($user_get['mobile']) ? $user_get['mobile'] : '',
              'email' => isset($user_get['email']) ? $user_get['email'] : '',
              'status' => isset($user_get['status']) ? $user_get['status'] : '',
              'qr_code' => isset($user_get['qr_code']) ? $user_get['qr_code'] : '',
              'alias' => isset($user_get['alias']) ? $user_get['alias'] : '',
              'biz_mail' => isset($user_get['biz_mail']) ? $user_get['biz_mail'] : ''
            ],
            [
              'userid' => $decryMsg['UserID'],
            ]
          );
      }

      /**
       * 3.判断是否已绑定企业微信
       * 1）.已绑定，更换绑定UserID
       * 2）.未绑定，跳过
       */
      $adminInfo = $this->getAdminInfoByUserId($decryMsg['UserID']);
      if (false != $adminInfo) {
        model('Admin')
          ->allowField(true)
          ->isUpdate(true)
          ->save(
            [
              'qy_userid' => $decryMsg['NewUserID']
            ],
            [
              'id' => $adminInfo->id,
              'bind_qywx' => 1,
              'qy_userid' => $decryMsg['UserID']
            ]
          );

        model('AdminLog')->record(
          '企业微信修改绑定，企业删除成员【UserId - ' . $decryMsg['NewUserID'] . '】',
          $adminInfo
        );
      }

      /** 4.更新所有企微客户关系 */
      model('corpwechat.CorpwechatExternalUser')
        ->allowField(true)
        ->isUpdate(true)
        ->save(
          [
            'userid' => $decryMsg['NewUserID']
          ],
          [
            'userid' => $decryMsg['UserID']
          ]
        );
    } else {
      /**
       * 不存在新的UserID
       * 常规更新逻辑
       */
      // 调用企业微信【读取成员】API
      $corp_user = new User($this->corpId, $this->corpSecret);
      $user_get = $corp_user->UserGet($decryMsg['UserID']);
      if (false === $user_get) {
        Log::error('[user_get]API请求失败。' . $corp_user->getError());
        return;
      }

      /**
       * 1.判断企业微信成员UserID
       * 1.）不存在，写入
       * 2.）存在，更新
       */
      $user_id_is_set = $this->userIDIsSet($decryMsg['UserID'], USER_TYPE_STAFF);
      if (false === $user_id_is_set) {
        model('corpwechat.CorpwechatUserAll')
          ->data(
            [
              'userid' => $decryMsg['UserID'],
              'user_type' => USER_TYPE_STAFF,
              'register' => 4,
              'name' => isset($user_get['name']) ? $user_get['name'] : '',
              'gender' => isset($user_get['gender']) ? $user_get['gender'] : 0,
              'avatar' => isset($user_get['avatar']) ? $user_get['avatar'] : '',
              'thumb_avatar' => isset($user_get['thumb_avatar']) ? $user_get['thumb_avatar'] : '',
            ]
          )
          ->allowField(true)
          ->isUpdate(false)
          ->save();
      } else {
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
              'userid' => $decryMsg['UserID'],
            ]
          );
      }

      /**
       * 2.判断员工是否存在
       * 1）.不存在，写入
       * 2）.存在，更新
       */
      $staff_is_set = $this->staffIsSet($decryMsg['UserID']);
      if (false === $staff_is_set) {
        // 写入`corpwechat_staff`表
        model('corpwechat.CorpwechatStaff')
          ->allowField(true)
          ->isUpdate(false)
          ->save(
            [
              'userid' => $decryMsg['UserID'],
              'department' => isset($user_get['department']) ? $user_get['department'] : [],
              'mobile' => isset($user_get['mobile']) ? $user_get['mobile'] : '',
              'email' => isset($user_get['email']) ? $user_get['email'] : '',
              'status' => isset($user_get['status']) ? $user_get['status'] : '',
              'qr_code' => isset($user_get['qr_code']) ? $user_get['qr_code'] : '',
              'alias' => isset($user_get['alias']) ? $user_get['alias'] : '',
              'biz_mail' => isset($user_get['biz_mail']) ? $user_get['biz_mail'] : ''
            ]
          );
      } else {
        // 更新`corpwechat_staff`表
        model('corpwechat.CorpwechatStaff')
          ->allowField(true)
          ->isUpdate(true)
          ->save(
            [
              'department' => isset($user_get['department']) ? $user_get['department'] : [],
              'mobile' => isset($user_get['mobile']) ? $user_get['mobile'] : '',
              'email' => isset($user_get['email']) ? $user_get['email'] : '',
              'status' => isset($user_get['status']) ? $user_get['status'] : '',
              'qr_code' => isset($user_get['qr_code']) ? $user_get['qr_code'] : '',
              'alias' => isset($user_get['alias']) ? $user_get['alias'] : '',
              'biz_mail' => isset($user_get['biz_mail']) ? $user_get['biz_mail'] : ''
            ],
            [
              'userid' => $decryMsg['UserID'],
            ]
          );
      }
    }
  }


  /**
   * @Purpose:
   * 删除成员事件
   * @Method deleteUser()
   *
   * @param $decryMsg
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/8
   */
  public function deleteUser($decryMsg) {
    $staffIsSet = $this->staffIsSet($decryMsg['UserID']);
    if (true === $staffIsSet) {
      model('corpwechat.CorpwechatStaff')
        ->where('userid', $decryMsg['UserID'])
        ->delete();
    }

    $adminInfo = $this->getAdminInfoByUserId($decryMsg['UserID']);
    if (false != $adminInfo) {
      model('Admin')
        ->allowField(true)
        ->isUpdate(true)
        ->save(
          [
            'bind_qywx' => 0,
            'qy_userid' => ''
          ],
          [
            'id' => $adminInfo->id,
            'bind_qywx' => 1,
            'qy_userid' => $decryMsg['UserID']
          ]
        );

      model('AdminLog')->record(
        '企业微信解绑，企业删除成员【UserId - ' . $decryMsg['UserID'] . '】',
        $adminInfo
      );
    }
  }
}
