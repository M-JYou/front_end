<?php

namespace app\v1_0\controller\member;

class Order extends \app\v1_0\controller\common\Base {
  public function _initialize() {
    parent::_initialize();
    $this->checkLogin();
  }
  public function find() {
    $data = input('param.');
    if ($this->userinfo->utype == 1 && isset($data['goods']) && is_array($data['goods'])) {
      $gw = $data['goods'];
      $gw['create'] = $this->userinfo->uid;
      $mg = model('Goods')->where($gw)->column('id');
      $data['service_id'] = ['in', $mg];
    } else {
      $data['uid'] = $this->userinfo->uid;
    }
    $this->__find($data, null, 'id desc', '*,service_type as other');
  }
  public function add() {
    $data = input('post.');
    $data['utype'] = $this->userinfo->utype;
    $data['uid'] = $this->userinfo->uid;
    $m = model('Order');
    $r = $m->addOrder($data);
    $e = $m->getError();
    $this->ajaxReturn($e ? 500 : 200, $e ? $e : '增加订单成功', $r);
  }
  public function edit() {
    $this->ajaxReturn(500, '不支持订单修改，请关闭后重新下单');
  }
  public function delete() {
    $mm = model('Order');
    $r = $mm->orderClose(input('post.id'),  $this->userinfo->uid);
    $e = $mm->getError();
    $this->ajaxReturn($e ? 500 : 200, $e ? $e : '关闭成功', $r);
  }
}
