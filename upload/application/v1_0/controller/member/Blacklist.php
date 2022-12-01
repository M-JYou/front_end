<?php

namespace app\v1_0\controller\member;

/** 黑名单 */
class Blacklist extends \app\v1_0\controller\common\Base {
  public function add() {
    $id = input('post.id', 0, 'intval');
    if ($id) {
      $this->checkLogin();
      $this->_add(['create' => $this->userinfo->uid, 'mid' => $id]);
    } else {
      ext(500, '缺少属性:id');
    }
  }
  public function edit() {
    ext(500, '禁止修改');
  }
  public function delete() {
    $id = input('post.id', 0, 'intval');
    if ($id) {
      $this->checkLogin();
      $this->_delete(['create' => $this->userinfo->uid, 'mid' => $id]);
    } else {
      ext(500, '缺少属性:id');
    }
  }
}
