<?php

namespace app\common\model;

class Games extends \app\common\model\BaseModel {
  protected $readonly = ['id', 'addtime', 'create'];
  protected $type     = [
    'id'              => 'integer', // INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
    'addtime'         => 'integer', // INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
    'create'          => 'integer', // INT(10) UNSIGNED NOT NULL COMMENT '创建人id',
    'sort_id'         => 'integer', // INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
    'type'            => 'integer', // TINYINT(1) UNSIGNED NOT NULL COMMENT '类型',0:财税问答,1:人社问答
    'games'           => 'integer', // INT(10) NOT NULL DEFAULT 0 COMMENT '隶属id;评论需要设置此值为目标id',
    'name'            => 'string',  // CHAR(32) NOT NULL COMMENT '名称',
    'cover'           =>'string',   // CHAR(255) NOT NULL COMMENT '封面',
    'link'            =>'string',   // CHAR(255) NOT NULL COMMENT '名称',
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
  protected function setGamesAttr($value) {
    if ($value != 0 && !model('Games')->get($value)) {
      throw new \Exception('不存在问题id:' . $value, 500);
    }
    return $value;
  }
  protected function setAnswerAttr($v, $d) {
    if ($v && !isset($d['id'])) {
      throw new \Exception("没有id", 500);
    }
    if (isset($d['id'])) {
      $r = $this->get($d['id']);
      if (!$r) {
        throw new \Exception("id错误", 500);
      }
      if ($r['answer']) { // 已经采纳了答案
        throw new \Exception("答案已采纳,请勿重新提交", 500);
      } else {
        $u = $this->get($v);
        if (!$r) {
          throw new \Exception("答案id错误", 500);
        }
        model('Member')->transferPoints($r['create'], $u['create'], $r['expense'], '有奖问答。');
      }
    }
    return $v;
  }
}
