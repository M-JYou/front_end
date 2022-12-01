<?php

namespace app\v1_0\controller\member;

class Cart extends \app\v1_0\controller\common\Base {
  public function _initialize() {
    parent::_initialize();
    $this->checkLogin();
  }
  public function find() {
    $param = input('param.');
    $param['create'] = $this->userinfo->uid;
    $this->_find($param);
  }
  public function add() {
    $pg = input('post.goods/a', []);
    try {
      if (!($pg && isset($pg['id']) && isset($pg['sum'])) && input('post.id/d') > 0 && ($sum = input('post.sum/d', 0, 'intval')) > 0) {
        $pg = [['id' => input('post.id/d'), 'sum' => $sum]];
      } else {
        throw null;
      }
      $t = [];
      foreach ($pg as $v) {
        if ($v['id'] > 0 && $v['sum'] > 0) {
          $t[] = ['id' => $v['id'], 'sum' => $v['sum']];
        }
      }
      if (count($t)) {
        $pg = $t;
      } else {
        throw null;
      }
    } catch (\Throwable $th) {
      ext(500, '参数错误', input('param.'));
    }

    $m = model($this->dbname);
    $s = clo($m->where([
      'status' => 0,
      'create' => $this->userinfo->uid,
    ])->order('id desc')->find());

    if ($s) {
      if (count($pg) == 1) {
        $pg = $pg[0];
        $t = findArr($s['goods'], $pg['id'], 'id', true);
        if ($t < 0) {
          $s['goods'][] = $pg;
        } else {
          $s['goods'][$t]['sum'] += $pg['sum'];
        }
      } else {
        $s['goods'] = $pg;
      }
      $m->update(['goods' => $s['goods']], ['id' => $s['id']]);
      ext(200, '成功加入购物车', $m->find($s['id']));
    }
    $param = [
      'create' => $this->userinfo->uid,
      'goods' => $pg,
    ];
    $this->_add($param);
  }
  public function edit() {
    $param = [
      'goods' => input('post.goods/a', []),
      'expense' => 0,
    ];
    $m = model($this->dbname);
    $tj = ['id' => input('post.id/d', 0, 'intval'), 'create' => $this->userinfo->uid];
    $m->update($param, $tj);
    if (input('?post.payment')) {
      $t = model('MemberInfo')->where('id', $this->userinfo->uid)
        ->field('addr')->find()['addr'][0];
      if ($t) {
        $r = model('Order')->addOrder([
          'uid' => $this->userinfo->uid,
          'service_type' => 'cart',
          'service_id' => $tj['id'],
          'note' => $t
        ]);
        $e = model('Order')->getError();
        if (!$e) {
          $e = $r ? "支付成功" : "未知错误";
        }

        ext($r ? 200 : 500, $e, $r);
      } else {
        ext(500, '没有找到地址信息,请先完善收货地址');
      }
    } else {
      ext(200, '修改成功', $m->where($tj)->find());
    }
  }
  public function delete() {
    $this->_delete([
      'id' => input('post.id'),
      'create' => $this->userinfo->uid,
      'status' => 0
    ]);
  }
}
