<?php

namespace app\common\lib\corpwechat;

use app\common\lib\corpwechat\promise\Corp;

class Oauth extends Corp {
  /**
   * 构造独立窗口登录二维码
   * @var string
   */
  const QRCONNECT = 'https://open.work.weixin.qq.com/wwopen/sso/qrConnect';

  /**
   * 获取访问用户身份
   * @var string
   */
  const GETUSERINFO = 'https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo';


  /**
   * @Purpose:
   * 构造独立窗口登录二维码
   * @Method qrConnect()
   *
   * @param string $redirect_url 重定向地址
   * @param string $aging_id 应用ID
   * @param string $state
   *
   * @return string
   *
   * @throws null
   *
   * @link https://open.work.weixin.qq.com/wwopen/sso/qrConnect?appid=CORPID&agentid=AGENTID&redirect_uri=REDIRECT_URI&state=STATE
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/2/28
   */
  public function qrConnect($redirect_url, $aging_id, $state = 'STATE') {
    $redirect_uri = urlencode($redirect_url);
    return self::QRCONNECT . "?appid={$this->corpId}&agentid={$aging_id}&redirect_uri={$redirect_uri}&state={$state}";
  }


  /**
   * @Purpose:
   * 获取访问用户身份
   * @Method userInfo()
   *
   * @param $code
   *
   * @return false|mixed
   *
   * @throws null
   *
   * @link https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?access_token=ACCESS_TOKEN&code=CODE
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/2/28
   */
  public function userInfo($code) {
    return $this->callGet(self::GETUSERINFO, ['code' => $code]);
  }
}
