<?php

namespace app\common\lib;

/** 微信公众号发送红包类 */
class WechatRed {
  private $sslPath; //API安全证书地址
  public function __construct() {

    $this->sslPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'pay' . DIRECTORY_SEPARATOR . 'wxpay' . DIRECTORY_SEPARATOR . 'cert' . DIRECTORY_SEPARATOR;
  }

  //支付
  public function pay($url, $obj) {
    //创建随机字符串
    $obj['nonce_str'] = $this->createNoncestr();
    //创建签名
    $string = $this->createSign($obj, false);
    $stringSignTemp = $string . "&key=" . config('global_config.payment_wechat_key'); //key为商户平台设置的密钥key
    $sign = strtoupper(md5($stringSignTemp));
    $obj['sign'] = $sign;
    $postXml = $this->arrayToXml($obj);
    $responseXml = $this->curlPostSsl($url, $postXml);
    return $responseXml;
  }


  //生成签名,参数：生成签名的参数和是否编码
  public function createSign($arr, $urlencode) {
    $buff = "";
    ksort($arr); //对传进来的数组参数里面的内容按照字母顺序排序，a在前面，z在最后（字典序）
    foreach ($arr as $k => $v) {
      if (null != $v && "null" != $v && "sign" != $k) {
        //签名不要转码
        if ($urlencode) {
          $v = urlencode($v);
        }
        $buff .= $k . "=" . $v . "&";
      }
    }
    if (strlen($buff) > 0) {
      $reqPar = substr($buff, 0, strlen($buff) - 1); //去掉末尾符号“&”
    }
    return $reqPar;
  }


  //生成随机字符串，默认32位
  public function createNoncestr($length = 32) {
    //创建随机字符
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
  }

  //数组转xml
  public function arrayToXml($arr) {
    $xml = "<xml>";
    foreach ($arr as $k => $v) {
      $xml .= "<" . $k . ">" . $v . "</" . $k . ">";
    }
    $xml .= "</xml>";
    return $xml;
  }

  //post请求网站，需要证书
  public function curlPostSsl($url, $vars, $second = 30, $aHeader = array()) {
    $ch = curl_init();
    //超时时间
    curl_setopt($ch, CURLOPT_TIMEOUT, $second);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //这里设置代理，如果有的话
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    //商户号API安全证书： cert 与 key 分别属于两个.pem文件
    //请确保您的libcurl版本是否支持双向认证，版本高于7.20.1
    curl_setopt($ch, CURLOPT_SSLCERT, $this->sslPath . 'apiclient_cert.pem');
    curl_setopt($ch, CURLOPT_SSLKEY, $this->sslPath . 'apiclient_key.pem');

    if (count($aHeader) >= 1) {
      curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
    }
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
    $data = curl_exec($ch);
    if ($data) {
      curl_close($ch);
      return $data;
    } else {
      $error = curl_errno($ch);
      echo "call faild, errorCode:$error\n";
      curl_close($ch);
      return false;
    }
  }
}
