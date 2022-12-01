<?php

namespace app\v1_0\controller\member;

/** 简历 */
class Resume extends \app\v1_0\controller\personal\Resume {
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
    // $param = input('param.');
    // $param['create'] = $this->userinfo->uid;
    // $this->_add($param);
    ext(500, '系统自动添加');
  }
  public function edit() {
    ext(500, '请通过其他方式修改');
  }
  public function delete() {
    // $param = input('param.');
    // $param['create'] = $this->userinfo->uid;
    // $this->_find($param);
    ext(500, '禁止删除');
  }
}
