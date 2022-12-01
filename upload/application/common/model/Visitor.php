<?php

namespace app\common\model;

class Visitor extends \app\common\model\BaseModel {
  protected $readonly = ['id', 'create', 'model', 'mid'];
  protected $type     = [
    'id'              => 'integer', // INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
    'addtime'         => 'integer', // INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
    'create'          => 'integer', // INT(10) UNSIGNED NOT NULL COMMENT '创建人id',
    'model'           => 'string',
    'mid'             => 'integer'
  ];
  protected $auto = ['addtime'];
  protected function setAddtimeAttr() {
    return time();
  }

}
