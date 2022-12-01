<?php

namespace app\apiadmin\controller;

class TrademarkTransferType extends \app\common\controller\Backend {
  public function find() {
    $param = input('param.');
    $field = isset($param['name']) ? '*,`create` `name`' : '*';
    if (isset($param['type_'])) {
      $field .= ',`type` `type_`';
    }
    $this->_find($param, $field);
  }
}
