<?php

/**  验证手机号等 */

namespace app\v1_0\controller\member;

class Account extends \app\v1_0\controller\common\Base {
  public function _initialize() {
    parent::_initialize();
    $this->checkLogin();
  }
  /**  获取账号管理相关信息 */
  public function index() {
    $return = [];
    $member = model('Member')
      ->field('username,mobile,email,password,last_login_time')
      ->where('uid', $this->userinfo->uid)
      ->find();
    if ($member === null) {
      $this->ajaxReturn(50002, '请先登录');
    }
    $return['username'] = $member->username;
    $return['mobile'] = $member->mobile;
    $return['email'] = $member->email;
    $return['last_login_time'] = $member['last_login_time'] == 0 ? '从未登录' : date('Y-m-d H:i:s', $member['last_login_time']);
    $return['is_set_password'] = $member->password == '' ? 0 : 1;
    $bind_data = model('MemberBind')
      ->where(['uid' => $this->userinfo->uid])
      ->select();
    $return['bind_qq'] = 0;
    $return['bind_sina'] = 0;
    $return['bind_weixin'] = 0;
    $return['bind_qq_nickname'] = '';
    if (!empty($bind_data)) {
      foreach ($bind_data as $key => $value) {
        if ($value['type'] == 'qq') {
          $return['bind_qq'] = 1;
          $return['bind_qq_nickname'] = $value['nickname'];
          continue;
        }
        if ($value['type'] == 'sina') {
          $return['bind_sina'] = 1;
          continue;
        }
        if ($value['type'] == 'weixin' && $value['is_subscribe'] == 1) {
          $return['bind_weixin'] = 1;
          $return['bind_weixin_nickname'] = $value['nickname'];
          continue;
        }
      }
    }
    if ($this->userinfo->utype == 1) {
      $company_profile = model('Company')
        ->field(true)
        ->where('uid', 'eq', $this->userinfo->uid)
        ->find();
      $return['company_auth'] = $company_profile['audit'];
      $return['company_auth_text'] =
        $company_profile['audit'] == 0
        ? '未认证'
        : ($company_profile['audit'] == 1
          ? '已认证'
          : '认证未通过');
    }
    $this->ajaxReturn(200, '获取数据成功', $return);
  }
  /**  修改用户名 */
  public function resetUsername() {
    $d = [
      'username' => input('post.username/s', '', 'trim'),
    ];
    $validate = new \think\Validate([
      'username' => 'require|max:30|checkUsername',
    ]);
    $current_userinfo = $this->userinfo;
    $validate->extend('checkUsername', function ($value) use (
      $current_userinfo
    ) {
      if (fieldRegex($value, 'mobile')) {
        return '用户名不可以是手机号';
      }
      if (fieldRegex($value, 'email')) {
        return '用户名不可以是邮箱';
      }
      $info = model('Member')
        ->where([
          'username' => $value,
        ])
        ->find();
      if ($info !== null && $info->uid != $current_userinfo->uid) {
        return '用户名已被占用';
      }
      return true;
    });

    if (!$validate->check($d)) {
      $this->ajaxReturn(500, $validate->getError());
    }

    model('Member')
      ->where('uid', $this->userinfo->uid)
      ->setField('username', $d['username']);
    $this->writeMemberActionLog($this->userinfo->uid, '修改用户名【新用户名：' . $d['username'] . '】');
    $this->ajaxReturn(200, '修改成功');
  }
  /**  修改手机号 */
  public function resetMobile() {
    $d = [
      'mobile' => input('post.mobile/s', '', 'trim'),
      'code' => input('post.code/s', '', 'trim'),
    ];
    $validate = new \think\Validate([
      'mobile' => 'require|checkMobile',
      'code' => 'require|max:4',
    ]);
    $current_userinfo = $this->userinfo;
    $validate->extend('checkMobile', function ($value) use (
      $current_userinfo
    ) {
      if (fieldRegex($value, 'mobile')) {
        $info = model('Member')
          ->where([
            'mobile' => $value,
          ])
          ->find();
        if ($info !== null && $info->uid != $current_userinfo->uid) {
          return '手机号已被占用';
        }
        return true;
      } else {
        return '请输入正确的手机号码';
      }
    });

    if (!$validate->check($d)) {
      $this->ajaxReturn(500, $validate->getError());
    }
    $auth_result = cache('smscode_' . $d['mobile']);
    if (
      $auth_result === false ||
      $auth_result['code'] != $d['code'] ||
      $auth_result['mobile'] != $d['mobile']
    ) {
      \think\Cache::inc('smscode_error_num_' . $d['mobile']);
      $this->ajaxReturn(500, '验证码错误');
    }
    $error_num = \think\Cache::get(
      'smscode_error_num_' . $d['mobile']
    );
    if ($error_num !== false && $error_num >= 5) {
      $this->ajaxReturn(500, '验证码失效，请重新获取');
    }
    model('Member')
      ->where('uid', $this->userinfo->uid)
      ->setField('mobile', $d['mobile']);
    cache('smscode_' . $d['mobile'], null);
    $this->writeMemberActionLog($this->userinfo->uid, '修改手机号【新手机号：' . $d['mobile'] . '】');
    $this->ajaxReturn(200, '修改成功');
  }

