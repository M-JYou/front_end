<?php

/** 微信相关 */

namespace app\common\lib;

class WechatMiniprogram {
  protected $error;
  /*
        pwd_hash
        获取access_token
    */
  public function getAccessToken($reset = false) {
    $access_token = cache('wechat_miniprogram_access_token');
    if ($access_token && !$reset) return $access_token;
    $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . config('global_config.wechat_miniprogram_appid') . "&secret=" . config('global_config.wechat_miniprogram_appsecret');
    $result = self::https_request($url);
    $jsoninfo = json_decode($result, true);
    if (isset($jsoninfo['errcode']) && $jsoninfo['errcode'] !== 0) {
      $this->error = $jsoninfo['errmsg'];
      return false;
    }
    $access_token = $jsoninfo["access_token"];
    //更新数据
    cache('wechat_miniprogram_access_token', $access_token, 7200);
    return $access_token;
  }
  /** [生成微信小程序带参数二维码] */
  public function makeQrcode($page, $params = array(), $filename = '', $output = true) {
    if ($filename == '') {
      $file_dir_name = 'tmp/' . date('Y/m/d/');
      $file_dir = RUNTIME_PATH . $file_dir_name;
      $filename = date('YmdHis') . rand(1, 1000) * rand(1, 1000) . '.png';
    } else {
      $file_dir_name = 'files/' . date('Ymd/');
      $file_dir = SYS_UPLOAD_PATH . $file_dir_name;
    }
    $file_path = $file_dir . $filename;
    if (file_exists($file_path)) {
      if ($output === true) {
        return $this->fileToBase64($file_path);
      } else {
        return $file_path;
      }
    }

    $access_token = self::getAccessToken();
    $url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=" . $access_token;
    $scene = is_array($params) ? ('id=' . $params['id']) : $params;
    $post_data = [
      "scene" => $scene,
      "page" => $page,
    ];
    $result = self::https_request($url, $post_data);
    $result_arr = json_decode($result, true);
    if (!isset($result_arr['errcode'])) {
      if (!is_dir($file_dir)) {
        mkdir($file_dir, 0755, true);
      }
      file_put_contents($file_path, $result);
      if ($output === true) {
        $img_string = $this->fileToBase64($file_path);
        return $img_string;
      } else {
        return $file_dir_name . $filename;
      }
    }
    return $result_arr;
  }
  /** 本地文件转base64 */
  private function fileToBase64($file) {
    $base64_file = '';
    if (file_exists($file)) {
      $base64_data = base64_encode(file_get_contents($file));
      $base64_file = 'data:image/png;base64,' . $base64_data;
    }
    return $base64_file;
  }
  /** 解密开放数据 */
  public function decryptOpenData($sessionKey, $encryptedData, $iv) {
    require EXTEND_PATH . 'wechatMiniprogram/wxBizDataCrypt.php';
    $appid = config('global_config.wechat_miniprogram_appid');
    $pc = new \WXBizDataCrypt($appid, $sessionKey);
    $errCode = $pc->decryptData($encryptedData, $iv, $data);
    if ($errCode == 0) {
      return $data;
    } else {
      $this->error = $errCode;
      return false;
    }
  }
  /** 调用微信code2Session接口获取openid和unionid */
  public function code2Session($code) {
    $appid = config('global_config.wechat_miniprogram_appid');
    $secret = config('global_config.wechat_miniprogram_appsecret');
    $url = "https://api.weixin.qq.com/sns/jscode2session?appid=" . $appid . "&secret=" . $secret . "&js_code=" . $code . "&grant_type=authorization_code";
    $result = self::https_request($url);
    $jsoninfo = json_decode($result, true);
    if (isset($jsoninfo['errcode']) && $jsoninfo['errcode'] !== 0) {
      $this->error = $jsoninfo['errmsg'];
      return false;
    }
    return $jsoninfo;
  }
  public function https_request($url, $data = null) {
    $http = new \app\common\lib\Http();
    if ($data === null) {
      $result = $http->get($url);
    } else {
      $result = $http->post($url, $data);
    }
    return $result;
  }

  /** 错误 */
  public function getError() {
    return $this->error;
  }
}
