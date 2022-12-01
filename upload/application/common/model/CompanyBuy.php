<?php

namespace app\common\model;

/** 公司转让 */
class CompanyBuy extends \app\common\model\BaseModel {
  protected $readonly = ['id', 'addtime', 'create'];
  protected $insert = ['addtime'];
  // protected $auto = ['change_time'];
  protected function setAddtimeAttr() {
    return time();
  }
  protected function setChangeTimeAttr() {
    return time();
  }
  protected $type = [
    'id' => 'integer', // INT UNSIGNED NOT NULL COMMENT 'id',
    'addtime' => 'integer', // INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
    'change_time' => 'integer', // INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
    'click' => 'integer', // INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '浏览量',
    'create' => 'integer', // INT UNSIGNED NOT NULL COMMENT '创建者id',
    'is_display' => 'integer', // TINYINT NOT NULL DEFAULT 1 COMMENT '软删除',
    'name' => 'string', // CHAR(32) NOT NULL DEFAULT '' COMMENT '名称',
    'min' => 'integer', // INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '价格',
    'max' => 'integer', // INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '价格',
    'reg_time' => 'integer', // INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '成立时间',
    'end_time' => 'integer', // INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '有效期至',
    'type' => 'integer', // INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '所属行业',
    'regcap' => 'string', // CHAR(16) NOT NULL DEFAULT '' COMMENT '注册资本',
    'business' => 'string', // VARCHAR(1024) NOT NULL DEFAULT '' COMMENT '经营范围',
    'taxpayer' => 'string', // CHAR(16) NOT NULL DEFAULT '' COMMENT '纳税人类型',
    'addr' => 'integer', // INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '注册地',
    'contacts' => 'string', // CHAR(64) NOT NULL DEFAULT '' COMMENT '联系人',
    'tel' => 'string', // CHAR(16) NOT NULL DEFAULT '' COMMENT '电话',
    'content' => 'string', // TEXT NOT NULL COMMENT '其他说明',
    'other' => 'array', // TEXT NOT NULL COMMENT '其他',
  ];
  protected function getType_Attr($v) {
    return model('CompanyTransferType')
      ->where('id', $v)
      ->field('name')->find()->getData('name');
  }
}
