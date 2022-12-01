<?php

namespace app\common\model;

class Article extends \app\common\model\BaseModel {
  protected $readonly = ['id'];
  protected $type     = [
    'id' => 'integer', // INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    'addtime' => 'integer', //  INT UNSIGNED 创建时间
    'cid' => 'integer', //  INT UNSIGNED 分类id
    'create' => 'integer', //  INT UNSIGNED 创建者id
    'price' => 'integer', //  INT 价格
    'category_district' => 'integer', //  INT UNSIGNED 归属地
    'title' => 'string', //  VARCHAR(100) 标题
    'content' => 'string', //  LONGTEXT 正文
    'attach' => 'array', //  TEXT 文件[{name:"",url:""}]
    'other' => 'array', //  TEXT 其他json
    'thumb' => 'string', //  CHAR(255) 封面url
    'is_display' => 'integer', //  TINYINT(1) UNSIGNED 是否显示
    'link_url' => 'string', //  VARCHAR(200) 转载的url
    'seo_keywords' => 'string', //  VARCHAR(100) seo关键字
    'seo_description' => 'string', //  VARCHAR(200) seo说明
    'click' => 'integer', //  INT UNSIGNED 浏览次数
    'sort_id' => 'integer', //  INT UNSIGNED 排序
    'source' => 'integer', //  TINYINT(1) UNSIGNED 是否转载
    'fno' => 'string', //  CHAR(255) 文号
    'exetime' => 'string', //  CHAR(255) 执行时间
  ];
  // protected $insert = ['addtime'];
  // protected function setAddtimeAttr() {
  //   return time();
  // }

  protected function setAttachAttr($v) {
    $v = decode($v);
    foreach ($v as $k => $vv) {
      $v[$k] = ['name' => $vv['name'], 'url' => $vv['url']];
    }
    return $v;
  }
  protected function getContentAttr($v) {
    return $this->checkContent($v);
  }
  protected function getAttachAttr($v) {
    return $this->checkContent($v);
  }
}
