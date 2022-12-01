<?php

namespace app\apiadmin\controller;

use app\v1_0\controller\member\Contract as MemberContract;

class Contract extends \app\common\controller\Backend {
  public function type() {
    ext((new MemberContract())->type);
  }
  public function state() {
    ext((new MemberContract())->state);
  }
  public function find() {
    $p=input('param.');
    $this->_find($p,'*,`create` `create_`');
  }
}
