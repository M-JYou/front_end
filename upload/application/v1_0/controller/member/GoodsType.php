<?php

namespace app\v1_0\controller\member;

class GoodsType extends \app\v1_0\controller\common\Base {
  public function _initialize() {
    parent::_initialize();
  }
  protected function cud(&$p = null) {
    ext(500, '没有权限', null);
  }
}
