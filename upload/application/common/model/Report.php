<?php

namespace app\common\model;

class Report extends \app\common\model\BaseModel {
  protected $readonly = ['id', 'addtime', 'create', 'model', 'mid'];
  protected $type = [
    'id' => 'integer', // INT UNSIGNED AUTO_INCREMENT COMMENT 'id',
    'addtime' => 'integer', // INT UNSIGNED DEFAULT 0 COMMENT '创建时间',
    'create' => 'integer', // INT UNSIGNED COMMENT '创建人id',
    'model' => 'string', // CHAR(16) COMMENT '模块名,帕斯卡,首字母大写驼峰',
    'mid' => 'string', // CHAR(32) COMMENT '模块对应Id',
    'content' => 'string', // TEXT COMMENT '正文',
  ];
  protected $insert = ['addtime'];
  protected function setAddtimeAttr() {
    return time();
  }
}
