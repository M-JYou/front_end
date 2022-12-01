<?php

namespace app\common\model;

/** 商标求购 */
class TrademarkBuy extends \app\common\model\BaseModel {
  protected $readonly = ['id', 'addtime', 'create', 'type'];
  protected $insert = ['addtime'];
  // protected $auto = ['change_time'];
  protected function setAddtimeAttr() {
    return time();
  }
  protected function setChangeTimeAttr() {
    return time();
  }
  protected $type     = [
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
    'tra_type' => 'string', // CHAR(16) NOT NULL DEFAULT '' COMMENT '注册类型',
    'type' => 'integer', // INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '注册类别',
    'contacts' => 'string', // CHAR(64) NOT NULL DEFAULT '' COMMENT '联系人',
    'tel' => 'string', // CHAR(16) NOT NULL DEFAULT '' COMMENT '联系人电话',
    'content' => 'string', // TEXT NOT NULL COMMENT '其他说明',
  ];

  protected function getType_Attr($v) {
    return model('TrademarTransferType')
      ->where('id', $v)
      ->field('name')->find()->getData('name');
  }
}
