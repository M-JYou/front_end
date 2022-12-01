<?php

/**  验证手机号等 */

namespace app\v1_0\controller\member;

class Article extends \app\v1_0\controller\common\Base {
  public function _initialize() {
    parent::_initialize();
    $this->checkLogin();
  }
  private function pri(array $param) {
    $arr = [5, 8, 14];
    $ret = $param && isset($param['cid']) &&
      in_array($param['cid'], $arr);
    if ($ret) {
      $param['create'] = $this->userinfo->uid;
    } elseif (!isset($param['cid'])) {
      $param['cid'] = ['not in', $arr];
    }
    return $ret;
  }
  public function find() {
    $param = input('param.');
    // $this->pri($param);
    $id = ($this->userinfo->uid ? $this->userinfo->uid  : 0);
    $field = '*,' . $id . ' `buy`';
    // ext([$param, $field]);
    $this->_find($param, $field);
  }
  public function add() {
    $param = input('post.');
    if ($this->pri($param)) {
      $this->_add($param);
    }
    ext(500, '没有权限');
  }
  public function edit() {
    $id = input('post.id/d');
    if ($id) {
      $param = input('post.');
      $param['id'] = $id;
      $this->_edit($param, ['id' => $id, 'create' => $this->userinfo->uid]);
    }
    $this->ajaxReturn(200, '参数错误');
  }
  public function delete() {
    $this->_delete(['id' => input('post.id'), 'create' => $this->userinfo->uid]);
  }
}
