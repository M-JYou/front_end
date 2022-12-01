<?php

namespace app\common\model;

class StudyType extends \app\common\model\BaseModel {
  protected $readonly = ['id', 'create'];
  protected $type     = [
    'id' => 'integer', //  INT UNSIGNED id
    'addtime' => 'integer', //  INT UNSIGNED 创建时间
    'create' => 'integer', //  INT UNSIGNED 创建人id
    'is_display' => 'integer', //  TINYINT 是否显示
    'name' => 'string', //  CHAR(32) 名称
    'simple' => 'string', //  VARCHAR(1024) 简介
  ];
  protected $auto = ['addtime'];
  protected function setAddtimeAttr() {
    return time();
  }
  protected function getSimpleAttr($value) {
    return decode($value);
  }
}
