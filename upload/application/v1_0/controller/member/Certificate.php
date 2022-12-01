<?php

/** 我的证书 */

namespace app\v1_0\controller\member;

class Certificate extends \app\v1_0\controller\common\Base {
  public function _initialize() {
    parent::_initialize();
    $this->checkLogin();
  }
  protected function getData() {
    $ret = input('param.');
    $ret['create'] = $this->userinfo->uid;
    return $ret;
  }
}
