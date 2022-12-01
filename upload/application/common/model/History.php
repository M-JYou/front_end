<?php

namespace app\common\model;

class History extends \app\common\model\BaseModel {
  protected $readonly = ['id', 'create', 'model', 'mid'];
  protected $type = [
    'id' => 'integer', // INT UNSIGNED AUTO_INCREMENT COMMENT 'id',
    'addtime' => 'integer', // INT UNSIGNED DEFAULT 0 COMMENT '创建时间',
    'create' => 'integer', // INT UNSIGNED COMMENT '创建人id',
    'model' => 'string', // CHAR(16) COMMENT '模块名,帕斯卡,首字母大写驼峰',
    'mid' => 'string', // CHAR(32) COMMENT '模块对应Id',
  ];
  protected $auto = ['addtime'];
  protected function setAddtimeAttr() {
    return time();
  }

  public function adddata(array $d) {
    $ret = null;
    try {
      try {
        $logic = $d['create'] && $d['model'] && $d['mid'];
      } catch (\Throwable $th) {
        $logic = false;
      }
      if ($logic) {
        $ret = $this->create($d);
      }
    } catch (\Throwable $th) {
      $ret = $this->where($d)->field('id')->find();
      if ($ret) {
        $this->update(['addtime' => 0], ['id' => $ret['id']]);
      }
    }
    return $ret;
  }
}
