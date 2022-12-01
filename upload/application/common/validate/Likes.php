<?php

namespace app\common\validate;

use \app\common\validate\BaseValidate;

class Likes extends BaseValidate {
  protected $rule =   [
    // 'id'              => '>:0',         // INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
    'addtime' => 'require|>:0', // INT UNSIGNED DEFAULT 0 COMMENT '创建时间',
    'create' => 'require|>:0', // INT UNSIGNED COMMENT '创建人id',
    'model' => 'require|max:16', // CHAR(16) COMMENT '模块名,帕斯卡,首字母大写驼峰',
    'mid' => 'require|max:32', // CHAR(32) COMMENT '模块对应Id',
  ];
}
