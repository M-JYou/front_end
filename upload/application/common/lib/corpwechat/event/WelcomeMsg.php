<?php

/** 欢迎语 */

namespace app\common\lib\corpwechat\event;

use app\common\lib\corpwechat\ExternalContact;
use Think\Log;

/** 欢迎语附件类型[1:text;2:image;3:link;] */
define('TEXT_TYPE', 1); // 纯文本-附件类型-text
define('IMAGE_TYPE', 2); // 图片-附件类型-image
define('LINK_TYPE', 3); // 链接-附件类型-link

class WelcomeMsg extends Event {
  /**
   * @Purpose:
   * 发送渠道欢迎语
   * @Method sendChannelWelcomeMsg()
   *
   * @param array $decryMsg
   * @param array $channelInfo
   *
   * @throws null
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/11
   */
  public function sendChannelWelcomeMsg($decryMsg, $channelInfo) {
    if (false === strpos($channelInfo->text_content, '{用户昵称}')) {
      $content = $channelInfo->text_content;
    } else {
      $nickName = $this->getUserNameByUserId($decryMsg['ExternalUserID']);
      $content = str_replace("{用户昵称}", $nickName, $channelInfo->text_content);
    }

    $send = [
      'welcome_code' => $decryMsg['WelcomeCode'],
      'text' => [
        'content' => $content
      ],
    ];

    switch ($channelInfo->type) {
      case TEXT_TYPE: // 纯文本
        break;

      case IMAGE_TYPE: // 图片
        $send['attachments'][] = [
          'msgtype' => 'image',
          'image' => [
            'pic_url' => $channelInfo->pic_url
          ]
        ];
        break;

      case LINK_TYPE: // 链接
        $send['attachments'][] = [
          'msgtype' => 'link',
          'link' => [
            "title" => isset($channelInfo->link_title) ? $channelInfo->link_title : '',
            "picurl" => isset($channelInfo->link_picurl) ? $channelInfo->link_picurl : '',
            "desc" => isset($channelInfo->link_desc) ? $channelInfo->link_desc : '',
            "url" => isset($channelInfo->link_url) ? $channelInfo->link_url : ''
          ]
        ];
        break;

      default:
        break;
    }

    $externalContact = new ExternalContact($this->corpId, $this->corpSecret);
    $result = $externalContact->sendWelcomeMsg($send);
    if ($result === false) {
      Log::error('企业微信【发送欢迎语API】调用失败，' . $externalContact->getError());
    }
  }


  /**
   * @Purpose:
   * 发送默认欢迎语
   * @Method sendUserWelcomeMsg()
   *
   * @param $decryMsg
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/14
   */
  public function sendUserWelcomeMsg($decryMsg) {
    $userID = $decryMsg['UserID'];

    $welcome_id = model('corpwechat.CorpwechatStaff')
      ->where('userid', $userID)
      ->value('welcome_id');

    if (!isset($welcome_id) || empty($welcome_id)) {
      return;
    }

    $welcome_words = model('corpwechat.CorpwechatWelcomeWords')
      ->find($welcome_id);

    if (!isset($welcome_words) || empty($welcome_words)) {
      return;
    } else {
      $welcome_words = $welcome_words->toArray();
    }


    if (false === strpos($welcome_words['text_content'], '{用户昵称}')) {
      $content = $welcome_words['text_content'];
    } else {
      $nickName = $this->getUserNameByUserId($decryMsg['ExternalUserID']);
      $content = str_replace("{用户昵称}", $nickName, $welcome_words['text_content']);
    }

    $send = [
      'welcome_code' => $decryMsg['WelcomeCode'],
      'text' => ['content' => $content],
    ];

    switch ($welcome_words['type']) {
      case TEXT_TYPE: // 纯文本
        break;

      case IMAGE_TYPE: // 图片
        $send['attachments'][] = [
          'msgtype' => 'image',
          'image' => [
            'pic_url' => $welcome_words['pic_url']
          ]
        ];
        break;

      case LINK_TYPE: // 链接
        $send['attachments'][] = [
          'msgtype' => 'link',
          'link' => [
            "title" => isset($welcome_words['link_title']) ? $welcome_words['link_title'] : '',
            "picurl" => isset($welcome_words['link_picurl']) ? $welcome_words['link_picurl'] : '',
            "desc" => isset($welcome_words['link_desc']) ? $welcome_words['link_desc'] : '',
            "url" => isset($welcome_words['link_url']) ? $welcome_words['link_url'] : ''
          ]
        ];
        break;

      default:
        return;
    }

    $externalContact = new ExternalContact($this->corpId, $this->corpSecret);
    $result = $externalContact->sendWelcomeMsg($send);
    if ($result === false) {
      Log::error('企业微信【发送欢迎语API】调用失败，' . $externalContact->getError());
    }
  }
}
