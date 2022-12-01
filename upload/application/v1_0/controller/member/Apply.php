<?php

namespace app\v1_0\controller\member;

class Apply extends \app\v1_0\controller\common\Base {
  public function _initialize() {
    parent::_initialize();
    $this->checkLogin();
  }

  public function add() {
    $p = $this->getData();
    $p['status'] = 0;
    if (!in_array($p['type'], $this->type)) {
      ext(500, '类型错误', $this->type);
    }
    $this->_add($p);
  }
  protected function getData() {
    $ret = input('param.');
    $ret['create'] = $this->userinfo->uid;
    return $ret;
  }
  public function type() {
    ext(200, '获取类型成功', model($this->dbname)->type_);
  }
}
