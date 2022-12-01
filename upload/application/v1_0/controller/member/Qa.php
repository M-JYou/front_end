<?php

namespace app\v1_0\controller\member;

/** 问答 */
class Qa extends \app\v1_0\controller\common\Base {

  public function _initialize() {
    parent::_initialize();
  }
  protected function cud(&$p = null) {
    $this->checkLogin();
    $p['create'] = $this->userinfo->uid;
  }
  public function add() {
    $data = [
      'create' => $this->userinfo->uid,
      'type' => input('post.type/d', 0, 'intval'),
      'answer' => 0,
      'expense' => input('post.expense/d', 0, 'intval'),
      'qa' => input('post.qa/d', 0, 'intval'),
      'name' => input('post.name/s', '', 'trim'),
      'content' => input('post.content/s', '', 'trim'),
      'other' => input('post.other/a', [], 'trim'),
    ];
    $data['expense'] = $data['qa'] ? 0 : input('post.expense/d', 0, 'intval');

    $this->_add($data);
  }
  public function find() {
    // ext(model('Article')->count());
    $param = input('param.');
    $field = '*,`create` user';
    if (isset($param['comment'])) {
      $field .= ',' . $this->userinfo->uid . ' comment';
    }
    $this->_find($param, $field);
  }
  public function tst() {
    // ext(model('Article')->count());
    $param = input('param.');
    $field = isset($param['comment']) ?
      ('*,' . $this->userinfo->uid . ' as comment')
      : null;
    $this->_find($param, $field);
  }
}
