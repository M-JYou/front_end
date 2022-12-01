<?php

namespace app\common\model;

class Ask extends \app\common\model\BaseModel {
  protected $readonly = ['id', 'addtime'];
  protected $type = [
    'id' => 'integer',
    'is_display' => 'integer',
    'power' => 'integer',
    'point' => 'integer',
    'state' => 'integer',
    //'addtime' =>'integer'
  ];
  // protected $insert = ['addtime'];
  public function initialize() {
    parent::initialize();
  }

  protected function setAddtimeAttr() {
    return time();
  }

  public function _create($p = null, $uid = null) {
    if (isset($uid)) {
      $this->data([
        'is_display' => 1,
        'power' => 0,
        'point' => $p['point'], // '积分数量'
        'create' => $uid, // $this->userinfo->uid, // 创建者ID
        'state' => 0, // '状态', -- 0: 等待回答, 1: 申请延时; 128: 完成
        'name' => $p['name'], // '标题',
        'content' => $p['content'], // input('post.content/s', '一二三四五六七八九十1234567890一二三四五六七八九十1234567890一二三四五六七八九十1234567890一二三四五六七八九十1234567890') // '正文',
      ])->save();
      return $this;
    }
  }
  public function _update($p = null, $uid) {
    $t = $this->getById($p);
    $ret = null;
    // 权限判定
    if ($t && (isset($uid) || ($t['create'] == $uid && $t['state'] < 4))) {
      $q = ['id' => $p['id']];
      if (isset($p['id'])) {
        $q['state'] = $p['state'];
      } elseif (isset($p['answer'])) { // 确定采纳
        if (AskAnswer::get($p['answer'])) {
          $q['answer'] = $p['answer'];
        }
      } else { // 修改标题或正文
        if (isset($p['name'])) {
          $q['name'] = $p['name'];
        }
        if (isset($p['content'])) {
          $q['content'] = $p['content'];
        }
      }
      if ($this->u($q)) {
        $ret = $this;
      }
    }
    return $ret;
  }
  public function _read($p = null) {
    return $this->r($p);
  }
  public function _delete($param = null, $uid) {
    if (isset($uid) && ($t = $this->getById($param)) && $t['create'] == $uid && $t['state'] < 4) {
      return $this->d($param);
    }
  }
}
