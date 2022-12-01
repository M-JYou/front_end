<?php

namespace app\v1_0\controller\member;

/** 戏说财税 */
class Entertain extends \app\v1_0\controller\common\Base {
  private $of = [];

  public function _initialize() {
    parent::_initialize();
  }
  protected function cud(&$p = null) {
    $this->checkLogin();
  }
  public function add() {
    $data = [
      'create' => $this->userinfo->uid,
      'type' => input('post.type/d', 0, 'intval'),
      'entertain' => input('post.entertain/d', 0, 'intval'),
      'name' => input('post.name/s', '', 'trim'),
      'content' => input('post.content/s', '', 'trim'),
      'other' => input('post.other/a', [], 'trim'),
    ];
    $data['expense'] = $data['entertain'] ? 0 : input('post.expense/d', 0, 'intval');

    $this->_add($data);
  }
  public function find() {
    $this->___find(input('param.'));
  }
  public function edit() {
    $data = [
      'id' => input('post.id/d', 0, 'intval'),
      'name' => input('post.name/s', null, 'trim'),
      'content' => input('post.content/s', null, 'trim'),
      'other' => input('post.other/s', null, 'trim'),
    ];

    $this->_edit($data, ['id' => $data['id'], 'create' => $this->userinfo->uid]);
  }
  public function delete() {
    $this->_delete(['id' => input('post.id'), 'create' => $this->userinfo->uid]);
  }
}
