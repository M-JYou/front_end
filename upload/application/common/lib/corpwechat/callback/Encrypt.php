<?php

namespace app\common\lib\corpwechat\callback;

class Encrypt {
  const block_size = 32;
  public $key = null;
  public $iv = null;


  function pkcs7Encode($text) {
    $block_size = self::block_size;
    $text_length = strlen($text);
    //计算需要填充的位数
    $amount_to_pad = $block_size - ($text_length % $block_size);
    if ($amount_to_pad == 0) {
      $amount_to_pad = $block_size;
    }
    //获得补位所用的字符
    $pad_chr = chr($amount_to_pad);
    $tmp = "";
    for ($index = 0; $index < $amount_to_pad; $index++) {
      $tmp .= $pad_chr;
    }
    return $text . $tmp;
  }

  /**
   * 对解密后的明文进行补位删除
   * @param decrypted 解密后的明文
   * @return 删除填充补位后的明文
   */
  function pkcs7Decode($text) {

    $pad = ord(substr($text, -1));
    if ($pad < 1 || $pad > self::block_size) {
      $pad = 0;
    }
    return substr($text, 0, (strlen($text) - $pad));
  }


  /**
   * Prpcrypt constructor.
   * @param $k
   */
  public function __construct($k) {
    $this->key = base64_decode($k . '=');
    $this->iv  = substr($this->key, 0, 16);
  }

  /**
   * 加密
   *
   * @param $text
   * @param $receiveId
   * @return array
   */
  public function encrypt($text, $receiveId) {
    try {
      //拼接
      $text = $this->getRandomStr() . pack('N', strlen($text)) . $text . $receiveId;
      $text        = $this->pkcs7Encode($text);
      //加密
      $encrypted = openssl_encrypt($text, 'AES-256-CBC', $this->key, OPENSSL_ZERO_PADDING, $this->iv);
      return [Error::$OK, $encrypted];
    } catch (\Exception $e) {
      print $e;
      return [Error::$EncryptAESError, null];
    }
  }

  public function extract($xmltext) {
    try {
      $xml = new \DOMDocument();
      $xml->loadXML($xmltext);
      $array_e = $xml->getElementsByTagName('Encrypt');

      $encrypt = $array_e->item(0)->nodeValue;
      return array(0, $encrypt);
    } catch (\Exception $e) {
      return array(Error::$ParseXmlError, null);
    }
  }

  public function generate($encrypt, $signature, $timestamp, $nonce) {
    $format = "<xml><Encrypt><![CDATA[%s]]></Encrypt><MsgSignature><![CDATA[%s]]></MsgSignature><TimeStamp>%s</TimeStamp><Nonce><![CDATA[%s]]></Nonce></xml>";
    return sprintf($format, $encrypt, $signature, $timestamp, $nonce);
  }

  public function decrypt($encrypted, $receiveId) {
    try {
      //解密
      $decrypted = openssl_decrypt($encrypted, 'AES-256-CBC', $this->key, OPENSSL_ZERO_PADDING, $this->iv);
    } catch (\Exception $e) {
      return [Error::$DecryptAESError, null];
    }
    try {
      //删除PKCS#7填充
      $result      = $this->pkcs7Decode($decrypted);
      if (strlen($result) < 16) {
        return [];
      }
      //拆分
      $content     = substr($result, 16, strlen($result));
      $len_list    = unpack('N', substr($content, 0, 4));
      $json_len     = $len_list[1];
      $json_content = substr($content, 4, $json_len);
      $from_receiveId = substr($content, $json_len + 4);
    } catch (\Exception $e) {
      print $e;
      return [Error::$IllegalBuffer, null];
    }
    if ($from_receiveId != $receiveId) {
      return [Error::$ValidateCorpidError, null];
    }
    return [0, $json_content];
  }

  /**
   * 生成随机字符串
   *
   * @return string
   */
  public function getRandomStr() {
    $str     = '';
    $str_pol = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyl';
    $max     = strlen($str_pol) - 1;
    for ($i = 0; $i < 16; $i++) {
      $str .= $str_pol[mt_rand(0, $max)];
    }
    return $str;
  }
}
