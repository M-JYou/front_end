<?php

namespace app\common\validate;

use \app\common\validate\BaseValidate;

class Bill extends BaseValidate {
  protected $rule =   [
    // 'id' => 'integer', // id,
    // 'addtime' => 'integer', // 创建时间,
    // 'create' => 'integer', // 创建人id,
    'account' => 'integer', // 归属账户,
    'date' => 'integer', // 时间,
    'desc' => 'max:255', // 255; 描述,
    'pay_type' => 'in:0,1,2', // 支付方式;0现金1支付宝2微信,
    'receivable' => 'integer', // 应收(单位分),
    'net_receipts' => 'integer', // 实收(单位分),
    'owe' => 'integer', // 欠收(单位分),
    'imgs' => 'max:2048', // 2048; ,
    'customer' => 'integer', // 往来单位,
    'mold' => 'integer', // 模式;0收入1支出,
    'other' => 'require|max:65535', // 65535; 其他json
  ];
}
