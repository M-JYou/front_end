<?php

/** 合同 */

namespace app\v1_0\controller\member;

class Contract extends \app\v1_0\controller\common\Base {
  public $type = [
    '代招人才',
    '代找工作',
    '代找账',
    '帮找实习单位',
    '线下实操培训',
    '财务外包',
    '包就业',
    '劳务派遣用工',
    '劳务派遣就业',
    '人才测评',
    '个人背景调查',
    '单位背景调查',
  ];
  public $state = [
    '上传合同',
    '费用支付托管',
    '付款',
    '延期付款',
    '申请退款',
    '同意付款',
    '完结'
  ];

  public function _initialize() {
    parent::_initialize();
  }
  public function find() {
    $d = $this->getData();
    $this->_find($d, '*', 'addtime desc');
  }
  protected function getData() {
    $this->checkLogin();
    $ret = input('param.');
    if (isset($ret['type']) && !in_array($ret['type'], $this->type)) {
      ext(500, '类型值错误', $this->type);
    }
    $ret['create'] = $this->userinfo->uid;
    return $ret;
  }
  public function add() {
    $ret = $this->getData();
    if (isset($ret['id'])) {
      $this->addContract();
    }
    $t = ['state', 'status', 'order', 'points'];
    foreach ($t as $v) {
      $ret[$v] = 0;
    }
    $ret['state'] = $this->state[0];
    $ret['appraise'] = '';
    $files = [];
    foreach ($ret['files'] as $v) {
      $files[] = ['name' => $v['name'], 'url' => $v['url']];
    }
    $ret['files'] = $files;

    if (!$ret['files'] || !is_array($ret['files'])) {
      ext(500, '必须为数组');
    }
    if (!in_array($ret['type'], $this->type, true)) {
      ext(500, 'type值为数组成员', $this->type);
    }
    $this->_add($ret);
  }
  public function edit() {
    ext(500, '无此接口');
  }
  public function delete() {
    ext(500, '无此接口');
  }
  // 增加合同
  public function addContract() {
    $id = input('post.id/d', 0, 'intval');
    $f = input('post.files/a', '', 'trim');
    if ($f && ($d = model($this->dbname)->where([
      'id' => $id,
      'create' => $this->userinfo->uid,
      'state' => '上传合同'
    ])->find())) {
      $d['files'] = decode($d['files']);
      foreach ($f as $v) {
        if (is_string($v['name']) && is_string($v['url']) && !findArr($d['files'], $v['url'], 'url')) {
          $d['files'][] = ['name' => $d['name'], 'url' => $d['url']];
        }
      }
      model($this->dbname)->update(['files' => $d['files']], ['id' => $id]);
      ext(200, '增加合同完成', $d);
    }
    ext(500, '数据格式错误或当前状态无法上传合同');
  }
  // 评价
  public function appraise() {
    $id = input('post.id/d', 0, 'intval');
    $f = input('post.appraise/s', '', 'trim');
    if ($f && ($d =
      model($this->dbname)
      ->where('create', $this->userinfo->uid)
      ->where('id', $id)
      ->where('state', 6)
      ->where('appraise', '')
      ->find())) {
      $d['files'] = decode($d['files']);
      $d['files'][] = $f;
      model($this->dbname)->update(['files' => $d['files']], ['id' => $id]);
      ext(200, '增加合同完成', $d);
    }
    ext(500, '数据格式错误或当前状态无法评价');
  }
  // 付费
  public function pay() {
    $c = 500;
    $e = '需要用户登录';
    if ($this->userinfo->uid) {
      $e = '当前合同状态无法支付';
      $d = model($this->dbname)->where([
        'id' => input('post.id/d', 0, 'intval'),
        'state' => '费用支付托管'
      ])->find();
      if ($d) {
        try {
          model('MemberPoints')->setPoints([[
            'uid' => $this->userinfo->uid,
            'points' => $d['points'],
            'note' => '用户【' . $this->userinfo->uid
              . '】支付合同费用:' . $d['id'],
          ]]);
          model($this->dbname)->update(['state' => '付款'], ['id' => $d['id']]);
          $c = 200;
          $e = '付款成功';
        } catch (\Throwable $th) {
          $e = $th->getMessage();
          $c = $th->getCode();
        }
      }
    }
    ext($c, $e, $d);
  }
  // 延期
  public function sleep() {
    $m = model($this->dbname);
    $d = $m->where([
      'id' => input('post.id/d', 0, 'intval'),
      'create' => $this->userinfo->uid,
      'state' => '付款'
    ])->find();
    $c = 500;
    $e = '当前状态无法延期';
    if ($d) {
      $m->update([
        'state' => '延期付款',
        'endtime' => $d['endtime'] + 604800
      ], ['id' => $d['id']]);
      $c = 200;
      $e = '已延期7天';
    }
    ext($c, $e, $d);
  }
  // 申请退款
  public function refund() {
    $c = 500;
    $e = '当前状态无法申请退款';
    $m = model($this->dbname);
    $d = $m->where([
      'id' => input('post.id/d', 0, 'intval'),
      'create' => $this->userinfo->uid,
      'state' => ['in', ['付款', '延期付款']]
    ])->find();
    if ($d) {
      $m->update(['state' => '申请退款'], ['id' => $d['id']]);
      $c = 200;
      $e = '已申请退款';
    }
    ext($c, $e, $d);
  }
  // 同意付款
  public function agree() {
    $c = 500;
    $e = '当前状态无法 同意付款';
    $m = model($this->dbname);
    $d = $m->where([
      'id' => input('post.id/d', 0, 'intval'),
      'create' => $this->userinfo->uid,
      'state' => ['in', ['付款', '延期付款', '申请退款']]
    ])->find();
    if ($d) {
      $m->update(['state' => '同意付款'], ['id' => $d['id']]);
      $c = 200;
      $e = '已 同意付款';
    }
    ext($c, $e, $d);
  }

  public function type() {
    ext(200, '获取类型成功', $this->type);
  }
}
