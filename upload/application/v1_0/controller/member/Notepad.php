<?php

namespace app\v1_0\controller\member;

/** 记事本 */
class Notepad extends \app\v1_0\controller\common\Base {
  public function _initialize() {
    parent::_initialize();
    $this->checkLogin();
  }
  public function add() {
    $data = [
      'create' => $this->userinfo->uid,
      'content' => input('post.content/s', '', 'trim'),
      'time' => input('post.time/s', 0, 'trim'),
    ];
    $this->_add($data);
  }
  public function find() {
    $param = input('param.');
    $param['create'] = $this->userinfo->uid;
    $this->_find($param);
  }

  public function edit() {
    $data = [
      'id' => input('post.id/d', 0, 'intval'),
      'content' => input('post.content/s', null, 'trim'),
      'time' => input('post.time/s', 0, 'trim'),
    ];

    $this->_edit($data, ['id' => $data['id'], 'create' => $this->userinfo->uid]);
  }
  public function delete() {
    $this->_delete(['id' => input('post.id'), 'create' => $this->userinfo->uid]);
  }
}
