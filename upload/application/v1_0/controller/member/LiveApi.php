<?php

namespace app\v1_0\controller\member;

class LiveApi extends \app\v1_0\controller\common\Base {
  public function getUid() {
    $data = [
      'uid' => $this->userinfo->uid,
      'mobile' => $this->userinfo->mobile
    ];
    $this->ajaxReturn(200, '获取UID', $data);
  }

  /*
     * 判断当前用户有无注册
     */
  public function isRegister() {
    $utype = input('post.utype/d', 0, 'intval');
    $mobile = input('post.mobile/d', 0, 'intval');
    $res_resume = model('Member')->where('utype', $utype)->where('mobile', $mobile)->find();
    if (empty($res_resume)) {
      $this->ajaxReturn(200, '未注册');
    }
    $this->ajaxReturn(500, '已注册');
  }

  /*
     * 判断当前用户有无简历
     */
  public function isResume() {
    $uid = input('post.uid/d', 0, 'intval');
    $res_resume = model('Resume')->where('uid', $uid)->find();
    if (empty($res_resume)) {
      $this->ajaxReturn(500, '暂无简历');
    }
    $this->ajaxReturn(200, '已有简历');
  }

  /** 投递简历 */
  public function jobApplyAdd() {
    $this->checkLogin(2);
    $this->interceptPersonalResume();
    if (
      false ===
      model('JobApply')->liveJobApplyAdd(input('post.'), $this->userinfo->uid)
    ) {
      $this->ajaxReturn(500, model('JobApply')->getError());
    }
    $this->writeMemberActionLog($this->userinfo->uid, '投递简历【职位ID：' . input('post.jobid') . '】');
    $this->ajaxReturn(200, '投递简历成功');
  }

  /** 快速完善简历并投递 */
  public function personalQuick() {
    $input_data = [
      'uid' => input('post.uid/d', 0, 'intval'),
      'jobid' => input('post.jobid/d', 0, 'intval'),
      'fullname' => input('post.fullname/s', '', 'trim,badword_filter'),
      'sex' => input('post.sex/d', 0, 'intval'),
      'birthday' => input('post.birthday/s', '', 'trim'),
      'education' => input('post.education/d', 0, 'intval'),
      'enter_job_time' => input(
        'post.enter_job_time/s',
        '',
        'trim'
      ),
      'category1' => input('post.category1/d', 0, 'intval'),
      'category2' => input('post.category2/d', 0, 'intval'),
      'category3' => input('post.category3/d', 0, 'intval'),
      'district1' => input('post.district1/d', 0, 'intval'),
      'district2' => input('post.district2/d', 0, 'intval'),
      'district3' => input('post.district3/d', 0, 'intval'),
      'minwage' => input('post.minwage/d', 0, 'intval'),
      'maxwage' => input('post.maxwage/d', 0, 'intval'),
      'current' => input('post.current/d', 0, 'intval'),
    ];
    //开始注册
    \think\Db::startTrans();
    try {
      $re_member = model('Member')->where('uid', $input_data['uid'])->find();
      if (empty($re_member)) {
        $this->ajaxReturn(500, '账号未注册或信息有误');
      }
      $add_resume_data = [
        'uid' => $input_data['uid'],
        'fullname' => $input_data['fullname'],
        'sex' => $input_data['sex'],
        'birthday' => $input_data['birthday'],
        'education' => $input_data['education'],
        'enter_job_time' => $input_data['enter_job_time'],
        'current' => $input_data['current'],
        'major1' => 0,
        'major2' => 0,
        'major' => 0,
        'is_live' => 1,
      ];
      $add_resume_data['enter_job_time'] = !$add_resume_data['enter_job_time'] ? 0 : strtotime($add_resume_data['enter_job_time']);
      $add_resume_data['platform'] = config('platform');
      $result = model('Resume')
        ->validate('Resume.reg_from_app_by_form')
        ->allowField(true)
        ->save($add_resume_data);
      if (false === $result) {
        throw new \Exception(model('Resume')->getError());
      }
      $resume_id = model('Resume')->id;

      $ad_contact_data = [
        'rid' => $resume_id,
        'uid' => $input_data['uid'],
        'mobile' => $re_member['mobile'],
        'email' => '',
        'qq' => '',
        'weixin' => ''
      ];
      $result = model('ResumeContact')
        ->validate(false)
        ->allowField(true)
        ->save($ad_contact_data);
      if (false === $result) {
        throw new \Exception(model('ResumeContact')->getError());
      }

      $add_intention_data = [
        'rid' => $resume_id,
        'uid' => $input_data['uid'],
        'category1' => $input_data['category1'],
        'category2' => $input_data['category2'],
        'category3' => $input_data['category3'],
        'district1' => $input_data['district1'],
        'district2' => $input_data['district2'],
        'district3' => $input_data['district3'],
        'minwage' => $input_data['minwage'],
        'maxwage' => $input_data['maxwage'],
        'current' => $input_data['current']
      ];
      $add_intention_data['category'] =
        $add_intention_data['category3'] > 0
        ? $add_intention_data['category3']
        : ($add_intention_data['category2'] > 0
          ? $add_intention_data['category2']
          : $add_intention_data['category1']);
      $add_intention_data['district'] =
        $add_intention_data['district3'] > 0
        ? $add_intention_data['district3']
        : ($add_intention_data['district2'] > 0
          ? $add_intention_data['district2']
          : $add_intention_data['district1']);
      $result = model('ResumeIntention')
        ->validate('ResumeIntention.reg_from_app_by_form')
        ->allowField(true)
        ->save($add_intention_data);
      if (false === $result) {
        throw new \Exception(model('ResumeIntention')->getError());
      }
      //更新完整度
      model('Resume')->updateComplete(
        [
          'basic' => 1,
          'intention' => 1
        ],
        $resume_id,
        $input_data['uid']
      );
      \think\Db::commit();
    } catch (\Exception $e) {
      \think\Db::rollBack();
      $this->ajaxReturn(500, $e->getMessage());
    }
    model('Resume')->refreshSearch($resume_id);
    $this->writeMemberActionLog($input_data['uid'], '注册 - 保存简历基本信息');
    $login_return = $this->loginExtra($input_data['uid'], 2, $re_member['mobile']);
    $global_config = config('global_config');

    $current_complete = model('Resume')->countCompletePercent($resume_id);
    $login_return['require_complete'] = $global_config['apply_job_min_percent'];
    $login_return['current_complete'] = $current_complete;
    $job_apply_data = [
      'jobid' => $input_data['jobid'],
      'note' => ''
    ];
    if (
      false ===
      model('JobApply')->liveJobApplyAdd($job_apply_data, $input_data['uid'])
    ) {
      $this->ajaxReturn(500, model('JobApply')->getError());
    }
    $this->writeMemberActionLog($input_data['uid'], '投递简历【职位ID：' . $job_apply_data['jobid'] . '】');
    $this->ajaxReturn(
      200,
      '投递成功',
      $login_return
    );
  }

