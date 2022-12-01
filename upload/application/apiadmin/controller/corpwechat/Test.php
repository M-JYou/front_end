<?php

namespace app\apiadmin\controller\corpwechat;

use app\common\controller\Backend;
use app\common\lib\corpwechat\callback\Chat;
use app\common\lib\corpwechat\callback\DecryMsg;
use app\common\lib\corpwechat\event\ChangeContactEvent;
use app\common\lib\corpwechat\event\ChangeExternalChatEvent;
use app\common\lib\corpwechat\event\ChangeExternalContactEvent;
use \app\common\lib\corpwechat\callback\Channel;
use app\common\lib\corpwechat\callback\CorpTag;
use app\common\lib\corpwechat\event\ChangeExternalTagEvent;

class Test extends Backend {

  public function test() {

    $list = model('corpwechat.CorpwechatUserAll')
      ->where('avatar', '<>', '')
      ->field('id,avatar')
      ->select();

    foreach ($list as $one) {
      $avatar = $one['avatar'];
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

      model('corpwechat.CorpwechatUserAll')
        ->where('id', $one['id'])
        ->update(
          ['thumb_avatar' => $thumb_avatar]
        );
    }

    echo 1;
    die();
    // $class = new ChangeContactEvent();    // 通讯录回调通知
    // $decryMsg = [
    // 'UserID' => 'cba31df612344048a3ff6dff4db9e6a7'
    // ];

    /** 企业客户回调通知 */
    // $class = new ChangeExternalContactEvent();
    // $decryMsg = 'a:9:{s:10:"ToUserName";s:18:"ww9043b8cbdd401154";s:12:"FromUserName";s:3:"sys";s:10:"CreateTime";s:10:"1648436674";s:7:"MsgType";s:5:"event";s:5:"Event";s:23:"change_external_contact";s:10:"ChangeType";s:20:"add_external_contact";s:6:"UserID";s:8:"LiangHui";s:14:"ExternalUserID";s:32:"wmQZBhCgAAw-jq1Ch0NKi5wHp3udA4fw";s:11:"WelcomeCode";s:43:"LPsc7nHleb-mO3ZFhPkMlwc-1-a7ZHgEEp1LRi1UWRc";}';
    // $decryMsg = unserialize($decryMsg);
    // $class->addExternalContact($decryMsg);
    // die();

    /** 企业客户回调通知 */
    // $class = new ChangeExternalContactEvent();
    // $decryMsg = 'a:8:{s:10:"ToUserName";s:18:"ww9043b8cbdd401154";s:12:"FromUserName";s:3:"sys";s:10:"CreateTime";s:10:"1648013684";s:7:"MsgType";s:5:"event";s:5:"Event";s:23:"change_external_contact";s:10:"ChangeType";s:21:"edit_external_contact";s:6:"UserID";s:8:"LiangHui";s:14:"ExternalUserID";s:32:"wmQZBhCgAABPII8mnq-v2_q6BB3x1f_g";}';
    // $decryMsg = unserialize($decryMsg);
    // $class->editExternalContact($decryMsg);
    // die();

    // $changeContactEvent = new ChangeContactEvent();
    // $decryMsg = 'a:9:{s:10:"ToUserName";s:18:"ww9043b8cbdd401154";s:12:"FromUserName";s:3:"sys";s:10:"CreateTime";s:10:"1647590859";s:7:"MsgType";s:5:"event";s:5:"Event";s:14:"change_contact";s:10:"ChangeType";s:11:"update_user";s:6:"UserID";s:32:"04bf6589587b7a9976a5661d88f441b3";s:6:"Avatar";s:89:"http://wework.qpic.cn/bizmail/dkInyVffOomtL7QSLiaKm7VpUC4f7iaoCTqiaQ1sJkk09yqE8qBEU4dTQ/0";s:9:"NewUserID";s:10:"wangyaoxin";}';
    // $decryMsg = unserialize($decryMsg);
    // $changeContactEvent->updateUser($decryMsg);

    // $changeContactEvent = new ChangeContactEvent();
    // $decryMsg = 'a:18:{s:10:"ToUserName";s:18:"ww9043b8cbdd401154";s:12:"FromUserName";s:3:"sys";s:10:"CreateTime";s:10:"1647596266";s:7:"MsgType";s:5:"event";s:5:"Event";s:14:"change_contact";s:10:"ChangeType";s:11:"create_user";s:6:"UserID";s:7:"test001";s:4:"Name";s:9:"测试001";s:6:"Mobile";s:11:"15235140842";s:6:"Gender";s:1:"1";s:6:"Avatar";s:94:"https://rescdn.qqmail.com/node/wwmng/wwmng/style/images/independent/DefaultAvatar$73ba92b5.png";s:10:"Department";s:1:"2";s:6:"Status";s:1:"4";s:8:"IsLeader";s:1:"0";s:5:"Alias";s:9:"测试001";s:14:"IsLeaderInDept";s:1:"0";s:14:"MainDepartment";s:1:"2";s:7:"BizMail";s:24:"test001@rcxt1.wecom.work";}';
    // $decryMsg = unserialize($decryMsg);
    // $changeContactEvent->createUser($decryMsg);

    /** 渠道活码【State】 */
    // $decryMsg = 'a:10:{s:10:"ToUserName";s:18:"ww9043b8cbdd401154";s:12:"FromUserName";s:3:"sys";s:10:"CreateTime";s:10:"1647498075";s:7:"MsgType";s:5:"event";s:5:"Event";s:23:"change_external_contact";s:10:"ChangeType";s:20:"add_external_contact";s:6:"UserID";s:32:"04bf6589587b7a9976a5661d88f441b3";s:14:"ExternalUserID";s:32:"wmQZBhCgAA87NrWR5jC2t3TM7grximkA";s:5:"State";s:30:"ab7abe9a03841062ff564b179fd578";s:11:"WelcomeCode";s:43:"hbtgW8ls9wOqsnWymsQe7s9JsB36wTOoobNwAS0wF10";}';
    // $decryMsg = unserialize($decryMsg);
    // $decryMsg['NickName'] = '1111';
    // $channel = new \app\common\lib\corpwechat\event\Channel();
    // $channel->stateChannel($decryMsg);


    /** 同步企微客户标签 */
    // $changeExternalTagEvent = new ChangeExternalTagEvent();
    // $changeExternalTagEvent->synchronization();

    /** 群聊事件 */
    // 更新
    $decryMsg = 'a:10:{s:10:"ToUserName";s:18:"ww9043b8cbdd401154";s:12:"FromUserName";s:3:"sys";s:10:"CreateTime";s:10:"1648458575";s:7:"MsgType";s:5:"event";s:5:"Event";s:20:"change_external_chat";s:6:"ChatId";s:32:"wrQZBhCgAA6rFGKQu3osnYwomDJLlgzg";s:10:"ChangeType";s:6:"update";s:12:"UpdateDetail";s:10:"add_member";s:9:"JoinScene";s:1:"0";s:12:"MemChangeCnt";s:1:"2";}';
    // 创建
    // $decryMsg = 'a:7:{s:10:"ToUserName";s:18:"ww9043b8cbdd401154";s:12:"FromUserName";s:3:"sys";s:10:"CreateTime";s:10:"1647830431";s:7:"MsgType";s:5:"event";s:5:"Event";s:20:"change_external_chat";s:6:"ChatId";s:32:"wrQZBhCgAAyPENsaRh1s-fPLr1EkhQVw";s:10:"ChangeType";s:6:"create";}';
    // 解散
    // $decryMsg = 'a:7:{s:10:"ToUserName";s:18:"ww9043b8cbdd401154";s:12:"FromUserName";s:3:"sys";s:10:"CreateTime";s:10:"1647853463";s:7:"MsgType";s:5:"event";s:5:"Event";s:20:"change_external_chat";s:6:"ChatId";s:32:"wrQZBhCgAAKTGgwR6L9XGlzLj_rzFwEg";s:10:"ChangeType";s:7:"dismiss";}';
    $decryMsg = unserialize($decryMsg);
    $changeExternalContactEvent = new ChangeExternalChatEvent();
    $changeExternalContactEvent->updateChat($decryMsg);
  }
}
