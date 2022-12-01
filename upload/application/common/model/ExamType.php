<?php

namespace app\common\model;

class ExamType extends \app\common\model\BaseModel {
  protected $readonly = ['id', 'addtime', 'create'];
  protected $type     = [
    'id'          => 'integer',  // INT(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
    'addtime'     => 'integer',  // INT(10) UNSIGNED NOT NULL COMMENT '创建时间',
    'create'      => 'integer',  // INT(10) UNSIGNED NOT NULL COMMENT '创建人id',
    'pid'         => 'integer',   // INT(10) NOT NULL DEFAULT 0 COMMENT '上级id',
    'is_display'  => 'integer',  // TINYINT(1) NOT NULL DEFAULT 1 COMMENT '是否显示',
    'name'        => 'string',   // CHAR(32) NOT NULL COMMENT '名称',
    'simple'      => 'string',   // CHAR(255) NULL COMMENT '简介',
  ];
  protected $insert = ['addtime'];
  protected function setAddtimeAttr() {
    return time();
  }
  protected function getSimpleAttr($v) {
    return decode($v);
  }
}
