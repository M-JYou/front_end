<?php

namespace app\common\model;

class GoodsType2 extends \app\common\model\BaseModel {
  protected $readonly = ['id', 'addtime'];
  protected $type     = [
    'id'        => 'integer', // 'id',
    'status'    => 'integer', // '状态',
    'pid'       => 'integer', // '上级id',
    'addtime'   => 'integer', // '创建时间',
    'create'    => 'integer', // '创建人id',
    'name'      => 'string', // '名称',
    'simple'    => 'string', // '简介',
  ];
  protected $insert = ['addtime'];
  protected function setAddtimeAttr() {
    return time();
  }
}
