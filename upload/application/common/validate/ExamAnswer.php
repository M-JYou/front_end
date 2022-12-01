<?php

namespace app\common\validate;

class ExamAnswer extends \app\common\validate\BaseValidate {
  protected $rule =   [
    'id'          => 'integer',   // INT(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
    'addtime'     => '>:0',       // INT(10) UNSIGNED NOT NULL COMMENT '创建时间',
    'create'      => '>:0',       // INT(10) UNSIGNED NOT NULL COMMENT '创建人id',
    'exam'        => '>:0',       // INT(10) NOT NULL COMMENT '题库id',
    'note'        => 'max:255',   // CHAR(255) NOT NULL COMMENT '备注',
    'name'        => 'max:32',    // CHAR(32) NOT NULL COMMENT '名称',
    'content'     => 'array',     // MEDIUMTEXT NOT NULL COMMENT '试题内容',
    'score'       => '>:0',       // INT(10) NOT NULL COMMENT '总分',
    'check'       => '>=:0',      // INT(10) NOT NULL COMMENT '批改分数',
    'correct'     => '>=:0',      // INT(10) NOT NULL COMMENT '得分',
  ];
}
