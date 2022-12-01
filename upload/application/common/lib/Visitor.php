<?php

/**
 * visitor管理
 *
 * @author
 */

namespace app\common\lib;

use app\common\base\Userinfo;

class Visitor {
  public function refreshLogin(array $visitor, $expire) {
    // $visitor = cookie('visitor');
    cookie('visitor', json_encode($visitor), $expire);
  }
  public function setLogin($visitor, $expire) {
    cookie('visitor', json_encode($visitor), $expire);
  }
  public function setLogout() {
    cookie('visitor', null);
  }
  public function getLoginInfo() {
    $visitor = cookie('visitor');
    if ($visitor) {
      $visitor = json_decode($visitor, true);
      try {
        $user_token = isset($visitor['token']) ? $visitor['token'] : '';
        $auth_result = $this->auth($user_token);
        if ($auth_result['code'] == 200) {
          $auth_info = $auth_result['info'];
          $visitor['uid'] = $auth_info->uid;
        }
      } catch (\Exception $e) {
        return null;
      }
    }
    $pre = config('database.prefix');
    $tj = encode(['create' => $visitor->uid, 'is_display' => ['>=', 0]]);
    $field = "*,(SELECT `mobile` FROM `" . $pre .
      "member` WHERE uid=id) `mobile`,id points,'$tj' shop,'$tj' expert"
      . ',(SELECT count(*) FROM `' . $pre . 'favorites`) `favorites`'
      . ',(SELECT count(*) FROM `' . $pre . 'likes`) `likes`';
    // $visitor = new Userinfo($visitor);
    $me = clo(model('MemberInfo')
      ->where('id', $visitor['uid'])
      ->field($field)
      ->find());
    if ($me) {
      foreach ($me as $k => $v) {
        $visitor[$k] = $v;
      }
    }
    return new Userinfo($visitor);
    // return $visitor;
  }
  protected function auth($request_token) {
    $token = \app\common\lib\JwtAuth::getToken($request_token);
    if ($token->isExpired()) {
      return ['code' => 50002, 'info' => 'token失效'];
    }
    if (!$token->verify(config('sys.safecode'))) {
      return ['code' => 50001, 'info' => '非法token'];
    }
    return ['code' => 200, 'info' => $token->getData('info')];
  }
}
