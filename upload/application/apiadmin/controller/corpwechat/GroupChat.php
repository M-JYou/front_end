<?php

namespace app\apiadmin\controller\corpwechat;

use app\common\controller\Backend;
use app\common\lib\corpwechat\ExternalContact;

define('STAFF', 1);
define('EXTERNAL', 2);

class GroupChat extends Backend {
  /** 企业ID
   * @var string
   */
  private $corpId = '';

  /** 应用的凭证密钥
   * @var string
   */
  private $corpSecret = '';


  /** 客户群首页-列表
   * @Method index()
   *
   * @param integer $page_num 当前页
   * @param integer $page_size 每页显示条数
   * @param string $keyword 关键字检索[群昵称|群主]
   * @param string $sort 排序 [默认:ID倒序;create_time:创建时间;member_total:群总人数:]
   * @param array $date_range 时间搜索 [开始时间，结束时间]
   *
   * @return Jsonp
   *
   * @throws null
   *
   * @link {domain}corpwechat/group_chat/index
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/22
   * @noinspection PhpMissingReturnTypeInspection
   */
  public function index() {
    $page_num = input('post.page_num/d', 1, 'intval');
    $page_size = input('post.page_size/d', 10, 'intval');
    $keyword = input('post.keyword/s', '', 'trim');
    $sort = input('post.sort/s', '', 'trim');
    $date_range = input('post.date_range/a', []);

    $map = array(); // 查询条件

    // 1.关键字
    if (isset($keyword) && !empty($keyword)) {
      // 关键字检索[欢迎语名称|消息文本内容|图文消息的描述]
      $map['u.name|g.name'] = ['like', '%' . $keyword . '%'];
    }

    // 2.时间查询
    if (2 == count($date_range)) {
      $start_time = strtotime($date_range[0]);
      $end_time = strtotime($date_range[1]);
      $date_range = [$start_time, $end_time + 86400 - 1];
      $map['g.create_time'] = ['between time', $date_range];
    }

    // 3.排序
    switch ($sort) {
      case  'create_time':
        // 绑定时间排序
        $order = ["g.create_time desc"];
        break;
      case  'member_total':
        // 绑定时间排序
        $order = ["g.member_total desc"];
        break;
      default:
        $order = ["g.id desc"];
        break;
    }

    #  获取字段
    $field = 'u.name as owner_name, 
        g.id,
        g.chat_id,
        g.name,
        g.owner,
        g.member_total,
        g.create_time';
    $list = model('corpwechat.CorpwechatGroupChat')->getList($map, $order, $page_num, $page_size, $field);

    $this->ajaxReturn(200, 'SUCCESS', $list);
  }


  /** 获取客户群统计数据
   * @Method statistics()
   *
   * @param null
   *
   * @return Jsonp
   *
   * @throws null
   *
   * @link {domain}corpwechat/group_chat/statistics
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/22
   */
  public function statistics() {
    $group_chart_total = model('corpwechat.CorpwechatGroupChat')
      ->getTotalGroupChart();

    $member_total = model('corpwechat.CorpwechatGroupUser')
      ->getMemberTotal();

    $de_weight_total = model('corpwechat.CorpwechatGroupUser')
      ->getDeWeightTotal();

    $external_user_total = model('corpwechat.CorpwechatGroupChat')
      ->getExternalUserTotal();


    $return = [
      'group_chart_total' => $group_chart_total, // 客户群总数
      'member_total' => $member_total, // 客户群客户总数
      'de_weight_total' => $de_weight_total, // 客户群去重客户数
      'external_user_total' => $external_user_total, // 客户群好友数
    ];

    $this->ajaxReturn(200, 'SUCCESS', $return);
  }


