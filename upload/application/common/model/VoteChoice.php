<?php

namespace app\common\model;

/** 投票 */
class VoteChoice extends \app\common\model\BaseModel {
  protected $readonly = ['id', 'create', 'mid'];
  protected $type = [
    'id' => 'integer', // INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
    'addtime' => 'integer', // INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
    'create' => 'integer', // INT UNSIGNED NOT NULL COMMENT '创建人id',
    'mid' => 'integer', // INT UNSIGNED NOT NULL COMMENT 'vote id',
    'choice' => 'integer', // INT UNSIGNED NOT NULL COMMENT '选项',
  ];
  protected $auto = ['addtime'];
  protected function setAddtimeAttr() {
    return time();
  }

  protected function setMidAttr($v) {
    if (!model('Vote')->where('id', '=', $v)->field('id')->find()) {
      throw new \Exception("所投票id错误:$v", 5001);
    }
    return $v;
  }
  protected function setChoiceAttr($v, $d) {
    if (isset($d['mid']) && isset($d['create'])) { // 要求判断 $v值
      $e = model('Vote')->where('id', '=', $d['mid'])
        ->field('other,multiple')->find();
      $o = decode($e['other']);

      if (is_array($o) && $v >= 0 && $v < count($o)) {
        // 有完整配置
        if (!$e['multiple']) {
          $m = model($this->name);
          $t = $m->where(['create' => $d['create'], 'mid' => $d['mid']])->field('id')->find();
          if ($t) {
            // $m->update(['choice' => $v], ['id' => $t['id']]);
            // throw new \Exception("设置答案成功", 200);
            throw new \Exception("您已投票,请勿重复投票", 500);
          }
        }
      } else {
        throw new \Exception("选项choice不正确:".$v, 5003);
      }
    }
    return $v;
  }
}
