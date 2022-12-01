<?php

namespace app\v1_0\controller\member;

class Bank extends \app\v1_0\controller\common\Base {

  protected function cud(&$p = null) {
    ext(500, '没有权限');
  }
}