  /** 同步企业微信客户群
   * @Method synchronization()
   *
   * @param string $chat_id 客户群ID
   *
   * @return Jsonp
   *
   * @throws null
   *
   * @link {domain}corpwechat/group_chat/synchronization
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/22
   */
  public function synchronization() {
    $chatId = input('post.chat_id/s', '', 'trim');

    if (!isset($chatId) || empty($chatId)) {
      $this->ajaxReturn(500, '请选择要同步的群');
    }

    $apiConfig = config('global_config.corpwechat_api');
    if (empty($apiConfig) || !isset($apiConfig) || !is_array($apiConfig)) {
      $this->ajaxReturn(500, '请先完成企业微信配置');
    }

    $is_open = isset($apiConfig['is_open']) ? intval($apiConfig['is_open']) : -1;

    switch ($is_open) {
      case 1:
        $this->corpId = isset($apiConfig['corpid']) ? $apiConfig['corpid'] : '';
        $this->corpSecret = isset($apiConfig['corpsecret']) ? $apiConfig['corpsecret'] : '';
        break;

      default:
        $this->ajaxReturn(500, '请先开启企微服务');
    }
    /** 1.根据客户群ID调用企业微信【API】获取客户群详情 */
    $query = [
      'chat_id' => $chatId,
      'need_name' => 1
    ];
    $externalContact = new ExternalContact($this->corpId, $this->corpSecret);
    $result = $externalContact->groupChatGet($query);

    if (false === $result) {
      // 企业微信【API】调用失败
      $this->ajaxReturn(500, $externalContact->getError());
    }

    if (!isset($result['group_chat']) || empty($result['group_chat'])) {
      $this->ajaxReturn(500, '接口返回为空，同步失败');
    } else {
      $group_chat = $result['group_chat'];
    }

    if (!isset($group_chat['member_list']) || empty($group_chat['member_list'])) {
      $this->ajaxReturn(500, '获取客户群成员列表为空，同步失败');
    } else {
      $member_list = $group_chat['member_list'];
      $group_chat['member_total'] = count($member_list);
      unset($group_chat['member_list']);

      // 清空旧群成员
      model('corpwechat.CorpwechatGroupUser')
        ->where('chat_id', $chatId)
        ->delete();
    }

    // 2.群聊基本信息
    $write_group = model('corpwechat.CorpwechatGroupChat')->write($chatId, $group_chat);
    if (false === $write_group) {
      $this->ajaxReturn(500, '客户群基本信息入库失败，同步失败');
    }

    foreach ($member_list as $member) {
      /**
       * 成员类型。
       * 1 - 企业成员
       * 2 - 外部联系人
       */
      switch ($member['type']) {
        case STAFF:
          // 1-企业成员
          $userInfo = [
            'userid' => $member['userid'],
            'name' => $member['name'],
            'user_type' => $member['type'],
            'register' => 4,
            'unionid' => isset($member['unionid']) ? $member['unionid'] : ''
          ];
          $write_user = model('corpwechat.CorpwechatUserAll')->write($member['userid'], STAFF, $userInfo);
          if (false === $write_user) {
            $this->ajaxReturn(500, '客户群企业成员信息入库失败，同步失败');
          }
          break;

        case EXTERNAL:
          // 2-外部联系人
          $userInfo = [
            'userid' => $member['userid'],
            'name' => $member['name'],
            'user_type' => $member['type'],
            'register' => 3,
            'unionid' => isset($member['unionid']) ? $member['unionid'] : ''
          ];
          $write_user = model('corpwechat.CorpwechatUserAll')->write($member['userid'], EXTERNAL, $userInfo);
          if (false === $write_user) {
            $this->ajaxReturn(500, '客户群外部联系人信息入库失败，同步失败');
          }
          break;
      }

      if ($member['userid'] === $group_chat['owner']) {
        $is_owner = 1;
      } else {
        $is_owner = 0;
      }
      model('corpwechat.CorpwechatGroupUser')
        ->data(
          [
            'chat_id' => $chatId,
            'userid' => $member['userid'],
            'type' => $member['type'],
            'is_owner' => $is_owner,
            'unionid' => isset($member['unionid']) ? $member['unionid'] : '',
            'join_time' => $member['join_time'],
            'join_scene' => $member['join_scene'],
            'invitor' => isset($member['invitor']['userid']) ? $member['invitor']['userid'] : '',
            'nickname' => isset($member['nickname']) ? $member['nickname'] : '',
            'name' => isset($member['name']) ? $member['name'] : ''
          ]
        )
        ->allowField(true)
        ->isUpdate(false)
        ->save();
    }

    $this->ajaxReturn(200, '同步成功');
  }


