<?php

namespace app\v1_0\controller\member;

class ExamAnswer extends \app\v1_0\controller\common\Base {
  public function _initialize() {
    parent::_initialize();
    $this->checkLogin();
    // out($this->userinfo);
  }
  public function add() {
    $this->_add([
      'create' => $this->userinfo->uid,
      'exam' => input('post.exam/d'),
      'note' => input('post.note/s', '', 'trim'),
      'name' => 1,
      'content' => input('post.content/a', []),
      'score' => 0,
      'state' => 0,
      'correct' => 0,
    ]);
  }
  public function edit() {
    $this->ajaxReturn(500, '权限不足');
  }
  public function delete() {
    $this->ajaxReturn(500, '权限不足');
  }
  public function find() {
    $data = input('param.');
    $data['create'] = $this->userinfo->uid;
    $this->_find($data);
  }
}
