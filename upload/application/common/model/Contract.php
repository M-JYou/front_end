<?php

namespace app\common\model;

class Contract extends \app\common\model\BaseModel {
  protected $type     = [
    'id' => 'integer', //  INT UNSIGNED id
    'addtime' => 'integer', //  INT UNSIGNED 创建时间
    'create' => 'integer', //  INT UNSIGNED 创建人id
    'state' => 'string', //  CHAR(16) 步骤
    'order' => 'integer', //  INT 付款订单
    'points' => 'integer', //  INT 金额(点券)

    'type' => 'string', //  CHAR(16) 类型
    'endtime' => 'integer', //  INT UNSIGNED 截止时间
    'files' => 'array', //  TEXT 合同url

    'contacts' => 'string', //  CHAR(16) 联系人
    'tel' => 'string', //  CHAR(16) 联系电话
    'other' => 'array', //  TEXT 其他json

    'appraise' => 'string', //  CHAR(255) 评价
  ];
  protected $readonly = ['id', 'addtime', 'create', 'type'];
  protected $insert = ['addtime'];
  protected function setAddtimeAttr() {
    return time();
  }
  protected function getStateAttr($v, $d) {
    if ($d['endtime'] < time() && false === strpos($v, '完结')) {
      $v = '超时完结';
      model($this->name)->update(['state' => $v], ['id' => $d['id']]);
    }
    return $v;
  }
  protected function getFilesAttr($v, $d) {
    $r = [];
    $v = decode($v);
    foreach ($v as $vv) {
      $r[] = ['name' => $vv['name'], 'url' => $vv['url']];
    }
    return $r;
  }
}
