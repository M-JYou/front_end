<?php

namespace app\common\model;

class MemberPoints extends \app\common\model\BaseModel {

  /** 设置积分
   * @param array[['uid'=>int,'points'=>int,'note'=>string]] $param 成员 ['uid'=>int,'points'=>int,'note'=>string] 
   */
  public function setPoints(array $param) {
    if (!is_array($param) || !count($param)) {
      return;
    }
    if (isset($param['uid'])) {
      $param = [$param];
    }
    $data = [];
    foreach ($param as $v) {
      $data[$v['uid']] = ['points' => $v['points'], 'note' => (isset($v['note']) ? $v['note'] : '')];
    }
    \think\Db::startTrans();
    $mm = model('Member');
    $us = $mm->alias('a')
      ->where('a.uid', 'in', array_keys($data))
      ->field('b.id,a.uid,b.points')
      ->join('MemberPoints b', 'a.uid=b.uid', 'left')
      ->select();
    $spi = [];
    $sp = [];
    $sl = [];
    $addtime = time();
    foreach ($us as $v) {
      if ($v['points'] === null) {
        $v['points'] = $data[$v['uid']]['points'];
        $sl[] = ['uid' => $v['uid'], 'op' => 1, 'points' => 0, 'content' => '注册账号。归零。', 'addtime' => $addtime];
        $spi[] = ['uid' => $v['uid'], 'points' => $v['points']];
      } else {
        $v['points'] += $data[$v['uid']]['points'];
        $sp[] = ['id' => $v['id'], 'points' => $v['points']];
      }
      if ($v['points'] < 0) {
        throw new \Exception('用户【' . $v['uid'] . '】积分不足', 500);
      }
      $sl[] = ['uid' => $v['uid'], 'op' => $data[$v['uid']]['points'] < 0 ? 2 : 1, 'points' => abs($data[$v['uid']]['points']), 'content' => $data[$v['uid']]['note'], 'addtime' => $addtime];
    }
    if ($spi) {
      $this->saveAll($spi);
    }
    if ($sp) {
      $this->saveAll($sp);
    }
    if ($sl) {
      model('MemberPointsLog')->saveAll($sl);
    }
    \think\Db::commit();
  }
}
