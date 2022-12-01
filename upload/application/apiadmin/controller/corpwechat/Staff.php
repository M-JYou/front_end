<?php

namespace app\apiadmin\controller\corpwechat;


use app\common\controller\Backend;
use app\common\lib\corpwechat\ExternalContact;
use Exception;
use think\helper\Time;

class Staff extends Backend {
  /** 企业ID
   * @var string
   */
  private $corpId = '';

  /** 应用的凭证密钥
   * @var string
   */
  private $corpSecret = '';


  /** 员工列表
   * @Method index()
   *
   * @param integer $page_num 当前页
   * @param integer $page_size 每页显示条数
   * @param string $keyword 关键字检索[成员名称]
   * @param string $sort 排序 [默认:ID倒序;create_time:绑定时间;total_external:客户数量]
   *
   * @return Jsonp
   *
   * @link {domain}corpwechat/staff/index
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/3
   */
  public function index() {
    $page_num = input('post.page_num/d', 1, 'intval');
    $page_size = input('post.page_size/d', 10, 'intval');
    $keyword = input('post.keyword/s', '', 'trim');
    $sort = input('post.sort/s', '', 'trim');

    $map = array();

    // 1.关键字
    if (isset($keyword) && !empty($keyword)) {
      // 关键字检索[成员名称]
      $map['u.name'] = ['like', '%' . $keyword . '%'];
    }

    // 2.排序
    switch ($sort) {
      case  'create_time':
        // 绑定时间排序
        $order = ["s.{$sort} DESC"];
        break;
      case  'total_external':
        // 客户数量排序
        $order = ["c.{$sort} DESC"];
        break;
      default:
        $order = ['s.id DESC'];
        break;
    }

    // 查询字段
    $field = 's.userid,
        u.name,
        u.thumb_avatar,
        IFNULL(c.total_external,0) as total_external,
        IFNULL(a.bind_qywx,0) as is_bind,
        IFNULL(a.bind_qywx_time,0) as bind_time';

    try {
      $list = model('corpwechat.CorpwechatStaff')
        ->getList($map, $order, $page_num, $page_size, $field);
      if (false === $list) {
        throw new Exception(model('corpwechat.CorpwechatStaff')->getError());
      }
    } catch (Exception $e) {
      $this->ajaxReturn(500, $e->getMessage());
    }

    $this->ajaxReturn(200, 'SUCCESS', $list);
  }


  /** 员工详情
   * @Method details()
   *
   * @param string $user_id 企业微信成员UserID
   *
   * @return Jsonp
   *
   * @throws null
   *
   * @link {domain}corpwechat/staff/details
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/8
   */
  public function details() {
    $apiConfig = config('global_config.corpwechat_api');
    if (empty($apiConfig) || !isset($apiConfig) || !is_array($apiConfig)) {
      $this->ajaxReturn(500, '请先完成企业微信配置');
    }

    $is_open = $apiConfig['is_open'] ? intval($apiConfig['is_open']) : -1;

    switch ($is_open) {
      case 1:
        $this->corpId = isset($apiConfig['corpid']) ? $apiConfig['corpid'] : '';
        $this->corpSecret = isset($apiConfig['corpsecret']) ? $apiConfig['corpsecret'] : '';
        break;

      case 2:
      case -1:
      default:
        $this->ajaxReturn(500, '请先开启企微服务');
        break;
    }

    $user_id = input('post.user_id/s', '', 'trim');
    if (!isset($user_id) || empty($user_id)) {
      $this->ajaxReturn(500, '请选择要查看的员工');
    }

    $date_range = input('post.date_range/a', []);
    if (2 == count($date_range)) {
      if (empty($date_range[0]) || empty($date_range[1])) {
        $this->ajaxReturn(500, '请选择筛选日期区间');
      }
      $start_time = strtotime($date_range[0]);
      $end_time = strtotime($date_range[1]);
      if ($start_time > time() || $end_time > time()) {
        $this->ajaxReturn(500, '不可查看今日之后的时间');
      }
      //计算天数
      $time_diff = $end_time - $start_time;
      $days = intval($time_diff / 86400);
      if (30 <= $days) {
        $this->ajaxReturn(500, '最大查询跨度为30天');
      }
      $time_180 = Time::daysAgo(180);
      if ($start_time < $time_180 || $end_time < $time_180) {
        $this->ajaxReturn(500, '最多可查询最近180天内的数据');
      }
    } else {
      $this->ajaxReturn(500, '请选择正确的筛选日期区间');
    }


    $externalContact = new ExternalContact($this->corpId, $this->corpSecret);
    $behavior_info = [
      'userid' => [$user_id],
      'start_time' => $start_time,
      'end_time' => $end_time
    ];
    $result = $externalContact->getUserBehaviorData($behavior_info);

    if (false === $result) {
      $this->ajaxReturn(500, $externalContact->getError());
    }

    if (!isset($result['behavior_data']) || empty($result['behavior_data'])) {
      $this->ajaxReturn(500, '员工数据统计失败');
    } else {
      $items = is_array($result['behavior_data']) ? $result['behavior_data'] : [];
    }

    // 获取统计数据
    $new_apply_total = 0; // 发起申请数
    $new_contact_total = 0; // 新增客户数
    $chat_total = 0; // 聊天总数
    $message_total = 0; // 发送消息数
    $negative_feedback_total = 0; // 删除/拉黑成员的客户数(流失总数)
    foreach ($items as $data) {
      $new_apply_total += isset($data['new_apply_cnt']) ? $data['new_apply_cnt'] : 0;
      $new_contact_total += isset($data['new_contact_cnt']) ? $data['new_contact_cnt'] : 0;
      $chat_total += isset($data['chat_cnt']) ? $data['chat_cnt'] : 0;
      $message_total += isset($data['message_cnt']) ? $data['message_cnt'] : 0;
      $negative_feedback_total += isset($data['negative_feedback_cnt']) ? $data['negative_feedback_cnt'] : 0;
    }
    rsort($items);

    $return = [
      'items' => $items,
      'statistics' => [
        'new_apply_total' => $new_apply_total,
        'new_contact_total' => $new_contact_total,
        'chat_total' => $chat_total,
        'message_total' => $message_total,
        'negative_feedback_total' => $negative_feedback_total
      ],
      'date_range' => $date_range
    ];

    $this->ajaxReturn(200, 'SUCCESS', $return);
  }
}
