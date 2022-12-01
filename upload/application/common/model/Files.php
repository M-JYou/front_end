<?php

namespace app\common\model;

class Files extends \app\common\model\BaseModel {
  protected $readonly = ['id', 'addtime'];
  protected $type     = [
    'id' => 'integer', //  INT UNSIGNED id
    'create' => 'integer', //  INT UNSIGNED 创建者
    'addtime' => 'integer', //  INT UNSIGNED 创建时间
    'sort' => 'integer', //  INT UNSIGNED 排序
    'is_display' => 'integer', //  TINYINT(1) UNSIGNED 是否显示

    'type' => 'integer', //  INT UNSIGNED 类型
    'name' => 'integer', //  CHAR(64) 名称
    'content' => 'integer', //  TEXT 文档内容正文
    'url' => 'integer', //  CHAR(255) 跳转url
    'other' => 'integer', //  TEXT 其他json
  ];
  protected $insert = ['addtime'];
  protected function setAddtimeAttr() {
    return time();
  }


}
