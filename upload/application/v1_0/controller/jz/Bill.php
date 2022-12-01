<?php

namespace app\v1_0\controller\jz;

class Bill extends \app\v1_0\controller\common\Base {
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
    $field = '*,`customer` customer_, `account` `account_`';

    if (isset($param['account']) && isWhere($param['account'])) {
      $mc = model('Account');
      $param['account'] = ['in', $mc->where($mc->toWhere($param['account']))->column('id')];
    }
    if (isset($param['customer']) && isWhere($param['customer'])) {
      $mc = model('Customer');
      $param['customer'] = ['in', $mc->where($mc->toWhere($param['customer']))->column('id')];
    }

    $this->_find($param, $field);
  }
}
