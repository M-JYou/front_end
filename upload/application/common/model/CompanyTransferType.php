<?php

namespace app\common\model;

/** 公司转让类型 */
class CompanyTransferType extends \app\common\model\BaseModel {
  protected $readonly = ['id', 'addtime'];
  protected $insert = ['addtime'];
  protected function setAddtimeAttr() {
    return time();
  }
  protected $type     = [
    'id' => 'integer', // INT UNSIGNED NOT NULL COMMENT 'id',
    'pid' => 'integer', // INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '上级id',
    'addtime' => 'integer', // INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
    'is_display' => 'integer', // TINYINT NOT NULL DEFAULT 1 COMMENT '软删除',
    'name' => 'string', // CHAR(32) NOT NULL DEFAULT '' COMMENT '名称',
  ];
}
