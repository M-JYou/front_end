<?php

namespace app\common\model;

class AdsType extends \app\common\model\BaseModel {
  protected $readonly = ['id'];
  protected $type     = [
    'id' => 'integer', //  INT UNSIGNED id
    'name' => 'string', //  CHAR(64) 名称
    'model_name' => 'string', //  CHAR(64) 模块名
    'model' => 'string', //  CHAR(64) 模块
    'type' => 'string', //  CHAR(64) 模块类型
    'content' => 'array', //  TEXT 模块id组
  ];
  protected function getChildrenAttr($v, $d = null) {
    return model('Ads')->where([
      'type' => $d['id'],
      'is_display' => 1
    ])->order('sort desc')->select();
  }

  protected function setModelAttr($v, $d) {
    if (model('Model')->where('model', $v)->find()) {
      return $v;
    }
    ext(500, '不存在此模块:' . $v);
  }
  protected function setTypeAttr($v, $d) {
    if ($v && !modelType($v)) {
      $v = '';
    }
    return $v;
  }
  protected function setContentAttr($v) {
    $ret = decode($v);
    return $ret ? $ret : [];
  }
}
