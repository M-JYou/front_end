<?php

/** 渠道活码 */

namespace app\common\lib\corpwechat\event;

use app\common\lib\corpwechat\ExternalContact;
use think\Exception;

/** 欢迎语类型[1:渠道欢迎语;2:默认欢迎语;3:不使用欢迎语;] */
define('CHANNEL_WORDS', 1); // 1:渠道欢迎语
define('DEFAULT_WORDS', 2); // 2:默认欢迎语
define('NO_WORDS', 3); // 3:不使用欢迎语
/** 是否打渠道标签[1:是;0:否] */
define('HAVE_TAG', 1); // 1:打渠道标签
define('NO_TAG', 0); // 0:不打渠道标签

class Channel extends Event {
  public function stateChannel($decryMsg) {
    $welcomeMsg = new WelcomeMsg();
    /**
     * 1.判断是否有【State】参数
     * 1.）有，进入渠道活码业务线
     * 2.）跳过，进入发送欢迎语业务线
     */
    if (isset($decryMsg['State']) && !empty($decryMsg['State'])) {
      /** 2.判断渠道活码信息 */
      $state = $decryMsg['State'];
      $channel_info = model('corpwechat.CorpwechatChannel')
        ->where('state', $state)
        ->find();
      if (null === $channel_info) {
        $this->errorMessage = '渠道活码信息异常-【State：' . $state . '】';
        return false;
      }

      try {
        $update_success_result = model('corpwechat.corpwechatChannel')
          ->where('state', $decryMsg['State'])
          ->setInc('scan_num');
        if (false === $update_success_result) {
          throw new Exception(model('corpwechat.corpwechatChannel')->getError());
        }

        $is_day_log = model('corpwechat.CorpwechatChannelDayLog')
          ->where('state', $decryMsg['State'])
          ->whereTime('create_time', 'today')
          ->find();
        if (null === $is_day_log) {
          $day_log_result = model('corpwechat.CorpwechatChannelDayLog')
            ->data([
              'state' => $decryMsg['State'],
              'add_total' => 1
            ])
            ->allowField(true)
            ->isUpdate(false)
            ->save();
        } else {
          $day_log_result = model('corpwechat.CorpwechatChannelDayLog')
            ->where('state', $decryMsg['State'])
            ->whereTime('create_time', 'today')
            ->setInc('add_total');
        }
        if (false === $day_log_result) {
          throw new Exception(model('corpwechat.CorpwechatChannelDayLog')->getError());
        }

        $channel_log_result = model('corpwechat.corpwechatChannelLog')
          ->data(
            [
              'external_user_id' => $decryMsg['ExternalUserID'],
              'userid' => $decryMsg['UserID'],
              'state' => $decryMsg['State'],
              'type' => 1,
              'content' => '扫渠道活码添加好友'
            ]
          )
          ->allowField(true)
          ->isUpdate(false)
          ->save();
        if (false === $channel_log_result) {
          throw new Exception(model('corpwechat.corpwechatChannelLog')->getError());
        }
      } catch (Exception $e) {
        $this->errorMessage = $e->getMessage();
        return false;
      }

      /** 判断欢迎语类型 */
      switch ($channel_info->welcome_type) {
        case CHANNEL_WORDS:
          /** 1.1发送渠道欢迎语 */
          $welcomeMsg->sendChannelWelcomeMsg($decryMsg, $channel_info);
          break;

        case DEFAULT_WORDS:
          /** 1.2默认欢迎语 */
          $welcomeMsg->sendUserWelcomeMsg($decryMsg);
          break;

        case NO_WORDS:
          /** 1.3不发送欢迎语 */
          break;

        default:
          break;
      }

      /** 是否打渠道标签[1:是;0:否] */
      switch ($channel_info->is_tag) {
          /** 打渠道标签 */
        case HAVE_TAG:
          $this->addChannelTag($decryMsg, $channel_info);
          break;

          /** 不打渠道标签 */
        case NO_TAG:
        default:
          break;
      }
    } else {
      if (isset($decryMsg['WelcomeCode']) && !empty($decryMsg['WelcomeCode'])) {
        $welcomeMsg->sendUserWelcomeMsg($decryMsg);
      } else {
        $this->errorMessage = '缺少【WelcomeCode】参数';
        return false;
      }
    }

    return true;
  }

  /**
   * @Purpose:
   * 打渠道标签
   * @Method addChannelTag()
   *
   * @param $decryMsg
   * @param $channelInfo
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/14
   */
  public function addChannelTag($decryMsg, $channelInfo) {
    $channel_tag = $channelInfo->channel_tag;

    if (!is_array($channel_tag) || count($channel_tag) < 1) {
      return;
    }

    $add_tag = [];
    $tags = [];
    $tag_group = [];
    foreach ($channel_tag as $tag_name => $tag_id) {
      $tag_info = model('corpwechat.CorpwechatCorptag')
        ->field('name,group_name,group_id')
        ->find($tag_id);

      if (null === $tag_info) {
        continue;
      }

      $add_tag[] = $tag_id;
      $tags[$tag_name] = $tag_id;
      $tag_group[$tag_info['group_name']] = $tag_info['group_id'];
    }

    $mark_tag = [
      'userid' => $decryMsg['UserID'],
      'external_userid' => $decryMsg['ExternalUserID'],
      'add_tag' => $add_tag
    ];

    /** 调用企业微信API */
    $externalContact = new ExternalContact($this->corpId, $this->corpSecret);
    $result = $externalContact->markTag($mark_tag);
    if (false === $result) {
      cache('state_mar_tag_error', $externalContact->getError());
      return;
    }

    model('corpwechat.CorpwechatExternalUser')
      ->allowField(true)
      ->isUpdate(true)
      ->save(
        [
          'tags' => $tags,
          'tag_group' => $tag_group
        ],
        [
          'userid' => $decryMsg['UserID'],
          'external_user_id' => $decryMsg['ExternalUserID']
        ]
      );
  }
}
