<?php

namespace app\common\validate;

class Exam extends \app\common\validate\BaseValidate {
  protected $rule =   [
    'pid'       => 'require',         // '上级id',
    // 'status'    => 'in:0,1,3',          // '状态',
    // 'addtime'   => 'require|gt:0',    // '创建时间',
    'create'    => 'require|gt:0',    // '创建人id',
    'name'      => 'require|max:32',  // '名称',
    'simple'    => 'require|max:255', // '简介',

    'addtime'     => 'require|gt:0',   // INT(10) UNSIGNED NOT NULL COMMENT '创建时间',
    'is_display'  => 'in:0,1,2,3',   // TINYINT(1) NOT NULL DEFAULT 1 COMMENT '是否显示',
    'type'        => 'gt:0',   // INT(10) NOT NULL COMMENT '类型',
    'create'      => 'gt:0',   // INT(10) UNSIGNED NOT NULL COMMENT '创建人id',
    'name'        => 'require|max:32',    // CHAR(32) NOT NULL COMMENT '名称',
    'content'     => 'array',    // MEDIUMTEXT NOT NULL COMMENT '试题内容',
  ];
}
