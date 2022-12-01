<?php

namespace app\index\controller;

use app\common\lib\Pager;

class Live extends \app\index\controller\Base {

    public function index() {
        $this->assign('pageHeader', $this->pageHeader);
        return $this->fetch('index');
    }
}
