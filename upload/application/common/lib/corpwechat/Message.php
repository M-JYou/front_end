<?php

namespace app\common\lib\corpwechat;

use app\common\lib\corpwechat\promise\Corp;

class Message extends Corp {
  const MESSAGE_SEND = 'https://qyapi.weixin.qq.com/cgi-bin/message/send';
  const MEDIA_GET = 'https://qyapi.weixin.qq.com/cgi-bin/media/get';

  public function send($message) {
    return $this->callPost(self::MESSAGE_SEND, $message);
  }
}
