<?php

/** 发起投票 */

namespace app\v1_0\controller\member;

class Vote extends \app\v1_0\controller\common\Base {
  protected function getData() {
    $this->checkLogin();
    $ret = input('param.');
    $ret['create'] = $this->userinfo->uid;
    return $ret;
  }

  public function index() {
    $this->_find(input('param.'),'*,id `choice`');
  }
  public function choice() {
    $this->_add($this->getData(), model('VoteChoice'));
  }
  public function deleteChoice() {
    $this->_delete($this->getData(), model('VoteChoice'));
  }
}
