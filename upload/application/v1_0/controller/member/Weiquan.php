<?php

namespace app\v1_0\controller\member;

/** 维权 */
class Weiquan extends \app\v1_0\controller\common\Base {

  public function _initialize() {
    parent::_initialize();
    $this->checkLogin();
  }
  protected $state = ['提交维权', '已处理', '申请复议', '终结'];
  public function add() {
    $mobile = input('post.mobile/s', '', 'trim');
    $d = cache('smscode_' . $mobile);
    if ($d && strval($d['code']) === input('post.code/s', null, 'trim')) {
      $data = [
        'create' => $this->userinfo->uid,
        'state' => 0,
        'evidence' => input('post.evidence/a', []),
        'mobile' => $mobile,
        'name' => input('post.name/s', '', 'trim'),
        'reason' => input('post.reason/s', '', 'trim'),
        'content' => input('post.content/s', '', 'trim'),
      ];
      if (!$data['pid']) {
        $this->ajaxReturn(500, '没有权限');
      }
      $this->_add($data);
    }
    ext(500, '验证码错误');
  }
  public function edit() {
    ext(500, '无此接口');
  }
  public function delete() {
    ext(500, '无此接口');
  }
  public function state() {
    ext(200, 'state值为此数组下标', $this->state);
  }
  public function reconsideration() {
    $id = input('post.id/d', 0, 'intval');
    if ($d =
      model($this->dbname)
      ->where('create', $this->userinfo->uid)
      ->where('id', $id)
      ->where('state', 1)
      ->find()
    ) {

      model($this->dbname)->update(['state' => 2], ['id' => $id]);
      ext(200, '成功申请复议', $d);
    }
    ext(500, '数据格式错误或当前状态无法评价');
  }
}
