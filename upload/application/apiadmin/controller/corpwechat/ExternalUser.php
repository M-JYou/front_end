<?php

namespace app\apiadmin\controller\corpwechat;

use app\common\controller\Backend;
use think\Db;

class ExternalUser extends Backend {
  /** 企微客户客户管理-首页
   * @Method index()
   *
   * @param integer $page_num 当前页
   * @param integer $page_size 每页显示条数
   * @param string $userid 所属员工[企业微信成员UserID]
   * @param string $register 注册状态[3:非平台用户;1:企业用户;2:个人用户;]
   * @param string $keyword 关键字检索[外部联系人名称]
   * @param array $tag_name 客户标签/标签组名称
   * @param array $tag_id 客户标签ID[空:为标签组筛选;非空:标签筛选;]
   *
   * @return Jsonp
   *
   * @throws null
   *
   * @link {domain}corpwechat/external_user/index
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/10
   */
  public function index() {
    $map = array(); // 查询条件

    // 1.所属员工
    $userid = input('post.userid/s', '', 'trim');
    if (isset($userid) && !empty($userid)) {
      // 所属员工[企业微信成员UserID]
      $map['s.userid'] = ['=', $userid];
    }

    // 2.用户状态 - 注册状态[3:非平台用户;1:企业用户;2:个人用户;]
    $register = input('post.register/d', 0, 'intval');
    switch ($register) {
      case 1:
        // 1:企业用户;
      case 2:
        // 2:个人用户;
        $map['m.utype'] = ['=', $register];
        break;

      case 3:
        // 3:非平台用户
        $map['m.utype'] = ['EXP', Db::raw(' IS NULL ')];
        break;

      case 0:
      default:
        // 全部
        break;
    }

    // 3.客户标签
    $tag_name = input('post.tag_name/s', '', 'trim');
    $tag_id = input('post.tag_id/s', '', 'trim');
    if (isset($tag_name) && !empty($tag_name)) {
      if (isset($tag_id) && !empty($tag_id)) {
        // 标签
        $map['e.tags$."' . $tag_name . '"'] = ['=', $tag_id];
      } else {
        // 标签组
        $map['e.tag_group$."' . $tag_name . '"'] = ['=', $tag_name];
      }
    }


    // 4.关键字
    $keyword = input('post.keyword/s', '', 'trim');
    if (isset($keyword) && !empty($keyword)) {
      // 关键字检索[外部联系人名称]
      $map['u.name'] = ['like', "%{$keyword}%"];
    }

    // 5.排序
    $sort = input('post.sort/s', '', 'trim');
    switch ($sort) {
      case  'create_time':
        // 绑定时间排序
        $order = ["e.{$sort} DESC"];
        break;
      case  'update_time':
        // 更新时间排序
        $order = ["e.{$sort} DESC"];
        break;
      case  'add_time':
        // 添加时间排序
        $order = ["e.{$sort} DESC"];
        break;
      default:
        $order = ['e.create_time DESC'];
        break;
    }

    $page_num = input('post.page_num/d', 1, 'intval');
    $page_size = input('post.page_size/d', 10, 'intval');

    #  获取字段
    $field = 'e.id as external_id,
            e.external_user_id,
            u.name as external_name,
            u.thumb_avatar as external_avatar,
            u.gender as external_gender,
            s.name as staff_name,
            IFNULL(m.utype,3) as register,
            e.add_way,
            e.add_time,
            e.update_time,
            e.tags';
    $list = model('corpwechat.CorpwechatExternalUser')
      ->getList($map, $order, $page_num, $page_size, $field);

    $this->ajaxReturn(200, 'SUCCESS', $list);
  }


