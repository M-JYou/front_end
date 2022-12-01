<?php

namespace app\common\model;

class Study extends \app\common\model\BaseModel {
  protected $readonly = ['id', 'addtime', 'create'];
  protected $type     = [
    'id'              => 'integer', // INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
    'addtime'         => 'integer', // INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
    'create'          => 'integer', // INT(10) UNSIGNED NOT NULL COMMENT '创建人id',
    'sort'            => 'integer', // INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
    'type'            => 'integer', // TINYINT(1) UNSIGNED NOT NULL COMMENT '类型',0:财税问答,1:人社问答
    'is_caishui'      => 'integer', // TINYINT(1) UNSIGNED NOT NULL COMMENT '是否财税',
    'is_video'        => 'integer', // TINYINT(1) UNSIGNED NOT NULL COMMENT '是否视频',0:图文,1:视频
    'expense'         => 'integer', // INT(10) NOT NULL DEFAULT 0 COMMENT '价格',
    'simple'          => 'string',  // VARCHAR(1024) NOT NULL DEFAULT '' COMMENT '简介',
    'simple_teacher'  => 'string',  // VARCHAR(1024) NOT NULL DEFAULT '' COMMENT '简介-讲师',
    'video'           => 'string',  // VARCHAR(1024) NOT NULL DEFAULT '' COMMENT '视频url',
    'cover'           => 'string',  // VARCHAR(1024) NOT NULL DEFAULT '' COMMENT '封面url',
    'name'            => 'string',  // CHAR(32) NOT NULL COMMENT '名称',
    'content'         => 'string',  // MEDIUMTEXT NOT NULL COMMENT '正文富文本',
    'other'           => 'array',   // VARCHAR(2048) NOT NULL COMMENT '其他',
  ];
  protected $insert = ['addtime', 'sort_id' => 0];
  protected function setAddtimeAttr() {
    return time();
  }
  protected function getContentAttr($value) {
    return htmlspecialchars_decode($value);
  }
  protected function getSimpleAttr($v) {
    return decode($v);
  }
  protected function setPidAttr($value) {
    if ($value != 0 && !model('Study')->get($value)) {
      throw new \Exception('不存在问题id:' . $value, 500);
    }
    return $value;
  }
  private function exist($id) {
    $td = $this->get($id);
    if (!$td) {
      throw new \Exception('id不存在', 500);
    }
    return $td;
  }
  public function buy($id, $uid) {
    $td = $this->exist($id);
    $msb = model('StudyBuy');
    if ($msb->select(['create' => $uid, 'pid' => $id])->find()) { // 已经购买
      return;
    }
    model('Member')->setPoints([
      'uid' => $uid,
      'points' => $td['expense'],
      'note' => '阅读文章【' . $id . '】',
    ]);
  }
  public function like($id, $uid) {
    $this->exist($id);
    $m = model('StudyLikes');
    $s = $m->where(['create' => $uid, 'pid' => $id])->find();
    if ($s) {
      $d = ['id' => $s['id'], 'addtime' => $s['addtime'] ? 0 : time()];
    } else {
      $d = ['create' => $uid, 'pid' => $id];
    }
    $m->save($d);
  }
  public function report($id, $uid, $content) {
    $this->exist($id);
    $m = model('StudyReport');
    $s = $m->where(['create' => $uid, 'pid' => $id])->find();
    if ($s) {
      $d = ['id' => $s['id'], 'content' => $content];
    } else {
      $d = ['create' => $uid, 'pid' => $id, 'content' => $content];
    }
    $m->save($d);
  }
  public function reprint($id, $uid) {
    $this->exist($id);
    $m = model('StudyReprint');
    $s = $m->where(['create' => $uid, 'pid' => $id])->find();
    if ($s) {
      $d = ['id' => $s['id'], 'addtime' => $s['addtime'] ? 0 : time()];
    } else {
      $d = ['create' => $uid, 'pid' => $id];
    }
    $m->save($d);
  }
}
