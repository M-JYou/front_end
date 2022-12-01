<?php

namespace app\apiadmin\controller;

class FlinkType extends \app\common\controller\Backend {
  public function _initialize() {
    parent::_initialize();
    $this->dbname = 'LinkType';
  }
  public function add() {
    $this->_add([
      'name' => input('post.name/s', '', 'trim'),
      'is_sys' => 0,
    ]);
  }
  public function edit() {
    if (isPost()) {
      $this->_edit(['post.name/s', '', 'trim'], ['id' => input('post.id/d', 0, 'intval')]);
    } else {
      $this->_find(['id' => input('get.id/d', 0, 'intval')]);
    }
  }
}