  /** 获取企微客户计数据
   * @Method statistics()
   *
   * @param null
   *
   * @return Jsonp
   *
   * @throws null
   *
   * @link {domain}corpwechat/external_user/statistics
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/23
   */
  public function statistics() {
    $external_user_total = model('corpwechat.CorpwechatExternalUser')
      ->getExternalUserTotal(false);

    $de_weight_total = model('corpwechat.CorpwechatExternalUser')
      ->getExternalUserTotal(true);

    $register_external_total = model('corpwechat.CorpwechatExternalUser')
      ->getRegisterExternalUser();

    $uid_list = model('corpwechat.CorpwechatExternalUser')
      ->alias('eu')
      ->join('corpwechat_user_all ua', 'ua.userid = eu.external_user_id', 'LEFT')
      ->join('member_bind mb', 'mb.unionid = ua.unionid', 'LEFT')
      ->join('member m', 'm.uid = mb.uid', 'LEFT')
      ->where('m.utype', 'IN', [1, 2])
      ->column('mb.uid');

    // 日活数（DAU）
    $day_active = model('MemberActionLog')
      ->whereTime('addtime', "today")
      ->where('is_login', 1)
      ->where('uid', 'IN', $uid_list)
      ->field('id')
      ->group('uid')
      ->count('id');

    // 周活数（WAU）
    $week_active = model('MemberActionLog')
      ->whereTime('addtime', "week")
      ->where('is_login', 1)
      ->where('uid', 'IN', $uid_list)
      ->field('id')
      ->group('uid')
      ->count('id');

    // 月活数（MAU）
    $month_active = model('MemberActionLog')
      ->whereTime('addtime', "month")
      ->where('is_login', 1)
      ->where('uid', 'IN', $uid_list)
      ->field('id')
      ->group('uid')
      ->count('id');

    $return = [
      'external_user_total' => $external_user_total, // 总客户数
      'de_weight_total' => $de_weight_total, // 去重客户数
      'register_external_total' => $register_external_total, // 注册客户
      'day_active' => $day_active, // 日活数（DAU）
      'week_active' => $week_active, // 周活数（WAU）
      'month_active' => $month_active, // 月活数（MAU）
    ];

    $this->ajaxReturn(200, 'SUCCESS', $return);
  }


