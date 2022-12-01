<?php

/**  验证手机号等 */

namespace app\v1_0\controller\member;

class Files extends \app\v1_0\controller\common\Base {
  public function _initialize() {
    parent::_initialize();
  }
  public function find() {
    $param = input('param.');
    $this->_find($param);
  }
  public function add() {
    $this->checkLogin();
    $d = input('post.');
    $d['create'] = $this->userinfo->uid;
    $d['is_display'] = 0;
    model('MemberPoints')->setPoints([[
      'uid' => $this->userinfo->uid,
      'points' => intval(config('global_config.UploadReward')),
      'note' => '上传文件:' . $d['name']
    ]]);
    $this->_add($d);
  }
  public function edit() {
    ext(500, '没有权限');
  }
  public function delete() {
    ext(500, '没有权限');
  }
}
