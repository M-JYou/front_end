<?php

namespace app\common\model;

class At extends \app\common\model\BaseModel {
  protected $readonly = ['id'];
  protected $type = [
    'id' => 'integer',
    'd' => 'integer',
  ];
  public function c($param = null) {
    // return $this->data($param)->validate(true)->allowField(true)->save();
    return $this->create($param);
  }
}
