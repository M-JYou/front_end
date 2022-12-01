<?php

namespace app\apiadmin\controller;

class ExamType extends \app\common\controller\Backend {
  public function add() {
    $data = input('post.');
    $data['create'] = $this->admininfo->id;
    $this->_add($data);
  }
  public function edit() {
    $this->_edit([
      'id' => input('post.id/d', 0, 'intval'),
      'pid' => input('post.pid/d', 0, 'intval'),
      'is_display' => input('post.is_display/d', 0, 'intval'),
      'name' => input('post.name/s', 0, 'trim'),
      'simple' => input('post.simple/s', 0, 'trim'),
    ]);
  }
}
