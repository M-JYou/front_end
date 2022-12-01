<?php

namespace app\common\model;

class Ads extends \app\common\model\BaseModel {
  protected $readonly = ['id', 'addtime'];
  protected $type     = [
    'id' => 'integer', //  INT UNSIGNED id
    'addtime' => 'integer', //  INT UNSIGNED 创建时间
    'sort' => 'integer', //  INT UNSIGNED 排序
    'is_display' => 'integer', //  TINYINT(1) UNSIGNED 是否显示

    'type' => 'integer', //  INT UNSIGNED 类型
    'name' => 'string', //  CHAR(64) 名称
    'content' => 'string', //  TEXT 广告内容正文
    'img' => 'string', //  CHAR(255) 图片url
    'url' => 'string', //  CHAR(255) 跳转url
    'other' => 'array', //  TEXT 其他json
  ];
  protected $insert = ['addtime'];
  protected function setAddtimeAttr() {
    return time();
  }

  protected function setContentAttr($v) {
    
    return encode(decode($v));
  }
}