  public function delete() {
    $comid = input('post.comid/d', 0, 'intval');
    $personal_uid = input('post.personal_uid/d', 0, 'intval');
    $resume_id = input('post.resume_id/d', 0, 'intval');
    $jobid = input('post.jobid/d', 0, 'intval');
    $info = model('JobApply')
      ->where(['comid' => ['eq', $comid], 'personal_uid' => ['eq', $personal_uid], 'resume_id' => ['eq', $resume_id]])
      ->find();
    model('JobApply')
      ->where(
        [
          'comid' => ['eq', $comid],
          'personal_uid' => ['eq', $personal_uid],
          'resume_id' => ['eq', $resume_id],
          'jobid' => ['eq', $jobid]
        ]
      )
      ->delete();
    $this->ajaxReturn(200, '删除成功');
  }

  /**
   * 直播新登录接口
   * 2022.08.04
   * @return void
   */
  public function sendSmsLogin() {
    $live_app_key = input('post.live_app_key/s', '', 'trim');
    $live_app_secret = input('post.live_app_secret/s', '', 'trim');
    if ($live_app_key != '' && $live_app_secret != '') {
      if ($live_app_key != config('global_config.live_app_key')) {
        $this->ajaxReturn(500, '直播配置有误');
      }
      $md5_appsecret = md5(md5(config('global_config.live_app_key') . config('global_config.live_app_secret')) . 'ergFGsdfgf545');
      if ($live_app_secret != $md5_appsecret) {
        $this->ajaxReturn(500, '直播配置有误');
      }
    }
    $mobile = input('post.mobile/s', '', 'trim');
    if (!fieldRegex($mobile, 'mobile')) {
      $this->ajaxReturn(500, '手机号格式错误');
    }
    $utype = input('post.utype/d', 0, 'intval');
    if (!$utype) {
      $this->ajaxReturn(500, '参数错误');
    }
    if (1 === cache('sendsms_time_limit_' . $mobile)) {
      $this->ajaxReturn(500, '请60秒后再重新获取');
    }
    $code = mt_rand(1000, 9999) . '';
    $templateCode = 'SMS_2';
    $params = [
      'code' => $code,
      'sitename' => config('global_config.sitename')
    ];
    $class = new \app\common\lib\Sms();
    if (false === $class->send($mobile, $templateCode, $params)) {
      $this->ajaxReturn(500, $class->getError());
    }
    cache(
      'smscode_' . $mobile,
      [
        'code' => $code,
        'mobile' => $mobile,
        'utype' => $utype
      ],
      180
    );
    cache('sendsms_time_limit_' . $mobile, 1, 60);
    \think\Cache::set('smscode_error_num_' . $mobile, 0, 180);
    $this->ajaxReturn(200, '发送验证码成功');
  }
}
