<?php

namespace app\v1_0\controller\member;

/** 转发 */
class Reprint extends \app\v1_0\controller\common\Base {
  public function _initialize() {
    parent::_initialize();
    $this->checkLogin();
  }
  public function find() {
    $param = input('param.');
    $param['create'] = $this->userinfo->uid;
    $this->_find($param);
  }
  public function add() {
    $param = input('param.');
    $param['create'] = $this->userinfo->uid;
    $this->_add($param);
  }
  public function edit() {
    ext(500, '禁止修改');
  }
  public function delete() {
    $param = input('param.');
    $param['create'] = $this->userinfo->uid;
    $this->_find($param);
  }
}
