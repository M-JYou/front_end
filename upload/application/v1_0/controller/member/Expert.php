<?php

namespace app\v1_0\controller\member;

/** 专家库 */
class Expert extends \app\v1_0\controller\common\Base {

  protected function cud(&$p = null) {
    $this->checkLogin();
  }
  public function find() {
    $param = input('param.');
    if (!isset($param['id'])) {
      $param['id_display'] = 1;
    }
    $field = '*,(SELECT nickname FROM `qs_member_info` b WHERE b.id=`create`) nickname'
      . ',(SELECT photo FROM `qs_member_info` b WHERE b.id=`create`) photo'
      . ',(SELECT online FROM `qs_member_info` b WHERE b.id=`create`) online';
    $this->_find($param, $field);
  }
  public function add() {
    $param = input('post.');
    $param['is_display'] = 0;
    $s = model($this->dbname)->where('id', $this->userinfo->uid)->field('id')->find();
    if ($s) {
      $this->_edit($param, $s);
    }
    $param['create'] = $this->userinfo->uid;
    $this->_add($param);
  }
  public function edit() {
    $param = input('post.');
    $param['is_display'] = 0;
    $this->_edit($param, ['id' => input('post.id'), 'create' => $this->userinfo->uid]);
  }
  public function delete() {
    $this->_delete(['id' => input('post.id'), 'create' => $this->userinfo->uid]);
  }
}
