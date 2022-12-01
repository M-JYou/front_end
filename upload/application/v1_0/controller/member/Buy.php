<?php

/**  验证手机号等 */

namespace app\v1_0\controller\member;

class Buy extends \app\v1_0\controller\common\Base {
  public function _initialize() {
    parent::_initialize();
    $this->checkLogin();
  }
  public function find() {
    $param = input('param.');
    $this->instCreate($param);
    $this->_find($param);
  }
  public function add() {
    $param = input('post.');
    $this->_add($param);
  }
  public function edit() {
    ext(500, '没有权限');
  }
  public function delete() {
    ext(500, '没有权限');
  }
}
