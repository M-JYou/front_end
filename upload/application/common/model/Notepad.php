<?php

namespace app\common\model;

class Notepad extends \app\common\model\BaseModel {
  protected $readonly = ['id', 'addtime', 'create'];
  protected $type     = [
    'id'              => 'integer', // INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
    'addtime'         => 'integer', // INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
    'time'            => 'string', // INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
    'create'          => 'integer', // INT(10) UNSIGNED NOT NULL COMMENT '创建人id',
    'content'         => 'string',  // MEDIUMTEXT NOT NULL COMMENT '正文富文本',
  ];
  protected $insert = ['addtime'];
  protected function setAddtimeAttr() {
    return time();
  }
}
