<?php

namespace app\common\lib\corpwechat\promise;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use think\Cache;

class Corp {
  public $http;
  public $corpId;
  public $corpSecret;
  public $type;
  public $errorCode;
  public $errorMessage;

  public function __construct($corpId = null, $secret = null, $type = 0) {
    $this->http = new Client();
    $this->corpId = $corpId;
    $this->corpSecret = $secret;
    $this->type = $type;
  }

  public function accessToken() {
    if ($this->type === 'check') {
      $this->delAccessToken();
    }
    $token = Cache::get('corp_wechat_access_token_' . $this->type);
    if (!$token) {
      $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken";

      try {
        $response = $this->http->request(
          'GET',
          $url,
          ['query' => ['corpid' => $this->corpId, 'corpsecret' => $this->corpSecret]]
        );
        $res = $response->getBody()->getContents();
      } catch (ClientException $e) {
        $this->errorCode = 500;
        $this->errorMessage = $e->getMessage();
        return false;
      }

      $data = json_decode($res, true);
      if ($data['errcode'] === 0) {
        // Cache::set('CORP_TOKEN' . $this->corpId . $this->type, $data['access_token'], 7200);
        Cache::set('corp_wechat_access_token_' . $this->type, $data['access_token'], 7200);
        $token = $data['access_token'];
      } else {
        $this->errorCode = $data['errcode'] ? $data['errcode'] : '44444';
        $this->errorMessage = $data['errmsg'] ? $data['errmsg'] : '错误信息不详';
        return false;
      }
    }

    return $token;
  }

  public function delAccessToken() {
    Cache::rm('corp_wechat_access_token');
  }

  public function callPost($url, $data = []) {
    try {
      $response = $this->http->request(
        'POST',
        $url . '?access_token=' . $this->accessToken(),
        ['json' => $data]
      );
      $res = $response->getBody()->getContents();
    } catch (ClientException $e) {
      $this->errorCode = 500;
      $this->errorMessage = $e->getMessage();
      return false;
    }

    $data = json_decode($res, true);

    if (!isset($data['errcode']) && !isset($data['errmsg'])) {
      $this->errorCode = 'error';
      $this->errorMessage = '企业微信API调用失败';
      return false;
    }

    if (0 !== $data['errcode']) {
      $this->errorCode = $data['errcode'];
      $this->errorMessage = $data['errmsg'];
      return false;
    } else {
      unset($data['errcode']);
      unset($data['errmsg']);
      return $data;
    }
  }

  public function callPostMore($url, $data = []) {
    try {
      $response = $this->http->request(
        'POST',
        $url . '?access_token=' . $this->accessToken(),
        ['json' => $data]
      );
      $res = $response->getBody()->getContents();
    } catch (ClientException $e) {
      $this->errorCode = 500;
      $this->errorMessage = $e->getMessage();
      return false;
    }

    $data = json_decode($res, true);

    if (!isset($data['errcode']) && !isset($data['errmsg'])) {
      $this->errorCode = 'error';
      $this->errorMessage = '企业微信API调用失败';
      return false;
    }

    if (0 !== $data['errcode']) {
      $this->errorCode = $data['errcode'];
      $this->errorMessage = $data['errmsg'];
      return false;
    } else {
      unset($data['errcode']);
      unset($data['errmsg']);
      return $data;
    }
  }

  public function callGet($url, $data = []) {
    try {
      $data['access_token'] = $this->accessToken();
      $data['debug'] = 1;
      $response = $this->http->request('GET', $url, ['query' => $data]);
      $res = $response->getBody()->getContents();
    } catch (ClientException $e) {
      $this->errorCode = 500;
      $this->errorMessage = $e->getMessage();
      return false;
    }

    $data = json_decode($res, true);

    if (!isset($data['errcode']) && !isset($data['errmsg'])) {
      $this->errorCode = 'error';
      $this->errorMessage = '企业微信API调用失败';
      return false;
    }

    if (0 !== $data['errcode']) {
      $this->errorCode = $data['errcode'];
      $this->errorMessage = $data['errmsg'];
      return false;
    } else {
      unset($data['errcode']);
      unset($data['errmsg']);
      return $data;
    }
  }

  public function callMultipart($url, $multipart = []) {
    try {
      $response = $this->http->request(
        'POST',
        $url . '?access_token=' . $this->accessToken(),
        [
          'multipart' => $multipart
        ]
      );
      $res = $response->getBody()->getContents();
    } catch (ClientException $e) {
      $this->errorCode = 500;
      $this->errorMessage = $e->getMessage();
      return false;
    }

    $data = json_decode($res, true);

    if (0 != $data['errcode']) {
      $this->errorCode = $data['errcode'] ? $data['errcode'] : 'error';
      $this->errorMessage = $data['errmsg'] ? $data['errmsg'] : '企业微信API调用失败';
      return false;
    } else {
      $return_data = array();
      foreach ($data as $key => $value) {
        switch ($key) {
          case 'errcode':
          case 'errmsg':
            unset($data[$key]);
            break;
          default:
            $return_data = $value;
            break;
        }
      }
    }

    return $return_data;
  }

  public function getError() {
    return $this->errorMessage;
  }

  public function getErrorCode() {
    return $this->errorCode;
  }
}
