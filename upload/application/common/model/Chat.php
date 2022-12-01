<?php

namespace app\common\model;


class Chat extends \app\common\model\BaseModel {
  protected $readonly = ['id'];
  protected $insert = ['addtime'];
  protected function setAddtimeAttr() {
    return time();
  }
  protected $type     = [
    'id' => 'integer', //  BIGINT UNSIGNED id
    'addtime' => 'integer', //  INT UNSIGNED 创建时间
    'create' => 'integer', //  INT UNSIGNED 创建者
    'target' => 'integer', //  INT UNSIGNED 目标用户
    'type' => 'string', //  CHAR(16) 类型,接口type获得
    'new' => 'integer', //  TINYINT UNSIGNED 新消息
    'content' => 'string', //  CHAR(255) 正文
  ];
}
