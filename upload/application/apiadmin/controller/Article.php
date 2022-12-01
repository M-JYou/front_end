<?php

namespace app\apiadmin\controller;

class Article extends \app\common\controller\Backend {
  public function find() {
    $p = input('param.');

    $this->_find($p, '*', 'sort_id desc,addtime desc');
  }

  public function edit() {
    $d = input('post.');
    $this->_edit($d);
  }
}
