<?php

namespace app\v1_0\controller\member;

/** 报名 */
class Sign extends \app\v1_0\controller\common\Base {
  public function _initialize() {
    parent::_initialize();
    $this->checkLogin();
  }
  protected function getData() {
    $param = input('param.');
    $param['create'] = $this->userinfo->uid;
    return $param;
  }
  public function edit() {
    ext(500, '禁止修改');
  }
}