  /**  修改邮箱 */
  public function resetEmail() {
    $d = [
      'email' => input('post.email/s', '', 'trim'),
      'code' => input('post.code/s', '', 'trim'),
    ];
    $validate = new \think\Validate([
      'email' => 'require|checkEmail',
      'code' => 'require|max:4',
    ]);
    $current_userinfo = $this->userinfo;
    $validate->extend('checkEmail', function ($value) use (
      $current_userinfo
    ) {
      if (fieldRegex($value, 'email')) {
        $info = model('Member')
          ->where([
            'email' => $value,
          ])
          ->find();
        if ($info !== null && $info->uid != $current_userinfo->uid) {
          return '邮箱已被占用';
        }
        return true;
      } else {
        return '请输入正确的邮箱';
      }
    });

    if (!$validate->check($d)) {
      $this->ajaxReturn(500, $validate->getError());
    }
    $auth_result = cache('emailcode_' . $d['email']);
    if (
      $auth_result === false ||
      $auth_result['code'] != $d['code'] ||
      $auth_result['email'] != $d['email']
    ) {
      \think\Cache::inc('emailcode_error_num_' . $d['email']);
      $this->ajaxReturn(500, '验证码错误');
    }
    $error_num = \think\Cache::get(
      'emailcode_error_num_' . $d['email']
    );
    if ($error_num !== false && $error_num >= 5) {
      $this->ajaxReturn(500, '验证码失效，请重新获取');
    }
    model('Member')
      ->where('uid', $this->userinfo->uid)
      ->setField('email', $d['email']);
    cache('emailcode_' . $d['email'], null);
    $this->writeMemberActionLog($this->userinfo->uid, '修改邮箱【新邮箱：' . $d['email'] . '】');
    $this->ajaxReturn(200, '修改成功');
  }
  /**  修改密码 */
  public function resetPassword() {
    $d = [
      'old_password' => input('post.old_password/s', '', 'trim'),
      'password' => input('post.password/s', '', 'trim'),
      'password_confirm' => input('post.password_confirm/s', '', 'trim'),
    ];
    $model = model('Member')
      ->where('uid', $this->userinfo->uid)
      ->find();
    $validate = new \think\Validate([
      'old_password' => 'checkOldPassword',
      'password' => 'require|min:6|max:30|confirm',
      'password_confirm' => 'require|min:6|max:30',
    ]);
    $validate->extend('checkOldPassword', function ($value) use ($model) {
      if ($model->password != '' && $value == '') {
        return '请输入旧密码';
      }
      return true;
    });

    if (!$validate->check($d)) {
      $this->ajaxReturn(500, $validate->getError());
    }
    if (
      $model->password != '' &&
      $model->password !=
      $model->makePassword(
        $d['old_password'],
        $model->pwd_hash
      )
    ) {
      $this->ajaxReturn(500, '密码错误');
    }
    $model->pwd_hash = randstr();
    $model->password = $model->makePassword(
      $d['password'],
      $model->pwd_hash
    );
    $model->save();
    $this->writeMemberActionLog($this->userinfo->uid, '修改密码');
    $this->ajaxReturn(200, '修改成功');
  }
  /**  绑定qq */
  public function bindQq() {
    $d = [
      'type' => 'qq',
      'openid' => input('post.openid/s', '', 'trim'),
      'unionid' => input('post.unionid/s', '', 'trim'),
      'nickname' => input('post.nickname/s', '', 'trim'),
      'avatar' => input('post.avatar/s', '', 'trim'),
      'bindtime' => time(),
    ];
    $validate = new \think\Validate([
      'openid' => 'require',
      'unionid' => 'require',
      'nickname' => 'require',
      'avatar' => 'require',
    ]);
    if (!$validate->check($d)) {
      $this->ajaxReturn(500, $validate->getError());
    }

    $bindinfo = model('MemberBind')->where([
      'type' => $d['type'],
      'openid' => ['eq', $d['openid']],
      'unionid' => ['eq', $d['unionid']],
    ])->find();
    if ($bindinfo === null) {
      $sqlarr['uid'] = $this->userinfo->uid;
      $sqlarr['type'] = $d['type'];
      $sqlarr['openid'] = $d['openid'];
      $sqlarr['unionid'] = $d['unionid'];
      $sqlarr['nickname'] = $d['nickname'];
      $sqlarr['avatar'] = $d['avatar'];
      $sqlarr['bindtime'] = $d['bindtime'];
      model('MemberBind')->save($sqlarr);
      model('Task')->doTask(
        $this->userinfo->uid,
        $this->userinfo->utype,
        'bind_qq'
      );
    } else {
      $sqlarr['uid'] = $this->userinfo->uid;
      $sqlarr['nickname'] = $d['nickname'];
      $sqlarr['avatar'] = $d['avatar'];
      $sqlarr['bindtime'] = $d['bindtime'];
      model('MemberBind')->save($sqlarr, ['id' => $bindinfo['id']]);
    }
    $this->writeMemberActionLog($this->userinfo->uid, '绑定QQ');

    $this->ajaxReturn(200, '绑定成功');
  }
  /**  绑定新浪微博 */
  public function bindSina() {
    $d = [
      'type' => 'sina',
      'openid' => input('post.openid/s', '', 'trim'),
      'bindtime' => time(),
    ];
    $validate = new \think\Validate([
      'openid' => 'require',
    ]);
    if (
      model('MemberBind')
      ->where([
        'uid' => ['eq', $this->userinfo->uid],
        'type' => $d['type'],
        'openid' => ['eq', $d['openid']],
      ])
      ->find() === null
    ) {
      $sqlarr['uid'] = $this->userinfo->uid;
      $sqlarr['type'] = $d['type'];
      $sqlarr['openid'] = $d['openid'];
      $sqlarr['unionid'] = '';
      $sqlarr['nickname'] = '';
      $sqlarr['avatar'] = '';
      $sqlarr['bindtime'] = $d['bindtime'];
      model('MemberBind')->save($sqlarr);
      model('Task')->doTask(
        $this->userinfo->uid,
        $this->userinfo->utype,
        'bind_sina'
      );
    }
    $this->writeMemberActionLog($this->userinfo->uid, '绑定新浪微博');
    $this->ajaxReturn(200, '绑定成功');
  }
  /**  绑定微信 */
  public function bindWeixin() {
    $d = [
      'type' => 'weixin',
      'openid' => input('post.openid/s', '', 'trim'),
      'unionid' => input('post.unionid/s', '', 'trim'),
      'nickname' => input('post.nickname/s', '', 'trim'),
      'avatar' => input('post.avatar/s', '', 'trim'),
      'bindtime' => time(),
    ];
    $validate = new \think\Validate([
      'openid' => 'require',
      'unionid' => 'require',
      'nickname' => 'require',
      'avatar' => 'require',
    ]);
    if (
      model('MemberBind')
      ->where([
        'uid' => ['eq', $this->userinfo->uid],
        'type' => $d['type'],
        'openid' => ['eq', $d['openid']],
        'unionid' => ['eq', $d['unionid']],
      ])
      ->find() === null
    ) {
      $sqlarr['uid'] = $this->userinfo->uid;
      $sqlarr['type'] = $d['type'];
      $sqlarr['openid'] = $d['openid'];
      $sqlarr['unionid'] = $d['unionid'];
      $sqlarr['nickname'] = $d['nickname'];
      $sqlarr['avatar'] = $d['avatar'];
      $sqlarr['bindtime'] = $d['bindtime'];
      model('MemberBind')->save($sqlarr);
      model('Task')->doTask(
        $this->userinfo->uid,
        $this->userinfo->utype,
        'bind_weixin'
      );
    }
    $this->writeMemberActionLog($this->userinfo->uid, '绑定微信');
    $this->ajaxReturn(200, '绑定成功');
  }
  /** 解除绑定 */
  public function unbind() {
    $type = input('post.type/s', '', 'trim');
    if ($type == '') {
      $this->ajaxReturn(500, '请选择解绑类型');
    }
    model('MemberBind')->where('type', $type)->where('uid', $this->userinfo->uid)->delete();
    $unbindtype = $type == 'qq' ? 'QQ' : ($type == 'weixin' ? '微信' : '');
    $this->writeMemberActionLog($this->userinfo->uid, '解绑' . $unbindtype);
    $this->ajaxReturn(200, '解绑成功');
  }
  // 会员消息提醒未读查询
  public function msgunread() {
    $where['uid'] = $this->userinfo->uid;
    $unread = model('Message')
      ->field('id', true)
      ->where($where)
      ->where('is_readed', 0)
      ->find();
    $this->ajaxReturn(200, '获取数据成功', $unread === null ? 0 : 1);
  }
  // 会员消息提醒列表
  public function msglist() {
    $where['uid'] = $this->userinfo->uid;
    $current_page = input('get.page/d', 1, 'intval');
    $pagesize = input('get.pagesize/d', 10, 'intval');
    $list = model('Message')
      ->field('uid', true)
      ->where($where)
      ->order('id desc')
      ->page($current_page . ',' . $pagesize)
      ->select();
    foreach ($list as $key => $value) {
      $list[$key]['utype'] = $this->userinfo->utype;
      $list[$key]['type_text'] = model('Message')->map_type[$value['type']];
    }
    model('Message')->where($where)->setField('is_readed', 1);
    $this->ajaxReturn(200, '获取数据成功', ['items' => $list]);
  }
  // 会员消息提醒列表统计
  public function msglistTotal() {
    $where['uid'] = $this->userinfo->uid;
    $total = model('Message')
      ->where($where)
      ->count();
    $this->ajaxReturn(200, '获取数据成功', $total);
  }
  // 会员登录日志列表
  public function loginlog() {
    $where['uid'] = $this->userinfo->uid;
    $current_page = input('get.page/d', 1, 'intval');
    $pagesize = input('get.pagesize/d', 10, 'intval');
    $list = model('MemberActionLog')
      ->field('id,uid,utype', true)
      ->where('is_login', 1)
      ->where($where)
      ->order('id desc')
      ->page($current_page . ',' . $pagesize)
      ->select();
    foreach ($list as $key => $value) {
      $list[$key]['platform_text'] = model('BaseModel')->map_platform[$value['platform']];
    }
    $this->ajaxReturn(200, '获取数据成功', ['items' => $list]);
  }
  // 会员登录日志列表统计
  public function loginlogTotal() {
    $where['uid'] = $this->userinfo->uid;
    $total = model('MemberActionLog')
      ->where('is_login', 1)
      ->where($where)
      ->count();
    $this->ajaxReturn(200, '获取数据成功', $total);
  }
  // 注销账号
  public function cancelApply() {
    $m = model('MemberCancelApply');
    if ($m->where('uid', $this->userinfo->uid)->find()) {
      ext(200, '申请成功');
    }
    $data['uid'] = $this->userinfo->uid;
    $data['addtime'] = time();
    $data['status'] = 0;
    $data['handlertime'] = 0;
    $data['mobile'] = $this->userinfo->mobile;
    $company_profile = model('Company')->where('uid', $this->userinfo->uid)->find();
    if ($company_profile === null) {
      $data['companyname'] = '不详';
      $data['contact'] = '不详';
      $data['regtime'] = '不详';
    } else {
      $data['companyname'] = $company_profile['companyname'];
      $data['regtime'] = date('Y-m-d', $company_profile['addtime']);
      $company_contact = model('CompanyContact')->where('uid', $this->userinfo->uid)->find();
      if ($company_contact === null) {
        $data['contact'] = '不详';
      } else {
        $data['contact'] = $company_contact['contact'];
        $data['mobile'] = $company_contact['mobile'];
      }
    }
    $member = model('Member')->where('uid', $this->userinfo->uid)->find();
    $m->save($data);
    model('AdminNotice')->send(
      11,
      '账号注销申请通知',
      '账号类型:【企业会员】' . "\r\n" .
        '企业名称:【' . $company_profile['companyname'] . '】' . "\r\n" .
        '注册时间:【' . date('Y-m-d H:i:s', $member['reg_time']) . '】' . "\r\n" .
        '联系人:【' . $member['username'] . '】' . "\r\n" .
        '联系方式:【' . $this->userinfo->mobile . '】',
      '账号注销申请通知，请及时跟进（后台->用户->企业信息管理->账号注销申请）'
    );
    $this->writeMemberActionLog($this->userinfo->uid, '提交账号注销申请');
    $this->ajaxReturn(200, '申请成功');
  }
}
