<?php

namespace app\common\validate;

use \app\common\validate\BaseValidate;

class Customer extends BaseValidate {
  protected $rule =   [
    // 'id' => 'integer', // id,
    'create' => 'integer', // 创建人id,
    'type' => 'integer', // 类型;1客户2供应商3收支大类,
    'name' => 'max:16', // 16; 名称,
    'headerpic' => 'max:64', // 64; 头像url,
    'account' => 'integer', // 归属账户,
    'other' => 'require|max:65535', // 65535; 其他json
  ];
}
