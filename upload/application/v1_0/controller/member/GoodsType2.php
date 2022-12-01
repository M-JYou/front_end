<?php

namespace app\v1_0\controller\member;

class GoodsType2 extends \app\v1_0\controller\common\Base {
  public function _initialize() {
    parent::_initialize();
  }
  protected function cud(&$p = null) {
    $this->checkLogin(1);
  }
  public function add() {
    $input = [
      'status'  => input('post.status/d', 0, 'intval'),
      'pid'     => input('post.imageid/d', 0, 'intval'),  // '上级id',
      'create'  => $this->userinfo->uid,                  // '创建人id',
      'name'    => input('post.name/s', '', 'trim'),     // '名称',
      'simple'  => input('post.simple/s', '', 'trim'),     // '简介',
    ];
    $this->_add($input);
  }
  public function edit() {
    $input = input('post.');
    $this->_edit($input, ['id' => input('post.id'), 'create' => $this->userinfo->uid]);
  }
  public function delete() {
    $this->_delete(['id' => input('post.id'), 'create' => $this->userinfo->uid]);
  }
}
