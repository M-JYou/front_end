<?php

namespace app\common\model;

class Auth extends \app\common\model\BaseModel {
  protected $readonly = ['id'];
  protected $type     = [
    'id' => 'integer', // INT id,
    'addtime' => 'integer', // INT 创建时间,
    'uid' => 'integer', // INT 用户id,
    'type' => 'string', // CHAR(255) 认证类型,
    'files' => 'array', // TEXT 相关认证文件,
  ];
  protected $insert = ['addtime'];
  protected function setAddtimeAttr() {
    return time();
  }

  public function getByUid($uid) {
    $r = $this->where('uid', $uid)->select();
    if (!count($r)) {
      $r = [];
    }
    try {
      foreach ($r as $v) {
        if ($v['type'] == '企业认证') {
          throw null;
        }
      }
      $t = model('CompanyAuth')->where('uid', $uid)->find();
      if ($t) {
        $mu = model('Uploadfile'); //->getFileUrl(312);
        $r[] = $this->create([
          'uid' => $uid,
          'type' => '企业认证',
          'files' => [
            ['name' => '代理人身份证正面', 'url' => $mu->getFileUrl($t['legal_person_idcard_front'])],
            ['name' => '代理人身份证反面', 'url' => $mu->getFileUrl($t['legal_person_idcard_back'])],
            ['name' => '营业执照', 'url' => $mu->getFileUrl($t['license'])],
            ['name' => '委托书', 'url' => $mu->getFileUrl($t['proxy'])],
            ['name' => '法人', 'url' => $t['legal_person']]
          ],
        ]);
      }
    } catch (\Throwable $th) {
    }
    return $r;
  }
  public function getTypes($uid) {
    $r = $this->where('uid', $uid)->column('type');
    if (!count($r)) {
      $r = [];
    }
    if (!in_array('企业认证', $r)) {
      $t = model('CompanyAuth')->where('uid', $uid)->find();
      if ($t) {
        $mu = model('Uploadfile'); //->getFileUrl(312);
        $this->create([
          'uid' => $uid,
          'type' => '企业认证',
          'files' => [
            ['name' => '代理人身份证正面', 'url' => $mu->getFileUrl($t['legal_person_idcard_front'])],
            ['name' => '代理人身份证反面', 'url' => $mu->getFileUrl($t['legal_person_idcard_back'])],
            ['name' => '营业执照', 'url' => $mu->getFileUrl($t['license'])],
            ['name' => '委托书', 'url' => $mu->getFileUrl($t['proxy'])],
          ],
        ]);
        $r[] = '企业认证';
      }
    }
    return $r;
  }
}