  /** 客户群详情 - 详情数据
   * @Method details()
   *
   * @param string $chat_id 客户群ID
   *
   * @return Jsonp
   *
   * @throws null
   *
   * @link XXXXXXXXXX
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/22
   */
  public function details() {
    $chatId = input('post.chat_id/s', '', 'trim');

    $isSet = model('corpwechat.CorpwechatGroupChat')->isSetGroupChart($chatId);
    if (false === $isSet) {
      $this->ajaxReturn(500, '要查看的客户群不存在');
    }

    $data = model('corpwechat.CorpwechatGroupChat')
      ->alias('gc')
      ->join('corpwechat_user_all ua', 'ua.userid = gc.owner', 'LEFT')
      ->field('gc.name,
            gc.member_total,
            gc.notice,
            ua.name as owner_name')
      ->where('chat_id', $chatId)
      ->find();

    if (null === $data) {
      $this->ajaxReturn(500, '客户群基本信息异常');
    } else {
      $data = $data->toArray();
    }

    // 群客户数
    $group_external_total = model('corpwechat.CorpwechatGroupUser')
      ->getGroupExternalTotal($chatId);

    // 注册用户数
    $register_num = model('corpwechat.CorpwechatGroupChat')
      ->getRegisterNum($chatId);

    // 今日新增
    $add_change_cnt = model('corpwechat.CorpwechatGroupChat')
      ->getTodayMemChangeCnt($chatId, 1);

    // 今日退群
    $del_change_cnt = model('corpwechat.CorpwechatGroupChat')
      ->getTodayMemChangeCnt($chatId, 2);

    // 好友数
    $external_user_total = model('corpwechat.CorpwechatGroupChat')
      ->getExternalUserTotal($chatId);

    $return = [
      'name' => $data['name'], // 群名
      'member_total' => $data['member_total'], // 总人数
      'notice' => $data['notice'], // 群公告
      'owner_name' => $data['owner_name'], // 群主
      'group_external_total' => $group_external_total, // 群客户数
      'register_num' => $register_num, // 注册用户数
      'add_change_cnt' => $add_change_cnt, // 今日新增
      'del_change_cnt' => $del_change_cnt, // 今日退群
      'external_user_total' => $external_user_total, // 好友数
    ];

    $this->ajaxReturn(200, 'SUCCESS', $return);
  }


  /** 客户群详情 - 群成员列表
   * @Method details()
   *
   * @param string $chat_id 客户群ID
   * @param integer $page_num 当前页
   * @param integer $page_size 每页显示条数
   * @param string $keyword 关键字检索[在群里的昵称|外部联系人名称]
   * @param array $date_range 时间搜索 [开始时间，结束时间]
   *
   * @return Jsonp
   *
   * @throws null
   *
   * @link {domain}corpwechat/group_chat/details
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/22
   */
  public function groupUserList() {
    $chatId = input('post.chat_id/s', '', 'trim');
    $page_num = input('post.page_num/d', 1, 'intval');
    $page_size = input('post.page_size/d', 10, 'intval');
    $keyword = input('post.keyword/s', '', 'trim');
    $sort = input('post.sort/s', '', 'trim');
    $date_range = input('post.date_range/a', []);

    $map = array(); // 查询条件

    $isSet = model('corpwechat.CorpwechatGroupChat')
      ->isSetGroupChart($chatId);
    if (false === $isSet) {
      $this->ajaxReturn(500, '要查看的客户群不存在');
    } else {
      $map['gu.chat_id'] = ['=', $chatId];
    }


    // 1.关键字
    if (isset($keyword) && !empty($keyword)) {
      // 关键字检索[在群里的昵称|外部联系人名称]
      $map['gu.name|gu.nickname'] = ['like', '%' . $keyword . '%'];
    }

    // 2.时间查询
    if (2 == count($date_range)) {
      $start_time = strtotime($date_range[0]);
      $end_time = strtotime($date_range[1]);
      $date_range = [$start_time, $end_time + 86400 - 1];
      $map['gu.join_time'] = ['between time', $date_range];
    }

    $order = ['gu.is_owner DESC', 'gu.id DESC'];

    #  获取字段
    $field = 'gu.userid, 
        gu.is_owner,
        gu.type,
        gu.join_time,
        gu.join_scene,
        gu.name,
        gu.nickname,
        IFNULL(l.day,0) as day,
        CASE
            WHEN (gu.type = 1) AND (m.utype IS NULL)
                THEN 0
            WHEN (gu.type = 2) AND (m.utype IS NULL)
                THEN 3
            ELSE m.utype 
            END as register';
    $list = model('corpwechat.CorpwechatGroupUser')
      ->getList($map, $order, $page_num, $page_size, $field);

    $this->ajaxReturn(200, 'SUCCESS', $list);
  }
}
