<?php

namespace app\apiadmin\controller;

/** 免费学习 */
class Study extends \app\common\controller\Backend {

  public function find() {
    $d=input('param.');
    $this->_find($d,null,'sort desc');
  }
}
