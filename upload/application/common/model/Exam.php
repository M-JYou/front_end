<?php

namespace app\common\model;

class Exam extends \app\common\model\BaseModel {
  protected $readonly = ['id', 'addtime', 'create'];
  protected $type     = [
    'id'          => 'integer',   // INT(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
    'addtime'     => 'integer',   // INT(10) UNSIGNED NOT NULL COMMENT '创建时间',
    'create'      => 'integer',   // INT(10) UNSIGNED NOT NULL COMMENT '创建人id',
    'sort'        => 'integer',   // INT(10) UNSIGNED NOT NULL COMMENT '排序',
    'is_display'  => 'integer',   // TINYINT(1) NOT NULL DEFAULT 1 COMMENT '是否显示',
    'type'        => 'integer',   // INT(10) NOT NULL COMMENT '类型',
    'name'        => 'string',    // CHAR(32) NOT NULL COMMENT '名称',
    'content'     => 'array',     // MEDIUMTEXT NOT NULL COMMENT '试题内容',
    'score'       => 'integer',   // INT(10) NOT NULL COMMENT '总分',
  ];
  protected $insert = ['addtime', 'score'];
  protected $update = ['score'];
  protected function setAddtimeAttr() {
    return time();
  }
  protected function setContentAttr($params) {
    $params = decode($params);
    if (!is_array($params)) {
      throw new \Exception("content格式错误1", 5003);
    }
    $type = ["问答题", "单选题", "多选题", "排序题"];
    foreach ($params as $v) {
      $score = intval($v['score']);
      $options = []; // 备选答案
      if (is_array($v['options'])) {
        foreach ($v['options'] as $pk => $pv) {
          $options[$pk] = $pv;
        }
      } else {
        $options = [$v['options']];
      }
      $answer = $v['answer'];
      $ret[] = [
        'title' => strval($v['title']),
        'options' => $options,
        'answer' => $answer,
        'score' => $score,
        'type' => in_array($v['type'], $type) ? $v['type'] : $type[0],
      ];
    }
    return $ret;
  }
  protected function setScoreAttr($p, $d) {
    $params = decode($d['content']);
    if (!is_array($params)) {
      throw new \Exception("content格式错误2", 5003);
    }
    $ret = 0;
    foreach ($params as $v) {
      $ret += intval($v['score']);
    }
    return $ret;
  }
}
