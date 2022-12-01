<?php

namespace app\common\validate;

use \app\common\validate\BaseValidate;

class MemberInfo extends BaseValidate {
  protected $rule =   [
    'id'              => '>:0',       // INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
    'name'            => 'max:64',    // CHAR(64) NOT NULL DEFAULT '' COMMENT '名称',
    'photo'           => 'max:128',   // CHAR(128) NOT NULL DEFAULT '' COMMENT '头像',
    'addr'            => 'max:2048',  // VARCHAR(2048) NOT NULL DEFAULT '[]' COMMENT '地址',
  ];
}
