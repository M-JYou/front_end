<?php

/**  验证手机号等 */

namespace app\v1_0\controller\member;

class Flink extends \app\v1_0\controller\common\Base {
  public function _initialize() {
    parent::_initialize();
  }
  public function find() {
    $param = input('param.');
    $this->_find($param, '*', null, null, model('Link'));
  }
  public function add() {
    $this->checkLogin();
    $d = input('post.');
    $d['type'] = 1;
    $this->_add($d, model('Link'));
  }
  public function edit() {
    ext(500, '没有权限');
  }
  public function delete() {
    ext(500, '没有权限');
  }
}
