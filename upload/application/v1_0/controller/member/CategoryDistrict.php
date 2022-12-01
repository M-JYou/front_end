<?php

namespace app\v1_0\controller\member;

/** 地址 */
class CategoryDistrict extends \app\v1_0\controller\common\Base {
  public function _initialize() {
    parent::_initialize();
  }
  protected function cud(&$p = null) {
    ext(200, '没有权限');
  }
}
