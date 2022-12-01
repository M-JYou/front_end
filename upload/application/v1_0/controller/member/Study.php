<?php

namespace app\v1_0\controller\member;

/** 免费学习 */
class Study extends \app\v1_0\controller\common\Base {

  public function _initialize() {
    parent::_initialize();
  }
  protected function cud(&$p = null) {
    $this->checkLogin();
  }
  public function find() {
    $param = input('post.');
    $this->_find($param, '*,id history');
  }
  public function add() {
    $data = [
      'create' => $this->userinfo->uid,
      'type' => input('post.type/d', 0, 'intval'),
      'pid' => input('post.pid/d', 0, 'intval'),
      'name' => input('post.name/s', '', 'trim'),
      'content' => input('post.content/s', '', 'trim'),
      'other' => input('post.other/a', []),
    ];
    if (!$data['pid']) {
      $this->ajaxReturn(500, '没有权限');
    }
    $this->_add($data);
  }
  public function edit() {
    $data = [
      'id' => input('post.id/d', 0, 'intval'),
      'type' => input('post.type/d', null, 'intval'),
      'pid' => input('post.pid/d', null, 'intval'),
      'name' => input('post.name/s', null, 'trim'),
      'content' => input('post.content/s', null, 'trim'),
      'other' => input('post.other/a', null),
    ];

    $this->_edit($data, ['id' => $data['id'], 'create' => $this->userinfo->uid]);
  }
  public function delete() {
    $this->_delete(['id' => input('post.id'), 'create' => $this->userinfo->uid]);
  }
  public function tst() {
    ext(
      model($this->dbname)->field('id,id history')->select()
    );
  }
}
