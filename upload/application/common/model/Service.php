<?php

namespace app\common\model;


class Service extends \app\common\model\BaseModel {
  protected $readonly = ['id', 'addtime', 'create', 'type', 'typec'];
  protected $insert = ['addtime'];
  protected $auto = ['change_time'];
  protected function setAddtimeAttr() {
    return time();
  }
  protected function setChangeTimeAttr() {
    return time();
  }
  protected $type     = [
    'id' => 'integer', // INT UNSIGNED AUTO_INCREMENT COMMENT 'id',
    'addtime' => 'integer', // INT UNSIGNED DEFAULT 0 COMMENT '创建时间',
    'change_time' => 'integer', // INT UNSIGNED DEFAULT 0 COMMENT '修改时间',
    'create' => 'integer', // INT UNSIGNED NOT NULL COMMENT '创建人id',
    'status' => 'integer', // TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态',
    'type' => 'integer', // INT UNSIGNED NOT NULL COMMENT '类型',
    'typec' => 'string', // CHAR(16) NOT NULL COMMENT '子类型',
    'points' => 'integer', // INT UNSIGNED NOT NULL COMMENT '价格',
    'files' => 'string', // TEXT NOT NULL COMMENT '文件json',
    'content' => 'string', // TEXT NOT NULL COMMENT '正文json',
  ];
  protected function getFilesAttr($v = null) {
    return decode($v);
  }
  protected function setFilesAttr($v) {
    $v = decode($v);
    if (!is_array($v)) {
      $v = [];
    }
    $ret = [];
    foreach ($v as $vv) {
      $ret[] = ['name' => $vv['name'], 'url' => $vv['url']];
    }
    return encode($ret);
  }
  protected function getNameAttr($v) {
    return model('MemberInfo')->where('id', $v)->field('name')->find()->getData('name');
  }
  protected function getType_Attr($v) {
    return model($this->name . 'Type')->where('id', $v)->field('name')->find()->getData('name');
  }
}
