<?php

namespace app\common\model;

class Model extends \app\common\model\BaseModel {
  protected $readonly = ['id'];
  protected $type     = [
    'id' => 'integer', //  INT UNSIGNED id
    'name' => 'string', //  CHAR(64) 名称
    'model' => 'string', //  CHAR(64) 模块
  ];
  protected function getSearchAttr($v, $d) {
    $ret = [];
    if ($d['model'] && $d['model'] != 'Index') {
      try {
        $t = explode(' ', $v);
        $w = [];
        foreach ($t as $tv) {
          if ($tv) {
            $w[] = "%$tv%";
          }
        }
        $m = model($d['model']);
        $field = '*';
        if (in_array('create', $m->_getFields())) {
          $field .= ',`create` `user`, id info5';
        }
        $ret = $m->getModel(count($w) ? ['#like' => $w] : [])
          ->field($field)
          ->limit(3)->order('id desc')
          // ->fetchSql()
          ->select();
      } catch (\Throwable $th) {
        $ret = $th->getMessage();
      }
    }
    return $ret;
  }
}
