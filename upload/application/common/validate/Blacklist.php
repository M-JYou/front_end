<?php

namespace app\common\validate;

use \app\common\validate\BaseValidate;

class Blacklist extends BaseValidate {
  protected $rule =   [
    // 'id'              => '>:0',         // INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
    'addtime' => 'require|>:0', // INT UNSIGNED DEFAULT 0 COMMENT '创建时间',
    'create' => 'require|>:0', // INT UNSIGNED COMMENT '创建人id',
    'mid' => 'require|max:32', // CHAR(32) COMMENT '模块对应Id',
  ];
}
