<?php

namespace app\common\lib\corpwechat\callback;

class WXBizMsgCrypt {
  private $m_sToken;
  private $m_sEncodingAesKey;
  private $m_sReceiveId;

  public function __construct($token, $encodingAesKey, $receiveId) {
    $this->m_sToken = $token;
    $this->m_sEncodingAesKey = $encodingAesKey;
    $this->m_sReceiveId = $receiveId;
  }

  public function VerifyURL($sMsgSignature, $sTimeStamp, $sNonce, $sEchoStr, &$echoStr) {
    if (strlen($this->m_sEncodingAesKey) != 43) {
      return Error::$IllegalAesKey;
    }

    $encrypt = new Encrypt($this->m_sEncodingAesKey);
    $sign = new Signature();
    $me = $sign->getSHA1($this->m_sToken, $sTimeStamp, $sNonce, $sEchoStr);
    $ret = $me[0];

    if ($ret != 0) {
      return $ret;
    }

    $signature = $me[1];
    if ($signature != $sMsgSignature) {
      return Error::$ValidateSignatureError;
    }

    $result = $encrypt->decrypt($sEchoStr, $this->m_sReceiveId);
    if ($result[0] != 0) {
      return $result[0];
    }
    $echoStr = $result[1];
    return Error::$OK;
  }

  public function EncryptMsg($sReplyMsg, &$sEncryptMsg) {
    $pc = new Encrypt($this->m_sEncodingAesKey);

    $array = $pc->encrypt($sReplyMsg, $this->m_sReceiveId);
    $ret = $array[0];
    if ($ret != 0) {
      return $ret;
    }

    $sTimeStamp = time();
    $sNonce = $pc->getRandomStr();
    $encrypt = $array[1];

    //生成安全签名
    $sha1 = new Signature;

    $array = $sha1->getSHA1($this->m_sToken, $sTimeStamp, $sNonce, $encrypt);
    $ret = $array[0];
    if ($ret != 0) {
      return $ret;
    }
    $signature = $array[1];

    $sEncryptMsg = $pc->generate($encrypt, $signature, $sTimeStamp, $sNonce);
    return Error::$OK;
  }


  public function DecryptMsg($sMsgSignature, $sTimeStamp, $sNonce, $sPostData, &$sMsg) {
    if (strlen($this->m_sEncodingAesKey) != 43) {
      return Error::$IllegalAesKey;
    }
    $pc = new Encrypt($this->m_sEncodingAesKey);
    $array = $pc->extract($sPostData);
    $ret = $array[0];

    if ($ret != 0) {
      return $ret;
    }

    //		if ($sTimeStamp == null) {
    //			$sTimeStamp = time();
    //		}

    $encrypt = $array[1];

    //验证安全签名
    $sha1 = new Signature;
    $array = $sha1->getSHA1($this->m_sToken, $sTimeStamp, $sNonce, $encrypt);
    $ret = $array[0];

    if ($ret != 0) {
      return $ret;
    }

    $signature = $array[1];
    if ($signature != $sMsgSignature) {
      //print("signature not match, signature ".$signature.", sMsgSignature ".$sMsgSignature."\n");
      return Error::$ValidateSignatureError;
    }


    $result = $pc->decrypt($encrypt, $this->m_sReceiveId);
    if ($result[0] != 0) {
      return $result[0];
    }
    $sMsg = $result[1];

    return Error::$OK;
  }
}
