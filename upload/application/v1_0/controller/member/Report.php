<?php

namespace app\v1_0\controller\member;

/** 举报 */
class Report extends \app\v1_0\controller\common\Base {
  public function _initialize() {
    parent::_initialize();
    $this->checkLogin();
  }
  public function find() {
    $param = input('param.');
    $param['create'] = $this->userinfo->uid;
    $this->_find($param);
  }
  public function add() {
    $param = input('param.');
    $param['create'] = $this->userinfo->uid;
    $this->_add($param);
  }
  public function edit() {
    $param = input('param.');
    $param['create'] = $this->userinfo->uid;
    $this->_edit($param);
  }
  public function delete() {
    $param = input('param.');
    $param['create'] = $this->userinfo->uid;
    $this->_find($param);
  }
}
