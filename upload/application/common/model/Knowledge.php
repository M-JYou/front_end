<?php

namespace app\common\model;

class Knowledge extends \app\common\model\BaseModel {
  protected $readonly = ['id'];
  protected $type = [
    'id' => 'integer',
    'type' => 'integer',
    'is_display' => 'integer',
    'power' => 'integer',
    'op'=>'integer',
    'point' => 'integer',
    'state' => 'integer',
    'time' => 'integer'
  ];
  // protected $insert = ['addtime'];
  // protected function setAddtimeAttr() {
  // return time();
  // }
}
