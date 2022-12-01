<?php

namespace app\common\validate;

use \app\common\validate\BaseValidate;

class Bank extends BaseValidate {
  protected $rule =   [
    // 'id' => 'integer', // INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
    'addtime' => 'integer', // INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
    'is_display' => 'in:0,1', // TINYINT NOT NULL DEFAULT 1 COMMENT '软删除',
    'name' => 'max:32', // CHAR(32) NOT NULL DEFAULT '' COMMENT '名称',
    'cover' => 'max:255', // CHAR(255) NOT NULL DEFAULT '' COMMENT '封面url',
    'content' => 'max:65535', // TEXT NOT NULL COMMENT '正文json',
    'server' => 'max:64', // CHAR(64) NOT NULL DEFAULT '' COMMENT '客服id',
    'tel' => 'max:16', // CHAR(16) NOT NULL DEFAULT '' COMMENT '电话',
    'category_district' => 'integer', // INT UNSIGNED NOT NULL COMMENT '行政区域id',
    'pid' => 'integer', // INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '上级id',
  ];
}
