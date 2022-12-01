<?php

namespace app\common\validate;

use \app\common\validate\BaseValidate;

class Notepad extends BaseValidate {
  protected $rule =   [
    // 'id'              => '>:0',         // INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
    'addtime' => 'require|>:0', // INT UNSIGNED DEFAULT 0 COMMENT '创建时间',
    'time'=>'require|max:16', // CHAR(16) DEFAULT '' COMMENT '时间',
    'create' => 'require|>:0', // INT UNSIGNED COMMENT '创建人id',
    'content' => 'require', // TEXT COMMENT '正文json',
  ];
}
