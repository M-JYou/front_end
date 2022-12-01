<?php

namespace app\common\model;

class ExamAnswer extends \app\common\model\BaseModel {
  protected $readonly = ['id', 'addtime', 'create'];
  protected $type     = [
    'id'          => 'integer',   // INT(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
    'addtime'     => 'integer',   // INT(10) UNSIGNED NOT NULL COMMENT '创建时间',
    'create'      => 'integer',   // INT(10) UNSIGNED NOT NULL COMMENT '创建人id',
    'exam'        => 'integer',   // INT(10) NOT NULL COMMENT '题库id',
    'note'        => 'string',    // CHAR(255) NOT NULL COMMENT '备注',
    'name'        => 'string',    // CHAR(32) NOT NULL COMMENT '名称',
    'content'     => 'array',     // MEDIUMTEXT NOT NULL COMMENT '试题内容',
    'score'       => 'integer',   // INT(10) NOT NULL COMMENT '总分',
    'state'       => 'integer',   // INT(10) NOT NULL COMMENT '状态',
    'correct'     => 'integer',   // INT(10) NOT NULL COMMENT '得分',
  ];
  protected $insert = ['addtime'];
  protected function setAddtimeAttr() {
    return time();
  }
  private function getContent($d, $type = 0) {
    if (isset($d['content']) && isset($d['exam'])) {
      $bool = isset($d['#admin']);
      $c = decode($d['content']);
      if (!is_array($c)) {
        throw new \Exception("content格式错误", 5003);
      }
      $e = decode(model('Exam')->field('content')->find($d['exam'])['content']);
      switch ($type) {
        case 0: // 答题正文
          $ret = [];
          foreach ($e as $k => $v) {
            $answer = $c[$k]['answer'];
            $score = $bool ? intval($c[$k]['score']) : ($v['answer'] && $v['answer'] == $answer ? $v['score'] : 0);
            $ret[] = [
              'answer' => $answer,
              'score' =>  $score,
            ];
          }
          break;
        case 1: // 得分
          $ret = 0;
          foreach ($e as $k => $v) {
            $ret += $bool ? intval($c[$k]['score']) : ($v['answer'] && $v['answer'] == $c[$k]['answer'] ? $v['score'] : 0);
          }
          break;
        case 2: // 状态
          if ($bool) {
            $ret = intval($d['state']);
          } else {
            $ret = 1;
            foreach ($e as $v) {
              if (!$v['answer']) {
                $ret = 0;
                break;
              }
            }
          }
          break;

        default:
          $ret = null;
          break;
      }
      $this->allowField(true);
      return $ret;
    }
    throw new \Exception("缺少content或exam", 5004);
  }
  protected function setContentAttr($params, $d) {
    return $this->getContent($d);
  }
  protected function setScoreAttr($value, $d) {
    $ret = $value;
    if (isset($d['exam'])) {
      $ret = model('Exam')->field('score')->find($d['exam'])['score'];
    }
    return $ret;
  }
  protected function setStateAttr($value, $d) {
    $ret = $value;
    if (isset($d['content']) && isset($d['exam'])) {
      $ret = $this->getContent($d, 2);
    }
    return $ret;
  }
  protected function setCorrectAttr($value, $d) {
    $ret = $value;
    if (isset($d['content']) && isset($d['exam'])) {
      $ret = $this->getContent($d, 1);
    }
    return $ret;
  }
  protected function setNameAttr($value, $d) {
    $ret = $value;
    if (isset($d['exam'])) {
      $ret = model('Exam')->field('name')->find($d['exam'])['name'];
    }
    return $ret;
  }
}
