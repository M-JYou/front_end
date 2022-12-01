<?php

namespace app\common\lib\corpwechat\event;

use app\common\lib\corpwechat\ExternalContact;
use Exception;
use think\Db;
use think\Log;

class ChangeExternalChatEvent extends Event {
  /** 客户群变更事件
   * @Method dismissChat()
   *
   * @param $decryMsg
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/22
   */
  public function updateChat($decryMsg) {
    switch ($decryMsg['UpdateDetail']) {
      case 'add_member':
        // 成员入群
        model('corpwechat.CorpwechatGroupLog')->record(
          $decryMsg['ChatId'],
          $decryMsg['MemChangeCnt'],
          ADD_MEMBER,
          $decryMsg['CreateTime']
        );
        break;

      case 'del_member':
        // 成员退群
        model('corpwechat.CorpwechatGroupLog')->record(
          $decryMsg['ChatId'],
          $decryMsg['MemChangeCnt'],
          DEL_MEMBER,
          $decryMsg['CreateTime']
        );
        break;

      case 'change_owner':
        // 群主变更
      case 'change_name':
        // 群名变更
      case 'change_notice':
        // 群公告变更
        break;

      default:
        $this->errorMessage = '错误的客户群变更事件【变更详情】';
        return false;
    }

    $this->writeGroupChat($decryMsg['ChatId'], false);

    return true;
  }


  /** 客户群创建事件
   * @Method dismissChat()
   *
   * @param $decryMsg
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/22
   */
  public function createChat($decryMsg) {
    $chatInfo = model('corpwechat.CorpwechatGroupChat')
      ->where('chat_id', $decryMsg['ChatId'])
      ->find();

    if (null === $chatInfo) {
      $create = $this->writeGroupChat($decryMsg['ChatId'], true);
      if (false === $create) {
        return false;
      }
    } else {
      $this->errorMessage = '客户群已存在';
      return false;
    }
  }


  /** 客户群解散事件
   * @Method dismissChat()
   *
   * @param $decryMsg
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/22
   */
  public function dismissChat($decryMsg) {
    $chatInfo = model('corpwechat.CorpwechatGroupChat')
      ->where('chat_id', $decryMsg['ChatId'])
      ->find();

    if (null === $chatInfo) {
      $this->errorMessage = '客户群不存在';
      return false;
    }

    Db::startTrans();
    try {
      // 事务1.删除客户群信息
      $del_result = model('corpwechat.CorpwechatGroupChat')
        ->where('chat_id', $decryMsg['ChatId'])
        ->delete();
      if (false === $del_result) {
        throw new Exception(model('corpwechat.CorpwechatGroupChat')->getError());
      }

      // 事务2.删除客户群成员信息
      $admin_result = model('corpwechat.CorpwechatGroupUser')
        ->where('chat_id', $decryMsg['ChatId'])
        ->delete();
      if (false === $admin_result) {
        throw new Exception(model('corpwechat.CorpwechatGroupUser')->getError());
      }
      // 提交事务
      Db::commit();
    } catch (Exception $e) {
      // 回滚事务
      Db::rollBack();
      $this->errorMessage = '【客户群解散事件异步回调失败-DB事务】：' . $e->getMessage();
      return false;
    }

    return true;
  }

  public function getGroupChatInfoById($chatId, $needName = 1) {
    /** 1.根据客户群ID调用企业微信【API】获取客户群详情 */
    $query = [
      'chat_id' => $chatId,
      'need_name' => $needName
    ];
    $externalContact = new ExternalContact($this->corpId, $this->corpSecret);
    $result = $externalContact->groupChatGet($query);

    if (false === $result) {
      // 企业微信【API】调用失败
      Log::error('【groupChatGetAPI-调用失败：】' . $externalContact->getError());
      $this->errorMessage = '【groupChatGetAPI-调用失败：】' . $externalContact->getError();
      return false;
    } else {
      return $result;
    }
  }

  public function writeGroupChat($chatId, $isCreate = false) {
    /** 获取客户群详情 */
    $result = $this->getGroupChatInfoById($chatId);
    if (false === $result) {
      return false;
    }

    if (!isset($result['group_chat']) || empty($result['group_chat'])) {
      $this->errorMessage = '获取客户群详情接口返回空';
      return false;
    } else {
      $group_chat = $result['group_chat'];
    }

    if (!isset($group_chat['member_list']) || empty($group_chat['member_list'])) {
      $this->errorMessage = '获取客户群成员列表为空';
      return false;
    } else {
      $member_list = $group_chat['member_list'];
      $group_chat['member_total'] = count($member_list);
      unset($group_chat['member_list']);

      // 清空旧群成员
      model('corpwechat.CorpwechatGroupUser')
        ->where('chat_id', $chatId)
        ->delete();
    }

    // 2.群聊基本信息
    $write_group = model('corpwechat.CorpwechatGroupChat')->write($chatId, $group_chat);
    if (false === $write_group) {
      $this->errorMessage = '客户群基本信息入库失败';
      return false;
    }

    foreach ($member_list as $member) {
      /**
       * 成员类型。
       * 1 - 企业成员
       * 2 - 外部联系人
       */
      switch ($member['type']) {
        case 1:
          // 1-企业成员
          $userInfo = [
            'userid' => $member['userid'],
            'name' => $member['name'],
            'user_type' => $member['type'],
            'register' => 4,
            'unionid' => isset($member['unionid']) ? $member['unionid'] : ''
          ];
          $write_user = model('corpwechat.CorpwechatUserAll')->write($member['userid'], USER_TYPE_STAFF, $userInfo);
          if (false === $write_user) {
            $this->errorMessage = '客户群企业成员信息入库失败';
            return false;
          }
          break;
        case 2:
          // 2-外部联系人
          $userInfo = [
            'userid' => $member['userid'],
            'name' => $member['name'],
            'user_type' => $member['type'],
            'register' => 3,
            'unionid' => isset($member['unionid']) ? $member['unionid'] : ''
          ];
          $write_user = model('corpwechat.CorpwechatUserAll')->write($member['userid'], USER_TYPE_EXTERNAL, $userInfo);
          if (false === $write_user) {
            $this->errorMessage = '客户群外部联系人信息入库失败';
            return false;
          }
          break;
      }

      if ($member['userid'] === $group_chat['owner']) {
        $is_owner = 1;
      } else {
        $is_owner = 0;
      }
      model('corpwechat.CorpwechatGroupUser')
        ->data(
          [
            'chat_id' => $chatId,
            'userid' => $member['userid'],
            'type' => $member['type'],
            'is_owner' => $is_owner,
            'unionid' => isset($member['unionid']) ? $member['unionid'] : '',
            'join_time' => $member['join_time'],
            'join_scene' => $member['join_scene'],
            'invitor' => isset($member['invitor']['userid']) ? $member['invitor']['userid'] : '',
            'nickname' => isset($member['nickname']) ? $member['nickname'] : '',
            'name' => isset($member['name']) ? $member['name'] : ''
          ]
        )
        ->allowField(true)
        ->isUpdate(false)
        ->save();
    }

    if (true === $isCreate) {
      model('corpwechat.CorpwechatGroupLog')->record(
        $chatId,
        count($member_list),
        ADD_MEMBER,
        time()
      );
    }
    return true;
  }
}
