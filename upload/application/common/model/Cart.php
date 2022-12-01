<?php

namespace app\common\model;

class Cart extends \app\common\model\BaseModel {
  protected $readonly = ['id', 'addtime', 'create'];
  protected $type = [
    'id' => 'integer', // INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
    'addtime' => 'integer', // INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
    'create' => 'integer', // INT(10) UNSIGNED NOT NULL COMMENT '创建人id',
    'status' => 'integer', // TINYINT NOT NULL DEFAULT 0 COMMENT '状态;0:创建; 1:已完成,无法删除',
    'goods' => 'array', // TEXT NOT NULL COMMENT '商品id数组',
    'expense' => 'integer'
  ];
  protected $insert = ['addtime', 'expense', 'status' => 0];
  protected function setAddtimeAttr() {
    return time();
  }
  protected function setGoodsAttr($v, $d) {
    $v = parseArr($v);
    $r = [];
    $g = model('Goods')->getSureById(getArrField($v), null, null);
    $sum = 0;
    foreach ($g as $vv) {
      $t = findArr($v, $vv['id']);
      if ($t) {
        $r[] = ['id' => $vv['id'], 'sum' => $t['sum']];
        $sum += $vv['expense'] * $t['sum'];
      }
    }
    if (isset($d['id'])) {
      $this->allowField(true)
        ->update(['expense' => $sum, '#' => 1], ['id' => $d['id']]);
    }
    return $r;
  }
  protected function setExpenseAttr($v, $d) {
    if (isset($d['#'])) {
      $this->allowField(true);
      return $v;
    }
    $goods = decode($d['goods']);
    $g = model('Goods')->where('id', 'in', getArrField($goods))
      ->field('id,expense,freight')
      ->select();
    $r = 0;
    foreach ($goods as $vv) {
      $t = findArr($g, $vv['id']);
      if ($t) {
        $r += $vv['sum'] * $t['expense'] + $t['freight'];
      }
    }
    return $r;
  }
  protected function getGoodsAttr($v) {
    if (!is_array($v)) {
      try {
        $v = json_decode($v, true);
      } catch (\Throwable $th) {
        $v = [];
      }
    }
    $r = [];
    foreach ($v as $d) {
      $r[] = $d['id'];
    }
    $r = model('Goods')->where('id', 'in', $r)->field('id,create,name,simple,expense,freight,cover')->select();
    foreach ($r as $k => $vv) {
      $vv['sum'] = findArr($v, $vv['id'])['sum'];
      $r[$k] = $vv;
    }
    foreach ($v as $vv) {
      if (!findArr($r, $vv['id'])) {
        $vv['name'] = '商品已下架,无法查询原始信息';
        $r[] = $vv;
      }
    }
    return $r;
  }
}
