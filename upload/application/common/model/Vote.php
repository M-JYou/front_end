<?php

namespace app\common\model;

/** 发起投票 */
class Vote extends \app\common\model\BaseModel {
  protected $readonly = ['id', 'addtime', 'create', 'model', 'mid'];
  protected $type = [
    'id' => 'integer', // INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
    'addtime' => 'integer', // INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
    'create' => 'integer', // INT UNSIGNED NOT NULL COMMENT '创建人id',
    'multiple' => 'integer', // TINYINT(1) NOT NULL DEFAULT 0 COMMENT '是否多选',
    'name' => 'string', // CHAR(64) NOT NULL COMMENT '名称',
    'content' => 'string', // TEXT NOT NULL COMMENT '正文内容',
    'other' => 'array', // TEXT NOT NULL COMMENT '选项array',
  ];
  protected $insert = ['addtime'];
  protected function setAddtimeAttr() {
    return time();
  }

  protected function setOtherAttr($v) {
    $ret = [];
    $v = decode($v);
    if (is_array($v)) {
      foreach ($v as $vv) {
        $ret[] = $vv;
      }
    }
    return $ret;
  }
  protected function getChoiceAttr($v, $d) {
    $o = decode($d['other']);
    if (!is_array($o)) {
      throw new \Exception("查询时缺少字段:other,请管理员排查", 500);
    }
    $ret = [];
    $m = model('VoteChoice');
    $w = ['mid' => $v];
    foreach ($o as $k => $v) {
      $ret[] = $m->where($w)->where('choice', $k)->count();
    }
    return $ret;
  }
}
