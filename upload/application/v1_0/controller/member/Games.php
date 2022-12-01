<?php

namespace app\v1_0\controller\member;

/** 问答 */
class Games extends \app\v1_0\controller\common\Base {
  private $of = [];

  public function _initialize() {
    parent::_initialize();
  }
  protected function cud(&$p = null) {
    // $this->checkLogin();
    $this->ajaxReturn(500, '没有权限');
  }
  public function find() {
    $this->___find(input('param.'));
  }
}
