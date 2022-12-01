<?php

namespace app\common\model;

class Xtime extends \app\common\model\BaseModel {
  protected $readonly = ['id', 'addtime', 'create'];
  protected $type     = [
    'id'              => 'integer', // INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
    'addtime'         => 'integer', // INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
    'create'          => 'integer', // INT(10) UNSIGNED NOT NULL COMMENT '创建人id',
    'sort_id'         => 'integer', // INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
    'is_display'      => 'integer', // TINYINT(1) NOT NULL DEFAULT 1 COMMENT '是否显示',
    'display_type'    => 'integer', // TINYINT(1) NOT NULL DEFAULT 1 COMMENT '可见值0:自己可见;1:部分可见;2:全部可见',
    'sum'             => 'integer', // INT(10) NOT NULL DEFAULT 0 COMMENT '浏览量',
    'name'            => 'string',  // CHAR(32) NOT NULL COMMENT '名称',
    'content'         => 'string',  // MEDIUMTEXT NOT NULL COMMENT '正文富文本',
    'other'           => 'array',   // 其他
  ];
  protected $insert = ['addtime', 'sort_id' => 0];
  protected function setAddtimeAttr() {
    return time();
  }
  protected function setIsDisplayAttr($v) {
    return $v ? 1 : 0;
  }
  protected function getContentAttr($v, $d = null) {
    if ($d && isset($d['visitor'])) {
      $id = $this->getData('id');
      $this->where('id', $id)->setInc('sum'); // 浏览量+1
      $visitor = [
        'create' => $d['visitor'],
        'model' => $this->name,
        'mid' => $id,
      ];
      $m = model('Visitor');
      try {
        $m->create($visitor);
      } catch (\Throwable $th) {
        $d = $m->where($visitor)->find();
        $m->update(['addtime' => 1], ['id' => $d['id']]);
      }
    }
    return decode($v);
  }
  protected function getVisitorAttr($v, $d = null) {
    $ret = [];
    if ($d && isset($d['id']) && isset($d['create'])) {
      $ret = model('Visitor')
        ->alias('a')
        ->join(
          config('database.prefix') . 'member_info b',
          'a.create=b.id',
          'left'
        )
        ->where(['model' => 'Xtime', 'mid' => $d['id'], 'create' => ['<>', $d['create']]])
        ->field(
          'b.id,b.name,b.photo,a.addtime'
            . ',(SELECT COUNT(*) FROM `qs_xtime` WHERE `create`=`b`.`id`) `send`'
            . ',(SELECT COUNT(*) FROM `qs_likes` WHERE `create`=`b`.`id`) `like`'
        )
        // ->fetchSql()
        ->orderRaw('a.addtime desc')
        ->limit(10)
        ->select();
    }
    return $ret;
  }
}
