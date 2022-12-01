<?php

namespace app\apiadmin\controller;

class Flink extends \app\common\controller\Backend {
  public function _initialize() {
    parent::_initialize();
    $this->dbname = 'LinkType';
  }
  public function find() {
    $m = model('Link');
    $param = input('param.');
    if (isset($param['key_type']) && isset($param['keyword'])) {
    }
    if (isset($param['is_display']) && !$param['is_display'] && strval($param['is_display']) !== '0') {
      unset($param['is_display']);
    }
    if (isset($param['type']) && !$param['type']) {
      unset($param['type']);
    }
    $d = $m->r($param, null, 'sort_id desc,id asc');
    $e = $m->getError();
    $this->ajaxReturn(
      $e ? 500 : 200,
      '获取数据:' . $e,
      $d
    );
  }
  public function add() {
    $input_data = [
      'name' => input('post.name/s', '', 'trim'),
      'notes' => input('post.notes/s', '', 'trim'),
      'is_display' => input('post.is_display/d', 1, 'intval'),
      'type' => input('post.type/d', 0, 'intval'),
      'link_url' => input('post.link_url/s', '', 'trim'),
      'link_ico' => input('post.link_ico/s', '', 'trim'),
      'sort_id' => input('post.sort_id/d', 0, 'intval'),
      'content' => input('post.content/s', '', 'trim'),
    ];
    if (
      false ===
      model('Link')
      ->validate(true)
      ->allowField(true)
      ->save($input_data)
    ) {
      $this->ajaxReturn(500, model('Link')->getError());
    }
    model('AdminLog')->record(
      '添加友情链接。友情链接ID【' .
        model('Link')->id .
        '】;友情链接名称【' .
        $input_data['name'] .
        '】',
      $this->admininfo
    );
    $this->ajaxReturn(200, '保存成功');
  }
  public function edit() {
    $id = input('get.id/d', 0, 'intval');
    if ($id) {
      $info = model('Link')->find($id);
      if (!$info) {
        $this->ajaxReturn(500, '数据获取失败');
      }
      $this->ajaxReturn(200, '获取数据成功', ['info' => $info]);
    } else {
      $input_data = [
        'id' => input('post.id/d', 0, 'intval'),
        'is_display' => input('post.is_display/d', null, 'intval'),
        'name' => input('post.name/s', null, 'trim'),
        'link_url' => input('post.link_url/s', null, 'trim'),
        'link_ico' => input('post.link_ico/s', null, 'trim'),
        'sort_id' => input('post.sort_id/d', null, 'intval'),
        'notes' => input('post.notes/s', null, 'trim'),
        'content' => input('post.content/s', null, 'trim'),
        'type' => input('post.type/d', null, 'intval'),
      ];
      $id = intval($input_data['id']);
      if (!$id) {
        $this->ajaxReturn(500, '请选择数据');
      }
      clearArrNull($input_data);
      if (
        false ===
        model('Link')
        ->validate(true)
        ->allowField(true)
        ->update($input_data, ['id' => $id])
      ) {
        $this->ajaxReturn(500, model('Link')->getError());
      }
      model('AdminLog')->record('编辑友情链接。友情链接ID【' . $id . '】', $this->admininfo);
      $this->ajaxReturn(200, '保存成功');
    }
  }
  public function delete() {
    $id = input('post.id/a');
    if (!$id) {
      $this->ajaxReturn(500, '请选择数据');
    }
    $list = model('Link')
      ->where('id', 'in', $id)
      ->column('name');
    model('Link')->destroy($id);
    model('AdminLog')->record(
      '删除友情链接。友情链接ID【' .
        implode(',', $id) .
        '】;友情链接名称【' .
        implode(',', $list) .
        '】',
      $this->admininfo
    );
    $this->ajaxReturn(200, '删除成功');
  }
  public function type() {
    $this->ajaxReturn(200, '获取数据成功', model('LinkType')->select());
  }
}
