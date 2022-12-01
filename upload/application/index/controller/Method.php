<?php

namespace app\index\controller;

class Method extends \app\index\controller\Base {
  public function _initialize() {
    parent::_initialize();
  }
  public function agent() {
    $this->assign('navSelTag', 'agent');
  }
}
