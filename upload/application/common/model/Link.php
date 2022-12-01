<?php

namespace app\common\model;

class Link extends \app\common\model\BaseModel {
  protected $readonly = ['id'];
  protected $type     = [
    'id' => 'integer', // INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    'change_time' => 'integer', // INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '修改时间',
    'is_display' => 'integer', // TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
    'name' => 'string', // VARCHAR(32) NOT NULL,
    'link_url' => 'string', // VARCHAR(255) NOT NULL COMMENT '跳转地址',
    'link_ico' => 'string', // VARCHAR(255) NOT NULL COMMENT '图片地址',
    'sort_id' => 'integer', // INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
    'notes' => 'string', // VARCHAR(100) NOT NULL DEFAULT '' DEFAULT '简介',
    'content' => 'string', // TEXT NOT NULL COMMENT '正文',
    'type' => 'integer', // TINYINT NOT NULL DEFAULT 1 COMMENT '类型',
  ];
  protected $auto = ['change_time'];
  protected function setChangeTimeAttr() {
    cache('link_get', 1);
    return time();
  }
  public function getByType($type = 0) {
    return $this->getCache($type);
  }
  public function getCache($type = null) {
    if (cache('link_get') || !($ret = cache('link_all'))) {
      cache('link_get', null);
      $ret = parseField($this->where('is_display', 1)->field('*,`type` `type_`')->order('type asc,id asc,sort_id desc')->select());

      $color = ['e7dcd6', 'b8f6e7', 'e7fbc0', 'c6edfc', 'fdf4d7', 'e4fddf'];
      $i = 0;
      foreach ($ret as $k => $v) {
        if ($v['type'] == 13) {
          $ret[$k]['color'] = $color[$i];
          if ($i < 5) {
            $i++;
          } else {
            $i = 0;
          }
        }
      }

      cache('link_all', $ret);
    }
    try {
      if ($type > 0) {
        $t = $ret;
        $ret = [];
        foreach ($t as $v) {
          if ($v['type'] === $type) {
            $ret[] = $v;
          }
        }
      }
    } catch (\Throwable $th) {
    }
    return $ret;
  }
}
