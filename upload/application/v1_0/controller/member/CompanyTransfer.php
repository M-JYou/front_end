<?php

/**  验证手机号等 */

namespace app\v1_0\controller\member;

class CompanyTransfer extends \app\v1_0\controller\common\Base {
  protected function cud(&$p = null) {
    $this->checkLogin();
    $p['create'] = $this->userinfo->uid;
  }
  public function find() {
    $param = input('param.');
    $field = isset($param['name']) ? '*,`create` `name`' : '*';
    if (isset($param['type_'])) {
      $field .= ',`type` `type_`';
    }
    $this->_find($param, $field);
  }
}
