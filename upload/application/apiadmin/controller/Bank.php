<?php

namespace app\apiadmin\controller;

class Bank extends \app\common\controller\Backend {
  public function find() {
    model('Bank')->_find(input('param.'));
  }
  public function edit() {
    $id = input('get.id/d', 0, 'intval');
    if ($id) {
      $ret = model('Bank')->get($id);
      if (!$ret) {
        $this->ajaxReturn(500, '获取数据失败', $ret);
      }

      if ($article = input('param.article')) {
        $ret->article($article);
      }
      if (input('param.addr')) {
        $ret->addr();
      }
      $this->ajaxReturn(200, '获取数据成功', $ret);
    } else {
      $param = input('post.');
      $this->_edit($param);
    }
  }
}
