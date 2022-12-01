<?php

namespace app\apiadmin\controller;

class Shop extends \app\common\controller\Backend {
  public function find() {
    $d = input("param.");
    $f = '*,`create` `create_`';
    $this->_find($d, $f);
  }
}
