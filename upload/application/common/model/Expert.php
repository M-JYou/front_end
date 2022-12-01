<?php

namespace app\common\model;

class Expert extends \app\common\model\BaseModel {
  protected $readonly = ['id', 'addtime', 'create'];
  protected $type = [
    'id' => 'integer', // INT(10) UNSIGNED NOT NULL COMMENT 'id',
    'addtime' => 'integer', // INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
    'is_display' => 'integer', // TINYINT NOT NULL DEFAULT 1 COMMENT '软删除',
    'create' => 'integer', // INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '归属用户id',

    'name' => 'string', // CHAR(32) NOT NULL DEFAULT '' COMMENT '名称',
    'cover' => 'string', // CHAR(255) NOT NULL DEFAULT '' COMMENT '封面url',
    'content' => 'string', // TEXT NOT NULL COMMENT '正文',
    'tel' => 'string', // CHAR(16) NOT NULL DEFAULT '' COMMENT '电话',
    'category_district' => 'integer', // INT UNSIGNED NOT NULL COMMENT '行政区域id',
    'trade' => 'integer', // INT UNSIGNED NOT NULL COMMENT '行业',
    'other' => 'array', // TEXT NOT NULL COMMENT '其他json',
  ];
  protected $insert = ['addtime'];
  protected function setAddtimeAttr() {
    return time();
  }
}
