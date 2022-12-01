<?php

namespace app\apiadmin\controller;

class Goods extends \app\common\controller\Backend {

  public function find() {
    $d=input('param.');
    $this->_find($d,'*,`create` `create_`');
  }
  public function add() {
    // $this->ajaxReturn(500, '管理员不能新增');
    $d = input('post.');
    $d['create'] = 0;
    $d['sort'] = 0;
    $d['sort_id'] = 0;
    $d['enable_points_deduct'] = 1; // "可积分抵扣0否1可2部分" 商品常为1
    $d['deduct_max'] = 0; // "可部分抵扣最大额" 商品无效
    $d['is_display'] = $d['is_display'] ? 1 : 0; // "是否显示1是2否" 商品无效
    $this->_add($d);
  }
}
