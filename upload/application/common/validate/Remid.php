<?php

namespace app\common\validate;

use \app\common\validate\BaseValidate;

class Remid extends BaseValidate {
  protected $rule =   [
    // 'id'              => '>:0',         // INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
    'addtime' => 'require|>:0', // INT UNSIGNED DEFAULT 0 COMMENT '创建时间',
    'create' => 'require|>:0', // INT UNSIGNED COMMENT '创建人id',
    'date' => 'require|max:16', // CHAR(16) DEFAULT '' COMMENT '日期',
    'time' => 'require|max:16', // CHAR(16) DEFAULT '' COMMENT '时间',
    'type' => 'require', // INT UNSIGNED DEFAULT 0 COMMENT '类型',
    'content' => 'require', // TEXT COMMENT '正文',
  ];
}
