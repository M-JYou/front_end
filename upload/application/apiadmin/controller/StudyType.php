<?php

namespace app\apiadmin\controller;

/** 免费学习 */
class StudyType extends \app\common\controller\Backend {

  public function find() {
    $d = input('param.');
    $t = config('database.prefix') . 'admin_role';
    $this->_find($d, "*,(SELECT `name` FROM `$t` b WHERE b.id=`create`) `cname`");
  }
  public function add() {
    $d = input('param.');
    $d['create'] = $this->admininfo->id;
    $this->_add($d);
  }
}
