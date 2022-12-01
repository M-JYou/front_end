<?php

namespace app\common\model;


class Customer extends \app\common\model\BaseModel {
  protected $readonly = ['id', 'create', 'type'];
  // protected $auto = ['addtime'];
  // protected function setAddtimeAttr() {
  //   return time();
  // }
  protected $type     = [
    'id' => 'integer', // id,
    'create' => 'integer', // 创建人id,
    'type' => 'integer', // 类型;1客户2供应商3收支大类,
    'name' => 'string', // 16; 名称,
    'headerpic' => 'string', // 64; 头像url,
    'account' => 'integer', // 归属账户,
    'other' => 'string', // 65535; 其他json
  ];
  protected function getCustomer_Attr($v) {
    return model('Customer')->where('id', $v)->find();
  }
  protected function getAccount_Attr($v) {
    return model('Account')->where('id', $v)->find();
  }
}
