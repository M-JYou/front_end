<?php

namespace app\common\model;

class Imgs extends \app\common\model\BaseModel {
  protected $readonly = ['id'];
  protected $type     = [
    'id' => 'integer', //  INT UNSIGNED id
    'type' => 'string', //  CHAR(64) 名称
    'sort' => 'integer', //  INT UNSIGNED 排序
    'name' => 'string', //  CHAR(64) 名称
    'url' => 'string', //  CHAR(255) 地址
  ];
}