  /** 企微客户详情
   * @Method details()
   *
   * @param string $external_user_id 企微客户UserID
   *
   * @return Jsonp
   *
   * @throws \think\Exception
   * @throws \think\db\exception\DataNotFoundException
   * @throws \think\db\exception\ModelNotFoundException
   * @throws \think\exception\DbException
   *
   * @link {domain}corpwechat/external_user/details
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/24
   */
  public function details() {
    $external_user_id = input('post.external_user_id/s', '', 'trim');

    $isSet = model('corpwechat.CorpwechatUserAll')
      ->isSetUserID($external_user_id, 2);

    if (false === $isSet) {
      $this->ajaxReturn(500, '客户不存在');
    }

    // 1.获取客户基本信息
    $external_info = model('corpwechat.CorpwechatUserAll')
      ->alias('ua')
      ->where('userid', $external_user_id)
      ->join('member_bind mb', 'mb.unionid = ua.unionid', 'LEFT')
      ->join('member m', 'm.uid = mb.uid', 'LEFT')
      ->field('ua.unionid, 
                ua.name, 
                ua.thumb_avatar,
                ua.external_type,
                ua.gender,
                ua.corp_name,
                ua.corp_full_name,
                IFNULL(m.utype,3) as register')
      ->find();
    if (null === $external_info) {
      $this->ajaxReturn(500, '客户基本信息异常');
    } else {
      $external_info = $external_info->toArray();
    }

    // 2.获取客户平台绑定信息
    if (isset($external_info['unionid']) && !empty($external_info['unionid'])) {
      $bind_info = model('MemberBind')
        ->alias('mb')
        ->join('member m', 'm.uid = mb.uid', 'LEFT')
        ->where('unionid', $external_info['unionid'])
        ->field('mb.uid as uid, m.utype as utype')
        ->find();
    }

    // 3.如果存在平台绑定，则获取用户平台数据
    if (isset($bind_info) && !empty($bind_info)) {
      $member_info = model('Member')
        ->where('uid', $bind_info['uid'])
        ->where('utype', $bind_info['utype'])
        ->field('uid, utype, username, mobile, reg_time')
        ->find();
    }
    if (isset($member_info) && !empty($member_info)) {
      $member_info = $member_info->toArray();

      switch ($bind_info['utype']) {
        case 2:
          // 个人用户
          $resume = model('Resume')
            ->field('id,fullname')
            ->where('uid', $bind_info['uid'])
            ->find();
          if (null === $resume) {
            $member_info['show_username'] = $member_info['mobile'];
            $member_info['complete_percent'] = 0;
          } else {
            $member_info['show_username'] = $resume->fullname;
            $member_info['complete_percent'] = model('Resume')->countCompletePercent($resume->id, $bind_info['uid']);
          }
          break;

        case 1:
          // 企业用户
          $company = model('Company')
            ->field('id,companyname')
            ->where('uid', $bind_info['uid'])
            ->find();
          $member_info['show_username'] = $company === null ? $member_info['mobile'] : $company->companyname;
          break;

        default:
          $this->ajaxReturn(500, '客户注册信息异常');
          break;
      }
    } else {
      $member_info = [];
    }

    // 4.获取客户所属员工
    $external_user = model('corpwechat.CorpwechatExternalUser')
      ->alias('eu')
      ->join('corpwechat_user_all ua', 'ua.userid = eu.userid', 'LEFT')
      ->field('eu.userid, ua.name as staff_name, eu.add_way, eu.add_time')
      ->where('external_user_id', $external_user_id)
      ->select();
    if (null === $external_user) {
      $external_user = [];
    } else {
      $external_user = $external_user->toArray();
    }

    // 5.获取客户所在群聊
    $group_chat = model('corpwechat.CorpwechatGroupUser')
      ->alias('gu')
      ->join('corpwechat_group_chat gc', 'gc.chat_id = gu.chat_id', 'LEFT')
      ->join('corpwechat_user_all ua', 'ua.userid = gc.owner', 'LEFT')
      ->where('gu.userid', $external_user_id)
      ->field('gc.chat_id,
                gc.name,
                gc.member_total,
                ua.name as owner_name,
                gu.join_time')
      ->select();
    if (null === $group_chat) {
      $group_chat = [];
    } else {
      $group_chat = $group_chat->toArray();
    }

    // 6.获取客户用户动态[倒叙排列]
    $external_log = model('corpwechat.CorpwechatExternalLog')
      ->field('content, create_time')
      ->where('external_user_id', $external_user_id)
      ->order('id DESC')
      ->select();
    if (null === $external_log) {
      $external_log = [];
    } else {
      $external_log = $external_log->toArray();
    }

    $return = [
      'external_info' => $external_info, // 客户基本信息
      'member_info' => $member_info, // 客户平台信息
      'external_user' => $external_user, // 所属员工
      'group_chat' => $group_chat, // 所在群聊
      'external_log' => $external_log, // 用户动态
    ];

    $this->ajaxReturn(200, 'SUCCESS', $return);
  }


  /** 企微客户标签信息
   * @Method externalTag()
   *
   * @param string $external_user_id 企微客户UserID
   * @param string $userid 所属员工[企业微信成员UserID](默认空，返回客户全部标签；有则返回UserID员工所打标签)
   *
   * @return Jsonp
   *
   * @throws null
   *
   * @link {domain}corpwechat/external_user/externalTag
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/24
   */
  public function externalTag() {
    $external_user_id = input('post.external_user_id/s', '', 'trim');
    $userid = input('post.userid/s', '', 'trim');

    $isSet = model('corpwechat.CorpwechatUserAll')
      ->isSetUserID($external_user_id, 2);
    if (false === $isSet) {
      $this->ajaxReturn(500, '客户不存在');
    }

    // 模型`CorpwechatExternalTag`查询
    $model = model('corpwechat.CorpwechatExternalTag');
    $model->where('external_user_id', $external_user_id);
    if (isset($userid) && !empty($userid)) {
      $model->where('userid', $userid);
    }
    $tags = $model->column('tag_name');

    $this->ajaxReturn(200, 'SUCCESS', ['tags' => $tags]);
  }


  /** 获取企微客户所属员工
   * @Method externalUser()
   *
   * @param string $external_user_id 企微客户UserID
   *
   * @return Jsonp
   *
   * @throws \think\db\exception\DataNotFoundException
   * @throws \think\db\exception\ModelNotFoundException
   * @throws \think\exception\DbException
   *
   * @link {domain}corpwechat/external_user/externalUser
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/24
   */
  public function externalUser() {
    $external_user_id = input('post.external_user_id/s', '', 'trim');

    $isSet = model('corpwechat.CorpwechatUserAll')
      ->isSetUserID($external_user_id, 2);
    if (false === $isSet) {
      $this->ajaxReturn(500, '客户不存在');
    }

    $external_user = model('corpwechat.CorpwechatExternalUser')
      ->alias('eu')
      ->join('corpwechat_user_all ua', 'ua.userid = eu.userid', 'LEFT')
      ->field('eu.userid, ua.name as staff_name')
      ->where('external_user_id', $external_user_id)
      ->select();
    if (null === $external_user) {
      $external_user = [];
    } else {
      $external_user = $external_user->toArray();
    }

    $this->ajaxReturn(200, 'SUCCESS', ['external_user' => $external_user]);
  }


  /** 获取企微客户用户行为分析
   * @Method externalUser()
   *
   * @param string $external_user_id 企微客户UserID
   * @param string $date_span 时间跨度
   *
   * @return Jsonp
   *
   * @throws \think\db\exception\DataNotFoundException
   * @throws \think\db\exception\ModelNotFoundException
   * @throws \think\exception\DbException
   *
   * @link {domain}corpwechat/external_user/active
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/24
   */
  public function active() {
    $external_user_id = input('post.external_user_id/s', '', 'trim');
    $date_span = input('post.date_span/d', 7, 'intval');

    $isSet = model('corpwechat.CorpwechatUserAll')
      ->isSetUserID($external_user_id, 2);
    if (false === $isSet) {
      $this->ajaxReturn(500, '客户不存在');
    }

    // 1.获取客户基本信息
    $external_info = model('corpwechat.CorpwechatUserAll')
      ->alias('ua')
      ->where('ua.userid', $external_user_id)
      ->join('member_bind mb', 'mb.unionid = ua.unionid', 'LEFT')
      ->join('member m', 'm.uid = mb.uid', 'LEFT')
      ->field('mb.uid, IFNULL(m.utype,3) as register')
      ->find();
    if (null === $external_info) {
      $this->ajaxReturn(500, '客户暂未绑定平台账户');
    }

    if (!in_array($date_span, [7, 15, 30])) {
      $this->ajaxReturn(500, '时间范围错误');
    } else {
      $date_span -= 1;
    }

    switch ($external_info->register) {
      case 1;
        // 企业会员
        $return = $this->_companyActive($external_info->uid, $date_span);
        break;
      case 2;
        // 个人会员
        $return = $this->_memberActive($external_info->uid, $date_span);
        break;

      case 3;
        // 非平台用户
        $this->ajaxReturn(500, '客户暂未绑定平台账户');
        break;

      default:
        // 非平台用户
        $this->ajaxReturn(500, '客户绑定状态异常');
        break;
    }

    $this->ajaxReturn(200, 'SUCCESS', $return);
  }

  /** 个人会员-用户行为分析
   * @Method _memberActive()
   *
   * @param $uId
   * @param $dateSpan
   *
   * @return array
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/24
   */
  public function _memberActive($uId, $dateSpan) {
    $return = [
      'legend' => ['登录次数', '简历投递数', '简历被查看数', '刷新简历'],
      'xAxis' => [],
      'series' => []
    ];

    // 登录次数
    $member_login_data = model('MemberActionLog')
      ->whereTime('addtime', "-{$dateSpan} day")
      ->where('is_login', 1)
      ->where('uid', $uId)
      ->group('time')
      ->column(
        'UNIX_TIMESTAMP(FROM_UNIXTIME(`addtime`, "%Y%m%d")) as time,count(*) as num'
      );

    // 简历投递数
    $job_apply_data = model('JobApply')
      ->whereTime('addtime', "-{$dateSpan} day")
      ->where('personal_uid', $uId)
      ->group('time')
      ->column(
        'UNIX_TIMESTAMP(FROM_UNIXTIME(`addtime`, "%Y%m%d")) as time,count(*) as num'
      );

    // 简历被查看数
    $resume_view_data = model('ViewResume')
      ->whereTime('addtime', "-{$dateSpan} day")
      ->where('personal_uid', $uId)
      ->group('time')
      ->column(
        'UNIX_TIMESTAMP(FROM_UNIXTIME(`addtime`, "%Y%m%d")) as time,count(*) as num'
      );

    // 刷新简历
    $refresh_resume_data = model('RefreshResumeLog')
      ->whereTime('addtime', "-{$dateSpan} day")
      ->where('uid', $uId)
      ->group('time')
      ->column(
        'UNIX_TIMESTAMP(FROM_UNIXTIME(`addtime`, "%Y%m%d")) as time,count(*) as num'
      );

    $end_time = strtotime('today');
    $start_time = $end_time - 86400 * $dateSpan;

    for ($i = $start_time; $i <= $end_time; $i += 86400) {
      $return['xAxis'][] = date('m/d', $i);

      // 1.登录次数
      $return['series'][0][] = isset($member_login_data[$i])
        ? $member_login_data[$i]
        : 0;

      // 2.简历投递数
      $return['series'][1][] = isset($job_apply_data[$i])
        ? $job_apply_data[$i]
        : 0;

      // 3.简历被查看数
      $return['series'][2][] = isset($resume_view_data[$i])
        ? $resume_view_data[$i]
        : 0;

      // 4.刷新简历
      $return['series'][3][] = isset($refresh_resume_data[$i])
        ? $refresh_resume_data[$i]
        : 0;
    }
    return $return;
  }

  /** 企业会员-用户行为分析
   * @Method _memberActive()
   *
   * @param $uId
   * @param $dateSpan
   *
   * @return array
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/24
   */
  public function _companyActive($cId, $dateSpan) {
    $return = [
      'legend' => ['登录次数', '发布职位', '刷新职位', '查看简历', '下载简历'],
      'xAxis' => [],
      'series' => []
    ];

    // 1.登录次数
    $member_login_data = model('MemberActionLog')
      ->whereTime('addtime', "-{$dateSpan} day")
      ->where('is_login', 1)
      ->where('uid', $cId)
      ->group('time')
      ->column(
        'UNIX_TIMESTAMP(FROM_UNIXTIME(`addtime`, "%Y%m%d")) as time,count(*) as num'
      );

    // 2.发布职位
    $job_add_data = model('Job')
      ->whereTime('addtime', "-{$dateSpan} day")
      ->where('uid', $cId)
      ->group('time')
      ->column(
        'UNIX_TIMESTAMP(FROM_UNIXTIME(`addtime`, "%Y%m%d")) as time,count(*) as num'
      );

    // 3.刷新职位
    $job_refresh_data = model('RefreshJobLog')
      ->whereTime('addtime', "-{$dateSpan} day")
      ->where('uid', $cId)
      ->group('time')
      ->column(
        'UNIX_TIMESTAMP(FROM_UNIXTIME(`addtime`, "%Y%m%d")) as time,count(*) as num'
      );

    // 4.查看简历
    $view_resume_data = model('ViewResume')
      ->whereTime('addtime', "-{$dateSpan} day")
      ->where('company_uid', $cId)
      ->group('time')
      ->column(
        'UNIX_TIMESTAMP(FROM_UNIXTIME(`addtime`, "%Y%m%d")) as time,count(*) as num'
      );

    // 5.下载简历
    $down_resume_data = model('CompanyDownResume')
      ->whereTime('addtime', "-{$dateSpan} day")
      ->where('uid', $cId)
      ->group('time')
      ->column(
        'UNIX_TIMESTAMP(FROM_UNIXTIME(`addtime`, "%Y%m%d")) as time,count(*) as num'
      );

    $end_time = strtotime('today');
    $start_time = $end_time - 86400 * $dateSpan;

    for ($i = $start_time; $i <= $end_time; $i += 86400) {
      $return['xAxis'][] = date('m/d', $i);

      // 1.登录次数
      $return['series'][0][] = isset($member_login_data[$i])
        ? $member_login_data[$i]
        : 0;

      // 2.发布职位
      $return['series'][1][] = isset($job_add_data[$i])
        ? $job_add_data[$i]
        : 0;

      // 3.刷新职位
      $return['series'][2][] = isset($job_refresh_data[$i])
        ? $job_refresh_data[$i]
        : 0;

      // 4.查看简历
      $return['series'][3][] = isset($view_resume_data[$i])
        ? $view_resume_data[$i]
        : 0;

      // 5.下载简历
      $return['series'][4][] = isset($down_resume_data[$i])
        ? $down_resume_data[$i]
        : 0;
    }
    return $return;
  }
}
