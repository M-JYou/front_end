<?php

namespace app\common\validate;

use \app\common\validate\BaseValidate;

class Account extends BaseValidate {
  protected $rule =   [
    'id' => 'integer', // id,
    'create' => 'integer', // 创建人id,
    'name' => 'max:16', // 16; 账户名称,
    'merchant' => 'max:16', // 16; 商户名称,
    'contacts' => 'max:16', // 16; 联系人,
    'mobile' => 'max:16', // 16; 手机号,
    'address' => 'integer', // 地址id,
    'address_info' => 'max:255', // 255; 地址详情,
    'business_license' => 'max:64', // 64; 营业执照url,
    'idcard' => 'max:20', // 20; 身份证号码,
    'idcard_a' => 'max:64', // 64; 身份证a URL,
    'idcard_b' => 'max:64', // 64; 身份证b URL,
  ];
}
