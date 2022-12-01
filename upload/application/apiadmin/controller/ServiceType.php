<?php

namespace app\apiadmin\controller;

class ServiceType extends \app\common\controller\Backend {
  public function edit() {
    $param = input('param.');
    if (isGet()) {
      $this->_find($param);
    } else {
      $this->_edit($param);
    }
  }
  public function find() {
    $param = input('param.');
    $field = isset($param['simplify']) ? 'id,name' : '*';
    $this->_find($param, $field);
  }
}
