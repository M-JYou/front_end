<?php

namespace app\common\validate;

use \app\common\validate\BaseValidate;

class Cart extends BaseValidate {
  protected $rule =   [
    // 'id' => 'integer', // INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
    'addtime' => 'number', // INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
    'create' => '>:0', // INT(10) UNSIGNED NOT NULL COMMENT '创建人id',
    'status' => 'in:0,1', // TINYINT NOT NULL DEFAULT 0 COMMENT '状态;0:创建; 1:已完成,无法删除',
    // 'goods' => 'max:65535', // TEXT NOT NULL COMMENT '商品id数组',
    'expense' => '>=:0'
  ];
}
