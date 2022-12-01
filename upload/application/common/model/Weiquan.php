<?php

namespace app\common\model;

class Weiquan extends \app\common\model\BaseModel {
  protected $type     = [
    'id' => 'integer', //  BIGINT UNSIGNED id
    'addtime' => 'integer', //  INT UNSIGNED 创建时间
    'create' => 'integer', //  INT UNSIGNED 创建者
    'state' => 'integer', //  TINYINT UNSIGNED 状态
    'evidence' => 'array', //  TEXT 证据
    'mobile' => 'string', //  CHAR(16) 电话
    'reason' => 'string', //  TEXT 原因
    'content' => 'string', //  CHAR(255) 正文
  ];
  protected $readonly = ['id', 'addtime', 'create', 'type'];
  protected $insert = ['addtime'];
  protected function setAddtimeAttr() {
    return time();
  }
}
