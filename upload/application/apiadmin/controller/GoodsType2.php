<?php

namespace app\apiadmin\controller;

class GoodsType2 extends \app\common\controller\Backend {

  public function add() {
    $input_data = [
      'pid'     => input('post.imageid/d', 0, 'intval'),  // '上级id',
      'status' => 1,
      'create'  => 0,                  // '创建人id',
      'name'    => input('post.name/s', '', 'trim'),     // '名称',
      'simple'  => input('post.simple/s', '', 'trim'),     // '简介',
    ];
    $mm = model('GoodsType');
    if ($input_data['pid'] && !$mm->get($input_data['pid'])) {
      return $this->ajaxReturn(500, '上级ID不存在', $input_data['pid']);
    }
    if (false === $mm->validate(true)->allowField(true)->save($input_data)) {
      $this->ajaxReturn(500, $mm->getError());
    }
    model('AdminLog')->record('添加【商品类型2】。ID【' . $mm->id . '】;标题【' . $input_data['name'] . '】', $this->admininfo);
    $this->ajaxReturn(200, '保存成功');
  }
  public function edit() {
    $input_data = input('post.');
    $mm = model('GoodsType');
    if ($mm->get($input_data['id'])) {
      unset($input_data['create']);
      if (false === $mm->validate(true)->allowField(true)->update($input_data)) {
        $this->ajaxReturn(500, $mm->getError());
      }
      model('AdminLog')->record('编辑【商品类型2】。ID【' . $input_data['id'] . '】;标题【' . $input_data['name'] . '】', $this->admininfo);
      $this->ajaxReturn(200, '保存成功');
    }
    $this->ajaxReturn(500, 'id不存在');
  }
  public function delete() {
    $id = input('post.id/a');
    if (!$id) {
      $this->ajaxReturn(500, '请选择数据');
    }
    $list = model('GoodsType')->where('id', 'in', $id)->column('name');
    model('GoodsType')->destroy($id);
    model('AdminLog')->record('删除【商品类型2】。ID【' . implode(',', $id)  . '】;标题【' . implode(',', $list) . '】', $this->admininfo);
    $this->ajaxReturn(200, '删除成功');
  }

  public function tree() {
    $d = input('param.');
    if (!isset($d['pid'])) {
      $d['pid'] = 0;
    }
    ext(model($this->dbname)->getModel($d)->field('id,pid,name,id children')->select());
  }
}
