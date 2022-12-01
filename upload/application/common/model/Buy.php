<?php

namespace app\common\model;


class Buy extends \app\common\model\BaseModel {
  protected $readonly = ['id', 'addtime', 'create', 'model', 'mid', 'content'];
  protected $insert = ['addtime', 'mid'];
  protected $type     = [
    'id' => 'integer', // INT UNSIGNED NOT NULL AUTO_INCREMENT,
    'addtime' => 'integer', // INT UNSIGNED NOT NULL COMMENT '创建时间',
    'create' => 'integer', // INT UNSIGNED NOT NULL COMMENT '创建人',
    'model' => 'string', // CHAR(16) COMMENT '模块名,帕斯卡,首字母大写驼峰',
    'mid' => 'string', // CHAR(32) COMMENT '模块对应Id',
    'content' => 'string', // TEXT NOT NULL COMMENT '其他json',
  ];
  protected function setAddtimeAttr() {
    return time();
  }
  protected function setMidAttr($v) {
    $d = $this->getData();
    $m = model($d['model'])->where('id', $v)->find();
    if ($m) {
      $name = isset($m['name']) ? $m['name'] : $m['title'];
      $points = isset($m['points']) ? $m['points'] : (isset($m['price']) ? $m['price'] : (isset($m['expense']) ? $m['expense'] : 0));
      $note = '模块【' . $d['model'] . '】id:[' . $v . ']。名称:【' . $name . ' 】';
      $pd = [['uid' => $d['create'], 'points' => -1 * $points, 'note' => $note]];
      if (isset($m['create']) && $m['create']) {
        $pd[] = ['uid' => $m['create'], 'points' => $points, 'note' => $note];
      }
      model('MemberPoints')->setPoints($pd);
      return $v;
    }
    throw new \Exception("未查询到当前虚拟物品-" . $d['model'] . ':' . $v, 500);
  }
  protected function setContentAttr() {
    $d = $this->getData();
    $m = model($d['model'])->where('id', $d['mid'])->find();
    if ($m) {
      return encode([
        'name' => isset($m['name']) ? $m['name'] : $m['title'],
        'points' => isset($m['points']) ? $m['points'] : (isset($m['price']) ? $m['price'] : (isset($m['expense']) ? $m['expense'] : 0))
      ]);
    }
    throw new \Exception("未查询到当前虚拟物品-" . $d['model'] . ':' . $d['mid'], 500);
  }
}
