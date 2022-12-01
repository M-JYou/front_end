<?php

namespace app\v1_0\controller\member;

/** 企业 */
class Company extends \app\v1_0\controller\common\Base {
  protected function cud(&$p = null) {
    $this->checkLogin();
  }
  public function find() {
    $param = input('param.');
    $this->_find($param, '*,id `info`,id `contact`');
  }
  public function add() {
    $d = input('param.');
    $d['uid'] = $this->userinfo->uid;
    $this->_add($d);
  }
  public function edit() {
    $d = input('param.');
    $d['uid'] = $this->userinfo->uid;
    if (
      isset($d['info']) && is_array($d['info'])
      && ($s = model($this->dbname)->where(['id' => input('post.id/d'), 'uid' => $this->userinfo->uid]))
    ) {
      model('CompanyInfo')->u($d['info'], ['comid', $s['id']]);
    }
    if (
      isset($d['contact']) && is_array($d['contact'])
      && ($s = model($this->dbname)->where(['id' => input('post.id/d'), 'uid' => $this->userinfo->uid]))
    ) {
      model('CompanyContact')->u($d['contact'], ['comid', $s['id']]);
    }
    $this->_edit($d, [
      'id' => input('post.id/d', 0, 'intval'),
      'uid' => $this->userinfo->uid
    ]);
  }
  public function delete() {
    $d = input('param.');
    $d['uid'] = $this->userinfo->uid;
    $this->_delete($d);
  }
}
