<?php

namespace app\v1_0\controller\home;

use app\apiadmin\controller\Member;
use app\common\model\Ask as Model;
use think\Request;

class Ask extends \app\v1_0\controller\common\Base {
  public function _initialize() {
    parent::_initialize();
  }
  public function create($_params = null) {
    $this->checkLogin(); // 要求登录
    $points = input('post.points/d', 0);

    $r = model('Member')->setMemberPoints(
      [
        'uid' => $this->userinfo->uid, 'points' => $points,
        'explain' => '付费问答',
        'id' => '0', 'username' => 'system'
      ],
      2
    );
    return $this->ajaxReturn(200, 'ok', $r);
  }
  public function update($_params = null) {
    $this->checkLogin(); // 需要登录
    $p = is_array($_params) ? $_params : input('param.');
    $d = new Model;
    $t = $d->getById($_params);
    $r = false;
    // 权限判定
    if ($t && $t['create'] == $this->userinfo->uid && $t['state'] < 4) {
      $q = ['id' => $p['id']];
      if (isset($p['id'])) {
        $q['state'] = $p['state'];
      } else {
        if (isset($p['answer'])) { // 确定采纳
          # code...
        }
      }
      $r = $d->u($q);
    }
    try {
      $r = $d->u($p);
    } catch (\Throwable $th) {
      $r = false;
    }
    if ($_params) {
      return $d;
    } else {
      $r ?
        $this->ajaxReturn(200, '修改数据成功', $d) :
        $this->ajaxReturn(500, $d->getError());
    }
  }
  public function read($_params = null) {

    return $this->ajaxReturn(200, '修改数据成功', $this->adminInfo);

    $p = is_array($_params) ? $_params : input('param.');
    $d = new Model;
    try {
      $r = $d->u($p);
    } catch (\Throwable $th) {
      $r = false;
    }
    if ($_params) {
      return $d;
    } else {
      $r ?
        $this->ajaxReturn(200, '修改数据成功', $d) :
        $this->ajaxReturn(500, $d->getError());
    }
  }
  public function delete($_params = null) {
    $p = is_array($_params) ? $_params : input('param.');
    $d = new Model;
    try {
      $r = $d->u($p);
    } catch (\Throwable $th) {
      $r = false;
    }
    if ($_params) {
      return $d;
    } else {
      $r ?
        $this->ajaxReturn(200, '修改数据成功', $d) :
        $this->ajaxReturn(500, $d->getError());
    }
  }
}
