<?php


namespace app\common\lib\corpwechat;

use app\common\lib\corpwechat\promise\Corp;

/**
 * Class Callback
 * @package  app\common\lib\corpwechat
 * @author   Administrator
 * @since    2022/2/23
 * @version  1.0
 * @see      (参照)
 */
class Callback extends Corp {
  /**
   * 获取企业微信服务器的ip段
   * @var string
   */
  const GET_CALLBACK_IP = 'https://qyapi.weixin.qq.com/cgi-bin/getcallbackip';

  /**
   * @Method 获取企业微信服务器的ip段
   *
   * @param null
   * @return false|mixed
   *
   * @link https://qyapi.weixin.qq.com/cgi-bin/getcallbackip?access_token=ACCESS_TOKEN
   */
  public function getCallbackIp() {
    return $this->callGet(self::GET_CALLBACK_IP);
  }
}
