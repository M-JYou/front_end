<?php

namespace app\common\model;

class Remid extends \app\common\model\BaseModel {
  protected $readonly = ['id', 'addtime', 'create'];
  protected $type     = [
    'id' => 'integer', // INT UNSIGNED AUTO_INCREMENT COMMENT 'id',
    'addtime' => 'integer', // INT UNSIGNED DEFAULT 0 COMMENT '创建时间',
    'date' => 'string', // CHAR(16) DEFAULT '' COMMENT '日期',
    'time' => 'string', // CHAR(16) DEFAULT '' COMMENT '时间',
    'type' => 'integer', // INT UNSIGNED DEFAULT 0 COMMENT '类型',
    'create' => 'integer', // INT UNSIGNED COMMENT '创建人id',
    'content' => 'string', // TEXT COMMENT '正文json',
  ];
  protected $insert = ['addtime'];
  protected function setAddtimeAttr() {
    return time();
  }
}
