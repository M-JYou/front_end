<?php

namespace app\common\model;


class ServiceType extends \app\common\model\BaseModel {
  protected $readonly = ['id', 'addtime', 'create'];
  protected $insert = ['addtime'];
  protected function setAddtimeAttr() {
    return time();
  }
  protected $type     = [
    'id' => 'integer', // INT UNSIGNED AUTO_INCREMENT COMMENT 'id',
    'addtime' => 'integer', // INT UNSIGNED DEFAULT 0 COMMENT '创建时间',
    'name' => 'string', // CHAR(16) DEFAULT '' COMMENT '名称',
    'img' => 'string', // CHAR(64) DEFAULT '' COMMENT '图片url',
    'status' => 'string', // VARCHAR(1024) NOT NULL COMMENT '步骤名',
    'content' => 'string', // TEXT NOT NULL COMMENT '子菜单',
  ];
  protected function getStatusAttr($v = null) {
    return decode($v);
  }
  protected function setStatusAttr($v) {
    $v = decode($v);
    if (!is_array($v)) {
      $v = [];
    }
    $ret = [];
    foreach ($v as $vv) {
      $ret[] = $vv;
    }
    return encode($ret);
  }
  protected function setContentAttr($v) {
    $v = decode($v);
    if (!is_array($v)) {
      $v = [];
    }
    $ret = [];
    foreach ($v as $vv) {
      $ret[] = ['name' => $vv['name'], 'ico' => $vv['ico']];
    }
    return encode($ret);
  }
}
