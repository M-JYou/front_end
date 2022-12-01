<?php

namespace app\common\model;

/** 证书 */
class Certificate extends \app\common\model\BaseModel {
  protected $readonly = ['id', 'addtime', 'create'];
  protected $type     = [
    'id' => 'integer', // INT UNSIGNED AUTO_INCREMENT COMMENT 'id',
    'addtime' => 'integer', // INT UNSIGNED DEFAULT 0 COMMENT '创建时间',
    'create' => 'integer', // INT UNSIGNED NOT NULL COMMENT '创建人id',
    'cover' => 'string', // CHAR (255) NOT NULL DEFAULT '' COMMENT '证书url',
    'content' => 'string', // TEXT NOT NULL COMMENT '正文内容',
    'other' => 'string', // TEXT NOT NULL COMMENT '其他json',
  ];
  protected $insert = ['addtime', 'sort_id' => 0];
  protected function setAddtimeAttr() {
    return time();
  }
  protected function setEntertainAttr($value) {
    if ($value != 0 && !model('Entertain')->get($value)) {
      throw new \Exception('不存在问题id:' . $value, 500);
    }
    return $value;
  }
}
