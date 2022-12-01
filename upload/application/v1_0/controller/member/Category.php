<?php

/**  验证手机号等 */

namespace app\v1_0\controller\member;

class Category extends \app\v1_0\controller\common\Base {
  protected function cud($p = null) {
    $this->ajaxReturn(500, '禁止读写');
  }
}
