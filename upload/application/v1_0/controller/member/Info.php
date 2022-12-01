<?php

/** 会员信息相关 */

namespace app\v1_0\controller\member;

class Info extends \app\v1_0\controller\common\Base {
  public function _initialize() {
    parent::_initialize();
    $this->checkLogin();
    if ($this->userinfo->utype == 1) {
      $this->interceptCompanyProfile();
      $this->interceptCompanyAuth();
    } else {
      $this->interceptPersonalResume();
    }
  }
  /** 我的积分 */
  public function myPoints() {
    $return['points'] = model('Member')->getMemberPoints(
      $this->userinfo->uid
    );
    if ($this->userinfo->utype == 1) {
      $return['logo'] =
        $this->company_profile['logo'] > 0
        ? model('Uploadfile')->getFileUrl(
          $this->company_profile['logo']
        )
        : default_empty('logo');

      $return['companyname'] = $this->company_profile['companyname'];
    } else {
      $return['photo'] =
        $this->resume_info['photo_img'] > 0
        ? model('Uploadfile')->getFileUrl(
          $this->resume_info['photo_img']
        )
        : default_empty('photo');
      $return['fullname'] = $this->resume_info['fullname'];
    }
    $return['task'] = model('Task')->taskSituation(
      $this->userinfo->uid,
      $this->userinfo->utype
    );
    $return['taskPoints'] = model('Task')->countTaskPoints($this->userinfo->uid, $this->userinfo->utype);
    $this->ajaxReturn(200, '获取数据成功', $return);
  }
  /** 积分明细 */
  public function pointsLog() {
    $type = input('get.type/d', 0, 'intval');
    $where['uid'] = $this->userinfo->uid;
    switch ($type) {
      case 1:
        $where['op'] = 1;
        break;
      case 2:
        $where['op'] = 2;
        break;
      default:
        break;
    }
    $current_page = input('get.page/d', 1, 'intval');
    $pagesize = input('get.pagesize/d', 10, 'intval');
    $list = model('MemberPointsLog')
      ->field('id,uid', true)
      ->where($where)
      ->order('id desc')
      ->page($current_page . ',' . $pagesize)
      ->select();
    $this->ajaxReturn(200, '获取数据成功', ['items' => $list]);
  }
  /** 积分明细统计 */
  public function pointsLogTotal() {
    $type = input('get.type/d', 0, 'intval');
    $where['uid'] = $this->userinfo->uid;
    switch ($type) {
      case 1:
        $where['op'] = 1;
        break;
      case 2:
        $where['op'] = 2;
        break;
      default:
        break;
    }
    $total = model('MemberPointsLog')
      ->where($where)
      ->count();
    $this->ajaxReturn(200, '获取数据成功', $total);
  }
  /** 套餐日志 */
  public function setmealLog() {
    $where['uid'] = $this->userinfo->uid;
    $current_page = input('get.page/d', 1, 'intval');
    $pagesize = input('get.pagesize/d', 5, 'intval');

    $list = model('MemberSetmealLog')
      ->field('content,addtime')
      ->where($where)
      ->order('id desc')
      ->page($current_page . ',' . $pagesize)
      ->select();

    $this->ajaxReturn(200, '获取数据成功', ['items' => $list]);
  }
  /** 套餐日志总数统计 */
  public function setmealLogTotal() {
    $where['uid'] = $this->userinfo->uid;

    $total = model('MemberSetmealLog')
      ->where($where)
      ->count();

    $this->ajaxReturn(200, '获取数据成功', $total);
  }
  /** 获取专属客服信息 */
  public function customerService() {
    if ($this->company_profile['cs_id'] == 0) {
      $info = [];
    } else {
      $info = model('CustomerService')
        ->field('status', true)
        ->where('id', $this->company_profile['cs_id'])
        ->find();
      if ($info === null) {
        $info = [];
      } else {
        $info['photo'] =
          $info['photo'] > 0
          ? model('Uploadfile')->getFileUrl($info['photo'])
          : default_empty('photo');
        $info['wx_qrcode'] =
          $info['wx_qrcode'] > 0
          ? model('Uploadfile')->getFileUrl($info['wx_qrcode'])
          : '';
      }
    }
    $this->ajaxReturn(200, '获取数据成功', ['info' => $info]);
  }
  /** 投诉客服 */
  public function customerServiceComplaint() {
    $input_data = [
      'uid' => $this->userinfo->uid,
      'addtime' => time(),
      'status' => 0,
      'cs_id' => input('post.cs_id/d', 0, 'intval'),
      'content' => input('post.content/s', '', 'trim'),
    ];
    $validate = new \think\Validate([
      'cs_id' => 'require|number|gt:0',
      'content' => 'require|max:200',
    ]);
    if (!$validate->check($input_data)) {
      $this->ajaxReturn(500, $validate->getError());
    }
    model('CustomerServiceComplaint')->save($input_data);
    $this->writeMemberActionLog($this->userinfo->uid, '投诉客服【客服ID：' . $input_data['cs_id'] . '】');
    $this->ajaxReturn(200, '投诉成功');
  }
  /** 意见反馈 */
  public function feedback() {
    $input_data = [
      'type' => input('post.type/d', 1, 'intval'),
      'utype' => $this->userinfo->utype,
      'uid' => $this->userinfo->uid,
      'addtime' => time(),
      'status' => 0,
      'content' => input('post.content/s', '', 'trim'),
    ];
    $validate = new \think\Validate([
      'type' => 'require|number|gt:0',
      'content' => 'require|max:200',
    ]);
    if (!$validate->check($input_data)) {
      $this->ajaxReturn(500, $validate->getError());
    }
    model('Feedback')->save($input_data);
    model('AdminNotice')->send(
      13,
      '意见建议通知',
      '反馈类型:【' . model('Feedback')->map_type[$input_data['type']] . '】' . "\r\n" .
        '反馈内容:【' . $input_data['content'] . '】' . "\r\n" .
        '联系方式:【' . $this->userinfo->mobile . '】',
      '意见建议通知，请及时跟进（后台->内容->投诉建议->意见建议）'
    );
    $this->writeMemberActionLog($this->userinfo->uid, '提交意见反馈信息');
    $this->ajaxReturn(200, '感谢您的反馈，我们会尽快处理');
  }
  /** 投诉 */
  public function tipoff() {
    $input_data = [
      'target_id' => input('post.target_id/d', 0, 'intval'),
      'type' => input('post.type/d', 1, 'intval'),
      'uid' => $this->userinfo->uid,
      'reason' => input('post.reason/d', 1, 'intval'),
      'img' => input('post.img/a', []),
      'addtime' => time(),
      'status' => 0,
      'content' => input('post.content/s', '', 'trim'),
    ];
    $validate = new \think\Validate([
      'target_id' => 'require|number|gt:0',
      'type' => 'require|number|gt:0',
      'reason' => 'require|number|gt:0',
      'content' => 'require|max:200',
    ]);
    if (!$validate->check($input_data)) {
      $this->ajaxReturn(500, $validate->getError());
    }
    $input_data['img'] = !empty($input_data['img']) ? implode(",", $input_data['img']) : '';
    model('Tipoff')->save($input_data);
    if ($input_data['type'] == 1) {
      model('Task')->doTask($this->userinfo->uid, 2, 'report_job');
    }
    $type = $input_data['type'] == 1 ? '举报职位' : '举报简历';
    if ($input_data['type'] == 1) {
      $Job = model('Job')->where('id', $input_data['target_id'])->find();
      $ids = '职位ID:' . $Job['id'] . '、';
      $name = '职位名称:' . $Job['jobname'];
      $reason = model('Tipoff')->map_type_job[$input_data['reason']];
    } else {
      $Resume = model('Resume')->where('id', $input_data['target_id'])->find();
      $ids = '简历ID:' . $Resume['id'] . '、';
      $name = '简历名称:' . $Resume['fullname'];
      $reason = model('Tipoff')->map_type_resume[$input_data['reason']];
    }
    model('AdminNotice')->send(
      12,
      '投诉举报通知',
      '举报类型:【' . $type . '】' . "\r\n" .
        '被举报对象:【' . $ids . $name . '】' . "\r\n" .
        '举报原因:【' . $reason . '】' . "\r\n" .
        '举报内容:【' . $input_data['content'] . '】' . "\r\n" .
        '举报者:【电话号码:' . $this->userinfo->mobile . '、UID:' . $this->userinfo->uid . '】',
      '投诉举报通知，请及时跟进（后台->内容->投诉建议->举报信息）'
    );
    $this->writeMemberActionLog($this->userinfo->uid, '举报' . ($input_data['type'] == 1 ? '职位' : '简历') . '信息【举报信息ID：' . $input_data['target_id'] . '】');
    $this->ajaxReturn(200, '举报成功，我们会尽快核实处理');
  }
}
