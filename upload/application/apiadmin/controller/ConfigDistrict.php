<?php

namespace app\apiadmin\controller;

class ConfigDistrict extends \app\common\controller\Backend {
  public function index() {
    $pid = input('get.pid/d', 0, 'intval');
    $list = model('CategoryDistrict')
      ->where('pid', $pid)
      ->order('sort_id desc,id asc')
      ->select();
    foreach ($list as $key => $value) {
      $children = model('CategoryDistrict')->getCache($value['id']);
      $list[$key]['hasChildren'] = $children ? true : false;
    }
    $this->ajaxReturn(200, '获取数据成功', $list);
  }
  public function options() {
    $list = model('CategoryDistrict')->getCache('0');
    $return = [];
    foreach ($list as $key => $value) {
      $arr = [];
      $arr['value'] = $key;
      $arr['label'] = $value;
      $arr['level'] = 1;
      $children = model('CategoryDistrict')->getCache($key);
      if ($children) {
        foreach ($children as $k => $v) {
          $arr['children'][] = [
            'value' => $k,
            'label' => $v,
            'level' => 2
          ];
        }
      }
      $return[] = $arr;
    }
    $this->ajaxReturn(200, '获取数据成功', $return);
  }
  public function add() {
    $input_data = [
      'parentid' => input('post.parentid/a'),
      'name' => input('post.name/s', '', 'trim'),
      'sort_id' => input('post.sort_id/d', 0, 'intval'),
      'level' => input('post.level/d', 0, 'intval')
    ];
    $input_data['pid'] =
      isset($input_data['parentid']) && is_array($input_data['parentid'])
      ? (!empty($input_data['parentid'])
        ? end($input_data['parentid'])
        : 0)
      : 0;
    unset($input_data['parentid']);
    $result = model('CategoryDistrict')
      ->validate(true)
      ->allowField(true)
      ->save($input_data);
    if (false === $result) {
      $this->ajaxReturn(500, model('CategoryDistrict')->getError());
    }
    model('AdminLog')->record(
      '添加地区分类。分类ID【' .
        model('CategoryDistrict')->id .
        '】;分类名称【' .
        $input_data['name'] .
        '】',
      $this->admininfo
    );
    $this->ajaxReturn(200, '保存成功');
  }
  public function edit() {
    $id = input('get.id/d', 0, 'intval');
    if ($id) {
      $info = model('CategoryDistrict')->find($id);
      if (!$info) {
        $this->ajaxReturn(500, '数据获取失败');
      }
      if ($info['pid'] > 0) {
        $parent = model('CategoryDistrict')
          ->where('id', $info['pid'])
          ->column('pid');
        if ($parent[0] > 0) {
          $info['parentid'] = [$parent[0], $info['pid']];
        } else {
          $info['parentid'] = [$info['pid']];
        }
      } else {
        $info['parentid'] = [];
      }
      $this->ajaxReturn(200, '获取数据成功', ['info' => $info]);
    } else {
      $input_data = [
        'id' => input('post.id/d', 0, 'intval'),
        'parentid' => input('post.parentid/a'),
        'name' => input('post.name/s', '', 'trim'),
        'sort_id' => input('post.sort_id/d', 0, 'intval'),
        'level' => input('post.level/d', 0, 'intval'),
        'img' => input('post.img/s', '', 'trim'),
      ];
      if ($input_data['img'] && !fieldRegex($input_data['img'], 'url')) {
        ext(500, '地标图片url错误');
      }
      $id = intval($input_data['id']);
      if (!$id) {
        $this->ajaxReturn(500, '请选择数据');
      }
      $input_data['pid'] =
        isset($input_data['parentid']) &&
        is_array($input_data['parentid'])
        ? (!empty($input_data['parentid'])
          ? end($input_data['parentid'])
          : 0)
        : 0;
      unset($input_data['parentid']);
      $result = model('CategoryDistrict')
        ->validate(true)
        ->allowField(true)
        ->save($input_data, ['id' => $id]);
      if (false === $result) {
        $this->ajaxReturn(500, model('CategoryDistrict')->getError());
      }
      model('AdminLog')->record(
        '编辑地区分类。分类ID【' .
          $id .
          '】;分类名称【' .
          $input_data['name'] .
          '】',
        $this->admininfo
      );
      $this->ajaxReturn(200, '保存成功');
    }
  }
  public function delete() {
    $id = input('post.id/a');
    if (!$id) {
      $this->ajaxReturn(500, '请选择数据');
    }
    $children = model('CategoryDistrict')
      ->where('pid', 'in', $id)
      ->select();
    if ($children) {
      $child_ids = [];
      foreach ($children as $key => $value) {
        $child_ids[] = $value['id'];
      }
      model('CategoryDistrict')
        ->where('pid', 'in', $child_ids)
        ->delete();
      model('CategoryDistrict')
        ->where('pid', 'in', $id)
        ->delete();
    }
    model('CategoryDistrict')->destroy($id);
    model('AdminLog')->record(
      '删除地区分类。分类ID【' . implode(',', $id) . '】',
      $this->admininfo
    );
    $this->ajaxReturn(200, '删除成功');
  }
  public function tablesave() {
    $inputdata = input('post.');
    if (!$inputdata) {
      $this->ajaxReturn(500, '提交数据为空');
    }
    $sqldata = [];
    foreach ($inputdata as $key => $value) {
      if (!$value['id']) {
        continue;
      }
      $arr['id'] = $value['id'];
      $arr['sort_id'] = $value['sort_id'] == '' ? 0 : $value['sort_id'];
      $arr['pid'] = $value['pid'];
      $arr['name'] = $value['name'];
      $arr['level'] = $value['level'];
      $sqldata[] = $arr;
    }
    $validate = \think\Loader::validate('CategoryDistrict');
    foreach ($sqldata as $key => $value) {
      if (!$validate->check($value)) {
        $this->ajaxReturn(500, $validate->getError());
      }
    }
    model('CategoryDistrict')
      ->isUpdate()
      ->saveAll($sqldata);
    model('AdminLog')->record('批量保存地区分类', $this->admininfo);
    $this->ajaxReturn(200, '保存成功');
  }
}
