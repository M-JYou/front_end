<?php

namespace app\apiadmin\controller;

class Addr extends \app\common\controller\Backend {
  public function _initialize() {
    parent::_initialize();
    $this->dbname = 'CategoryDistrict';
  }
  public function tst() {
    $this->_find(['id' => 630103], 'name,id `RMB`');
  }
}
