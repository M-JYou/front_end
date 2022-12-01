<?php

namespace app\v1_0\controller\member;

class Goods extends \app\v1_0\controller\common\Base {
  public function _initialize() {
    parent::_initialize();
    // $this->checkLogin(1);
  }
  private $field = '*,`create` as `store`';
  private $order = 'sort_id desc';
  protected function cud(&$p = null) {
    $this->checkLogin(1);
  }
  public function find() {
    $param = input('param.');
    if (!(isset($param['create']) && $param['create'] == $this->userinfo->uid)) {
      $param['is_display'] = 1;
    }
    $param['examine'] = '';

    $this->_find($param, $this->field, $this->order);
  }
  public function add() {
    $param = input('post.');
    $param['create'] = $this->userinfo->uid;
    $shop = model('Shop')->where(['create' => $this->userinfo->uid, 'is_display' => 1])->field('id')->find();
    if (!$shop) {
      ext(500, '商铺未开业', null);
    }
    $param['shop'] = $shop['id'];
    $param['sort'] = 0;
    $param['examine'] = "";
    $param['sort_id'] = 0;
    $param['enable_points_deduct'] = 1; // "可积分抵扣0否1可2部分" 商品常为1
    $param['deduct_max'] = 0; // "可部分抵扣最大额" 商品无效
    $param['is_display'] = $param['is_display'] > 0 ? $param['is_display'] : 1; // "是否显示1是2否" 商品无效

    $this->ajaxReturn(200, '新增成功', model('Goods')->c($param));
  }
  public function edit() {
    $param = input('post.');
    $this->_edit($param, ['id' => input('post.id'), 'create' => $this->userinfo->uid]);
  }
  public function delete() {
    $this->_delete(['id' => input('post.id'), 'create' => $this->userinfo->uid]);
  }
  public function guess() {
    ext(200, '获取猜你喜欢完毕', model($this->dbname)->where(
      'type',
      'in',
      model($this->dbname)->where(
        'id',
        'in',
        model('History')->where([
          'model' => $this->dbname,
          'create' => $this->userinfo->uid,
        ])->column('id')
      )->group('type')->column('type')
    )->field($this->field)->order($this->order)->limit(20)->select());
  }
}
