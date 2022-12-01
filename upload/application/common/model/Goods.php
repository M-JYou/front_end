<?php

namespace app\common\model;

class Goods extends \app\common\model\BaseModel {
  protected $readonly = ['id', 'addtime', 'create', 'shop'];
  protected $type     = [
    'id' => 'integer', // INT id,
    'sort' => 'integer', // INT 排序,
    'addtime' => 'integer', // INT 创建时间,
    'create' => 'integer', // INT 创建人id,
    'shop' => 'integer', // INT 商铺id,
    'examine' => 'string', // CHAR(255) 审核文本,非空为异常,
    'type' => 'integer', // INT 类型,
    'type2' => 'integer', // INT 商户自定义类型,
    'sum' => 'integer', // INT 库存数量,
    'name' => 'string', // CHAR(32) 名称,
    'simple' => 'string', // CHAR(255) 简介,
    'content' => 'string', // TEXT 正文,
    'cover' => 'string', // CHAR(255) 封面,
    'banner' => 'array', // VARCHAR(510) 横幅,
    'expense' => 'integer', // decimal(10,2) 价格,
    'freight' => 'integer', // decimal(10,2) 运费,
    'enable_points_deduct' => 'integer', // TINYINT(1) 可积分抵扣0否1可2部,
    'deduct_max' => 'integer', // decimal(10,2) 可部分抵扣最大额,
    'is_display' => 'integer', // TINYINT(1) 是否显示1是2否,
    'sort_id' => 'integer', // INT 排序,
    'sales' => 'integer', // INT 排序,

    'seo_title' => 'integer', // CHAR(255) seo标题,
    'seo_desc' => 'integer', // CHAR(255) seo简介,
    'seo_keywords' => 'integer', // CHAR(255) seo关键字,
  ];
  protected $insert = ['addtime', 'deduct_max'];
  protected function setAddtimeAttr() {
    return time();
  }
  protected function setDeductMaxAttr($v, $d) {
    return $d['expense'];
  }
  protected function setEnablePointsDeductAttr() {
    return 1;
  }
  protected function setTypeAttr($v) {
    if (model('GoodsType')->find($v)) {
      return $v;
    }
    throw new \Exception("类型错误", 500);
  }
  protected function setType2Attr($v, $d) {
    if (isset($d['create'])) {
      if (model('GoodsType')->find($v)) {
        return $v;
      }
      throw new \Exception("类型错误", 500);
    }
    throw new \Exception("设置自定义商品类型时，需传递create", 500);
  }

  protected function getStoreAttr($v) {
    $ret = model('MemberInfo')->field('id,name,photo')->find($v);
    return $ret ? $ret
      : ['id' => 0, 'name' => '系统', 'photo' => '/upload/resource/empty_photo.png'];
  }
  protected function getShopAttr($v) {
    $v = decode($v);
    if (!is_array($v)) {
      $v = ['id' => $v,'#field'=>'*,`create` user'];
    }
    return $this->retOtherAttr($v);
  }

  public function getSureById($id, $field = '', $sum = ['>', 0]) {
    $where = [
      'id' => ['in', $id],
      'is_display' => ['=', 1],
      'examine' => ['=', ''],
    ];
    if ($sum) {
      $where['sum'] = ['>', 0];
    }
    return $this->where($where)->field($field ? $field : 'id,expense,examine,sum,is_display')->select();
  }
}
