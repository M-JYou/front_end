<?php

namespace app\apiadmin\controller;

class Apply extends \app\common\controller\Backend {
  public function find() {
    $p = input('param.');
    $this->_find($p, '*,`create` `create_`');
  }
  public function type() {
    ext(model($this->dbname)->type_);
  }
}
