<?php

namespace app\v1_0\controller\jz;

class Customer extends \app\v1_0\controller\common\Base {
  public function _initialize() {
    parent::_initialize();
    $this->checkLogin();
  }
  protected function cud(&$param = []) {
    $param['create'] = $this->userinfo->uid;
  }
  public function find() {
    $param = input('param.');
    $param['create'] = $this->userinfo->uid;
    $field = '*';
    if (input('?param.account')) {
      $field .= ', `account` `account_`';
    }
    $this->_find($param, $field);
  }
}
