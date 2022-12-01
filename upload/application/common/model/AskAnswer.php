<?php

namespace app\common\model;

class AskAnswer extends \app\common\model\BaseModel {
  protected $readonly = ['id', 'addtime'];
  protected $type = [
    'id' => 'integer',
    'addtime' => 'integer',
    'ask' => 'integer',
  ];
  protected $insert = ['addtime'];
  protected function setAddtimeAttr() {
    return time();
  }
  public static $fields = [
    'id', // int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
    'create', // CHAR(9) NOT NULL COMMENT '创建者ID',
    'addtime', // int(10) UNSIGNED NOT NULL COMMENT '时间',
    'ask', // int(10) UNSIGNED NOT NULL COMMENT '问题id',
    'content', // longtext NOT NULL COMMENT '正文',
    'like', // int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '点赞',
  ];
  public static function safeWhere($param = []) {
    return checkArray(Ask::$fields, $param);
  }
}
