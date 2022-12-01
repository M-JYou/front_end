<?php

namespace app\common\model;


class Account extends \app\common\model\BaseModel {
  protected $readonly = ['id', 'addtime', 'create', 'type', 'typec'];
  protected $insert = ['addtime'];
  protected function setAddtimeAttr() {
    return time();
  }
  protected $type     = [
    'id' => 'integer', // id,
    'addtime' => 'integer',
    'create' => 'integer', // 创建人id,
    'name' => 'string', // 16; 账户名称,
    'merchant' => 'string', // 16; 商户名称,
    'contacts' => 'string', // 16; 联系人,
    'mobile' => 'string', // 16; 手机号,
    'address' => 'integer', // 地址id,
    'address_info' => 'string', // 255; 地址详情,
    'business_license' => 'string', // 64; 营业执照url,
    'idcard' => 'string', // 20; 身份证号码,
    'idcard_a' => 'string', // 64; 身份证a URL,
    'idcard_b' => 'string', // 64; 身份证b URL,
    'tel' => 'string', // 64; 身份证b URL,
  ];
  protected function getCustomer_Attr($v) {
    return model('Customer')->where('id', $v)->find();
  }
  protected function getAccount_Attr($v) {
    return model('Account')->where('id', $v)->find();
  }
}
