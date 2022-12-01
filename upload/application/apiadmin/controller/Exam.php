<?php

namespace app\apiadmin\controller;

class Exam extends \app\common\controller\Backend {

  public function find() {
    $d=input('post.');

    $this->_find($d,null,'sort desc');
  }
  public function add() {
    $d = input('post.');
    unset($d['addtime']);
    $d['create'] = $this->admininfo->id;
    $this->_add($d);
  }
  public function answerType() {
    ext(200, '获取类型成功', [
      ['id' => 0, 'name' => '问答题'],
      ['id' => 1, 'name' => '单选题'],
      ['id' => 2, 'name' => '多选题'],
      ['id' => 3, 'name' => '排序题'],
    ]);
  }
}
