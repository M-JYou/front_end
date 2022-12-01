<?php

namespace app\apiadmin\controller;

class ExamAnswer extends \app\common\controller\Backend {
  public function find() {
    $d = input('param.');

    $this->_find($d, '*,`create` `create_`', 'addtime desc');
  }
  public function add() {
    $this->ajaxReturn(500, '管理员不能新增');
  }
  public function edit() {
    $d = input('post.');
    if (isset($d['content'])) {
      unset($d['id']);
      $m = model($this->dbname);
      $d = checkFields($d, $m->getUpdataColumn(), true);
      $d['#admin'] = $this->admininfo->id;
      $m->update($d, ['id' => input('post.id/d', 0, 'intval')]);
      ext(200, '数据修改成功');
    }
    $this->_edit($d);
  }
  public function type() {
    ext(200, '获取类型成功', [
      ['id' => 0, 'name' => '问答题'],
      ['id' => 1, 'name' => '单选题'],
      ['id' => 2, 'name' => '多选题'],
      ['id' => 3, 'name' => '排序题'],
    ]);
  }
}
