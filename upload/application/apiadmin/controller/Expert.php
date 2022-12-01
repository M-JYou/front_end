<?php

namespace app\apiadmin\controller;

/** 专家库 */
class Expert extends \app\common\controller\Backend {
  public function find() {
    $param = input('param.');
    $this->_find($param);
  }
  public function edit() {
    $id = input('get.id/d', 0, 'intval');
    if ($id) {
      $this->_find(['id' => $id]);
    } else {
      $param = input('post.');
      $this->_edit($param);
    }
  }
}
