<?php

namespace app\common\model;

/** 戏说财税 */
class Entertain extends \app\common\model\BaseModel {
  protected $readonly = ['id', 'addtime', 'create'];
  protected $type     = [
    'id'              => 'integer', // INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
    'addtime'         => 'integer', // INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
    'create'          => 'integer', // INT(10) UNSIGNED NOT NULL COMMENT '创建人id',
    'sort_id'         => 'integer', // INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
    'type'            => 'integer', // TINYINT(1) UNSIGNED NOT NULL COMMENT '类型',0:财税问答,1:人社问答
    'entertain'       => 'integer', // INT(10) NOT NULL DEFAULT 0 COMMENT '隶属id;评论需要设置此值为目标id',
    'name'            => 'string',  // CHAR(32) NOT NULL COMMENT '名称',
    'content'         => 'string',  // MEDIUMTEXT NOT NULL COMMENT '正文富文本',
    'other'           => 'array',   // VARCHAR(2048) NOT NULL COMMENT '其他',
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
