<?php

namespace app\common\lib\corpwechat\callback;

use app\common\lib\corpwechat\event\ChangeContactEvent;
use app\common\lib\corpwechat\event\ChangeExternalChatEvent;
use app\common\lib\corpwechat\event\ChangeExternalContactEvent;
use app\common\lib\corpwechat\event\ChangeExternalTagEvent;
use app\common\lib\corpwechat\event\Channel;
use app\common\lib\corpwechat\event\WelcomeMsg;
use think\Exception;
use think\Log;

class DecryMsg {
  public static function event($decryMsg) {
    Log::init([
      'type' => 'File',
      'path' => LOG_PATH . 'corpwechat_event/'
    ]);

    if (isset($decryMsg['Event']) && !empty($decryMsg['Event'])) {
      $event = $decryMsg['Event'];
    } else {
      return;
    }
    if (isset($decryMsg['ChangeType']) && !empty($decryMsg['ChangeType'])) {
      $changeType = $decryMsg['ChangeType'];
    } else {
      return;
    }

    switch ($event) {
        // 客户联系事件
      case 'change_external_contact':
        $changeExternalContactEvent = new ChangeExternalContactEvent();
        switch ($changeType) {
          case 'add_external_contact': // 添加企业客户事件
          case 'add_half_external_contact': // 外部联系人免验证添加成员事件
            cache('add_contact_' . time() . '_' . randstr(6, false), $decryMsg);
            /** 添加企微客户 */
            try {
              $result = $changeExternalContactEvent->addExternalContact($decryMsg);
              if (false === $result) {
                Log::error("{$event}-{$changeType}" . $changeExternalContactEvent->getError());
                return;
              }
            } catch (Exception $e) {
              Log::error("{$event}-{$changeType}-[Exception-DecryMsg]" . serialize($decryMsg));
              Log::error("{$event}-{$changeType}-[Exception]" . $e->getMessage());
              return;
            }
            /** 发送欢迎语 */
            try {
              $channel = new Channel();
              $channel_result = $channel->stateChannel($decryMsg);
              if (false === $channel_result) {
                Log::error("{$event}-{$changeType}" . $changeExternalContactEvent->getError());
                return;
              }
            } catch (Exception $e) {
              Log::error("{$event}-{$changeType}-[Exception-DecryMsg]" . serialize($decryMsg));
              Log::error("{$event}-{$changeType}-[Exception]" . $e->getMessage());
              return;
            }

            break;

          case 'edit_external_contact': // 编辑企业客户事件
            try {
              $result = $changeExternalContactEvent->editExternalContact($decryMsg);
              if (false === $result) {
                Log::error("{$event}-{$changeType}" . $changeExternalContactEvent->getError());
                return;
              }
            } catch (Exception $e) {
              Log::error("{$event}-{$changeType}-[Exception-DecryMsg]" . serialize($decryMsg));
              Log::error("{$event}-{$changeType}-[Exception]" . $e->getMessage());
              return;
            }
            break;

          case 'del_external_contact': // 删除企业客户事件
          case 'del_follow_user': // 删除跟进成员事件
            try {
              $result = $changeExternalContactEvent->delExternalContact($decryMsg);
              if (false === $result) {
                Log::error("{$event}-{$changeType}" . $changeExternalContactEvent->getError());
                return;
              }
            } catch (Exception $e) {
              Log::error("{$event}-{$changeType}-[Exception-DecryMsg]" . serialize($decryMsg));
              Log::error("{$event}-{$changeType}-[Exception]" . $e->getMessage());
              return;
            }
            break;

          default:
            Log::error("{$event}:错误的`ChangeType`类型={$changeType}");
            break;
        }
        break;

        // 通讯轮回调通知
      case 'change_contact':
        // 通讯录回调通知
        $changeContactEvent = new ChangeContactEvent();
        switch ($changeType) {
          case 'create_user':
            // 新增成员事件
            $changeContactEvent->createUser($decryMsg);
            break;

          case 'update_user':
            // 更新成员事件
            $changeContactEvent->updateUser($decryMsg);
            break;

          case 'delete_user':
            //删除成员事件
            $changeContactEvent->deleteUser($decryMsg);
            break;

          default:
            Log::error("{$event}:错误的`ChangeType`类型={$changeType}");
            break;
        }
        break;

        // 客户群事件
      case 'change_external_chat':
        $changeExternalChatEvent = new ChangeExternalChatEvent();
        switch ($changeType) {
          case 'create':
            // 客户群创建事件
            try {
              $result = $changeExternalChatEvent->createChat($decryMsg);;
              if (false === $result) {
                Log::error("{$event}-{$changeType}" . $changeExternalChatEvent->getError());
                return;
              }
            } catch (Exception $e) {
              Log::error("{$event}-{$changeType}-[Exception-DecryMsg]" . serialize($decryMsg));
              Log::error("{$event}-{$changeType}-[Exception]" . $e->getMessage());
              return;
            }
            break;

          case 'update':
            // 客户群变更事件
            cache('chart_update_' . time() . '_' . randstr(6, false), $decryMsg);
            try {
              $result = $changeExternalChatEvent->updateChat($decryMsg);;
              if (false === $result) {
                Log::error("{$event}-{$changeType}" . $changeExternalChatEvent->getError());
                return;
              }
            } catch (Exception $e) {
              Log::error("{$event}-{$changeType}-[Exception-DecryMsg]" . serialize($decryMsg));
              Log::error("{$event}-{$changeType}-[Exception]" . $e->getMessage());
              return;
            }
            break;

          case 'dismiss':
            // 客户群解散事件
            try {
              $result = $changeExternalChatEvent->dismissChat($decryMsg);;
              if (false === $result) {
                Log::error("{$event}-{$changeType}" . $changeExternalChatEvent->getError());
                return;
              }
            } catch (Exception $e) {
              Log::error("{$event}-{$changeType}-[Exception-DecryMsg]" . serialize($decryMsg));
              Log::error("{$event}-{$changeType}-[Exception]" . $e->getMessage());
              return;
            }
            break;

          default:
            Log::error("{$event}:错误的`ChangeType`类型={$changeType}");
            break;
        }
        break;

        // 企业客户标签事件
      case 'change_external_tag':
        $changeExternalTagEvent = new ChangeExternalTagEvent();
        $changeExternalTagEvent->synchronization();
        break;

      default:
        Log::error("错误的事件类型={$event}");
        break;
    }
  }
}
