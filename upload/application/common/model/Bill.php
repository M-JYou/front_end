<?php

namespace app\common\model;


class Bill extends \app\common\model\BaseModel {
  protected $readonly = ['id', 'addtime', 'create', 'type', 'typec'];
  protected $insert = ['addtime'];
  protected function setAddtimeAttr() {
    return time();
  }
  protected $type     = [
    'id' => 'integer', // id,
    'addtime' => 'integer', // 创建时间,
    'create' => 'integer', // 创建人id,
    'account' => 'integer', // 归属账户,
    'date' => 'integer', // 时间,
    'desc' => 'string', // 255; 描述,
    'pay_type' => 'integer', // 支付方式;0现金1支付宝2微信,
    'receivable' => 'float', // 应收(单位分),
    'net_receipts' => 'float', // 实收(单位分),
    'owe' => 'float', // 欠收(单位分),
    'imgs' => 'array', // 2048; ,
    'customer' => 'integer', // 往来单位,
    'mold' => 'integer', // 模式;0收入1支出,
    'other' => 'array', // 65535; 其他json
  ];
  protected function getCustomer_Attr($v) {
    return model('Customer')->where('id', $v)->find();
  }
  protected function getAccount_Attr($v) {
    return model('Account')->where('id', $v)->find();
  }
  public function getImgsAttr($v) {
    return decode($v);
  }
  public function getReceivableAttr($v) {
    return number_format($v, 2, null, '');
  }
  public function getNetReceiptsAttr($v) {
    return number_format($v, 2, null, '');
  }
  public function getOweAttr($v) {
    return number_format($v, 2, null, '');
  }
}
