<?php

namespace app\apiadmin\controller;

/** 财税游戏 */
class Games extends \app\common\controller\Backend {
  public function _initialize() {
    parent::_initialize();
  }
  public function add() {
    $data = [
      'create' => $this->userinfo->uid,
      'type' => input('post.type/d', 0, 'intval'),
      'games' => input('post.games/d', 0, 'intval'),
      'name' => input('post.name/s', '', 'trim'),
      'cover' => input('post.cover/s', '', 'trim'),
      'link' => input('post.link/s', '', 'trim'),
      'content' => input('post.content/s', '', 'trim'),
      'other' => input('post.other/a', [], 'trim'),
    ];
    $data['expense'] = $data['games'] ? 0 : input('post.expense/d', 0, 'intval');

    $this->_add($data);
  }
  public function edit() {
    $data = input('post.');

    $this->_edit($data);
  }
  public function delete() {
    $this->_delete(['id' => input('post.id')]);
  }
}
