<?php

namespace app\v1_0\controller\member;

/** 公司求购 */
class CompanyBuy extends \app\v1_0\controller\common\Base {
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

  public function type() {
    $m = model('CompanyTransferType');
    $d = $m->r(input('param.'));
    $e = $m->getError();
    $this->ajaxReturn($e ? 500 : 200, $e ? $e : '获取数据成功', $d);
  }
}
