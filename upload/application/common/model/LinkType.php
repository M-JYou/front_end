<?php

namespace app\common\model;

class LinkType extends \app\common\model\BaseModel {
  protected $readonly = ['id'];
  protected $type     = [
    'id' => 'integer', // INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    'name' => 'string', // VARCHAR(32) NOT NULL,
    'is_sys' => 'integer', // TINYINT NOT NULL DEFAULT 0 COMMENT '是否系统',
  ];
  protected function getChildrenAttr($v) {
    return model('Link')->where([
      'type' => $v,
      'is_display' => 1
    ])->order('sort_id desc, id asc')->select();
  }
  public function getByType($type = 0) {
    $key = "cache_flink_list$type";
    if (!($data = cache($key))) {
      $data = model('Link')->order('sort_id desc')->where('is_display', 1)->where('type', $type)->column('id,name,link_url,link_ico');
      cache($key, $data);
    }
    return $data;
  }
}
