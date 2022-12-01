<?php

namespace app\common\validate;

class ExamType extends \app\common\validate\BaseValidate {
  protected $rule =   [
    'id'          => 'integer',           // INT(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
    'addtime'     => 'gt:0',              // INT(10) UNSIGNED NOT NULL COMMENT '创建时间',
    'create'      => 'gt:0',              // INT(10) UNSIGNED NOT NULL COMMENT '创建人id',
    'pid'         => 'integer',           // INT(10) NOT NULL DEFAULT 0 COMMENT '上级id',
    'is_display'  => 'integer',           // TINYINT(1) NOT NULL DEFAULT 1 COMMENT '是否显示',
    'name'        => 'max:32',            // CHAR(32) NOT NULL COMMENT '名称',
    'simple'      => 'max:255',           // CHAR(255) NULL COMMENT '简介',
  ];
}
