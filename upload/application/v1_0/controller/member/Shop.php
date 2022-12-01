<?php

namespace app\v1_0\controller\member;

class Shop extends \app\v1_0\controller\common\Base {
  public function _initialize() {
    parent::_initialize();
    // $this->checkLogin(1);
  }
  protected function cud(&$p = null) {
    $this->checkLogin(1);
  }
  public function find() {
    $param = input('param.');
    $param['is_display'] = 1;
    $this->_find($param, '*', 'sort desc,id desc');
  }
  public function add() {
    $param = input('post.');
    $param['create'] = $this->userinfo->uid;
    $param['sort'] = 0;
    $param['is_display'] = 0;
    $ks = ['sort', 'is_display', 'evaluate', 'service', 'logistics'];
    foreach ($$ks as $k) {
      unset($param[$k]);
    }

    $this->_add($param);
  }
  public function edit() {
    $param = input('post.');
    unset($param['sort']);
    $ks = ['sort', 'is_display', 'evaluate', 'service', 'logistics'];
    foreach ($$ks as $k) {
      unset($param[$k]);
    }
    $param['is_display'] = 0;
    $this->_edit($param, ['id' => input('post.id'), 'create' => $this->userinfo->uid]);
  }
  public function delete() {
    $this->_delete(['id' => input('post.id'), 'create' => $this->userinfo->uid]);
  }
}
