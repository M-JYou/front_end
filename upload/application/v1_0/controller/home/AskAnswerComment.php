<?php

namespace app\v1_0\controller\home;

use app\common\model\AskAnswerComment as Model;

class AskAnswerComment extends \app\v1_0\controller\common\Base {
  public function _initialize() {
    parent::_initialize();
  }

  public function create() {
    $this->checkLogin(); // 要求登录
    $d = new Model([
      'create' => $this->userinfo->uid, // 创建者ID
      'answer' => input('post.answer/d'),
      'content' => input('post.content/s', ''), // '正文',
      'like' => 0,
    ]);
    $d->save() ?
      $this->ajaxReturn(200, '创建数据成功', $d) :
      $this->ajaxReturn(500, $d->getError());;
  }
  public function update() {
    $id = input('post.id/d', 0);
    if ($id) {
      $s = Model::get($id);
      if ($s && ($s->state | 1 === 1)) {
        $d = new Model();
        ($r = $d->allowField(true)->update(input('post.', []))) ?
          $this->ajaxReturn(200, '修改数据成功', $r) :
          $this->ajaxReturn(500, $d->getError());
      }
      $this->ajaxReturn(500, '该问题禁止修改');
    }
    $this->ajaxReturn(500, '参数错误');
  }
  public function read($_params = null) {
    $d = new Model();
    if (!$_params && count($search =  input('post.search/a', []))) {
      foreach ($search as $key => $value) {
        $d = $d->where('content', 'like', $value);
      }
    } else {
      $d = $d->where(Model::safeWhere($_params ? $_params : input('post.')));
    }
    $ret = $d->order('like desc,id asc')->select();
    if ($_params) {
      return $ret;
    } else {
      $this->ajaxReturn(200, '获取数据成功', $ret);
    }
  }
  public function delete() {
    $ids = input('post.id/a', []);
    $s = Model::all($ids);
    if (count($s) && isset($s[0]->is_display)) {
      (new Model)->where(['id' => array('in', $ids)])->update(['is_display' => 0]);
    } else {
      Model::destroy($ids);
    }
    $this->ajaxReturn(200, '删除数据成功');
  }
  public function like() {
    $this->checkLogin(); // 要求登录
    $r = (new Model)->where('id', input('post.id/d'))->setInc('like');
    $this->ajaxReturn(200, '点赞成功', $r);
  }
}
