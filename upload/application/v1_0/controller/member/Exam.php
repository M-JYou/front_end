<?php

namespace app\v1_0\controller\member;

class Exam extends \app\v1_0\controller\common\Base {
  protected function cud(&$p = null) {
    $this->ajaxReturn(500, '权限不足');
  }
}
