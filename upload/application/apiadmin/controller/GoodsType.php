<?php

namespace app\apiadmin\controller;

class GoodsType extends \app\common\controller\Backend {

  public function index() {
    $where = input('post.');
    if (isset($where['status'])) {
      $where['status'] = [
        // ['status', '>', 0], // 后台不需要
        $where['status']
      ];
    } else {
      // $where['status'] = ['status', '>', 0]; // 后台不需要
    }

    $this->ajaxReturn(
      200,
      '获取数据成功',
      model($this->dbname)->r($where)
    );
  }
  public function add() {
    $d = [
      'pid'     => input('post.pid/d', 0, 'intval'),  // '上级id',
      'create'  => $this->admininfo->id,                  // '创建人id',
      'name'    => input('post.name/s', '', 'trim'),     // '名称',
      'simple'  => input('post.simple/s', '', 'trim'),     // '简介',
      'status' => input('post.status/d', 0, 'intval'), // 状态
    ];
    $mm = model($this->dbname);
    if ($d['pid'] && !$mm->get($d['pid'])) {
      return $this->ajaxReturn(500, '上级ID不存在', $d['pid']);
    }
    if (false === $mm->validate(true)->allowField(true)->save($d)) {
      $this->ajaxReturn(500, $mm->getError());
    }
    model('AdminLog')->record('添加【商品类型】。ID【' . $mm->id . '】;标题【' . $d['name'] . '】', $this->admininfo);

    $this->ajaxReturn(200, '保存成功');
  }
  public function edit() {
    $d = input('post.');
    $mm = model($this->dbname);
    if ($mm->get($d['id'])) {
      unset($d['create']);
      if (false === $mm->validate(true)->allowField(true)->update($d)) {
        $this->ajaxReturn(500, $mm->getError());
      }
      model('AdminLog')->record('编辑【商品类型】。ID【' . $d['id'] . '】;标题【' . $d['name'] . '】', $this->admininfo);

      $this->ajaxReturn(200, '保存成功');
    }
    $this->ajaxReturn(500, 'id不存在');
  }
  public function delete() {
    $id = input('post.id/a');
    if (!$id) {
      $this->ajaxReturn(500, '请选择数据');
    }
    $list = model($this->dbname)->where('id', 'in', $id)->column('name');
    model($this->dbname)->destroy($id);
    model('AdminLog')->record('删除【商品类型】。ID【' . implode(',', $id)  . '】;标题【' . implode(',', $list) . '】', $this->admininfo);

    $this->ajaxReturn(200, '删除成功');
  }
}
