<?php

namespace app\v1_0\controller\jz;

class Account extends \app\v1_0\controller\common\Base {
  public function _initialize() {
    parent::_initialize();
    $this->checkLogin();
  }
  protected function getData() {
    $param=input('param.');
    $param['create'] = $this->userinfo->uid;
    return $param;
  }
  public function find() {
    $param = input('param.');
    $param['create'] = $this->userinfo->uid;
    $field = '*';
    $this->_find($param, $field);
  }
}
