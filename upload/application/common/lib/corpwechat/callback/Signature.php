<?php
namespace app\common\lib\corpwechat\callback;

class Signature
{
	public function getSHA1($token, $timestamp, $nonce, $encrypt_msg)
	{
		//排序
		try {
			$array = array($encrypt_msg, $token, $timestamp, $nonce);
			sort($array, SORT_STRING);
			$str = implode($array);
			return array(Error::$OK, sha1($str));
		} catch (\Exception $e) {
			return array(Error::$ComputeSignatureError, null);
		}
	}
}
