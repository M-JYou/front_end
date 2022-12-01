<?php

namespace app\apiadmin\controller\corpwechat;

use app\common\controller\Backend;
use app\common\lib\corpwechat\ExternalContact;
use think\Db;
use think\Exception;
use think\Validate;

define('CHANNEL_WORDS', 1); // 1:渠道欢迎语
define('DEFAULT_WORDS', 2); // 2:默认欢迎语
define('NO_WORDS', 3); // 3:不使用欢迎语
define('TEXT_TYPE', 1); // 纯文本-附件类型-text
define('IMAGE_TYPE', 2); // 图片-附件类型-image
define('LINK_TYPE', 3); // 链接-附件类型-link

class Channel extends Backend {
  /** 企业ID
   * @var string
   */
  private $corpId = '';

  /** 应用ID
   * @var string
   */
  private $agentId = '';

  /** 应用的凭证密钥
   * @var string
   */
  private $corpSecret = '';

  /** 通讯录的凭证秘钥
   * @var string
   */
  private $customerContactSecret = '';

  /** 新增/编辑渠道活码分组【渠道活码】
   * @Method editGroup()
   *
   * @param integer $group_id 渠道活码分组ID
   * @param string $group_name 渠道活码分组名称
   *
   * @return Jsonp
   *
   * @throws null
   *
   * @link {domain}corpwechat/channel/editGroup
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/11
   */
  public function editGroup() {
    // 接受请求参数
    $group_id = input('post.group_id/d', 0, 'intval');
    $group_name = input('post.group_name/s', '', 'trim');

    if (1 === $group_id) {
      $this->ajaxReturn(500, '默认分组不可编辑');
    }

    $data = [
      'group_id' => $group_id,
      'group_name' => $group_name
    ];

    // Validate验证规则
    $rule = [
      'group_id' => 'require|integer',
      'group_name' => 'require|chsAlphaNum|length:1,10'
    ];

    // Validate报错信息
    $msg = [
      'group_name.require' => '请输入分组名称',
      'group_name.chsDash' => '分组名称只能是汉字、字母和数字',
      'group_name.length' => '分组名称长度限制为1~10',
    ];

    // 实例化验证类
    $validate = new Validate($rule, $msg);
    if (!$validate->check($data)) {
      $this->ajaxReturn(500, $validate->getError());
    }

    $isRepeat = model('corpwechat.CorpwechatChannelGroup')
      ->where('name', '=', $group_name)
      ->where('id', '<>', $group_id)
      ->find();

    if (isset($isRepeat) && !empty($isRepeat)) {
      $this->ajaxReturn(500, '分组名称已存在');
    }

    if (isset($group_id) && !empty($group_id)) {
      /** 修改渠道活码分组 */
      $isSet = model('corpwechat.CorpwechatChannelGroup')->find($group_id);
      if (!isset($isSet) || empty($isSet)) {
        $this->ajaxReturn(500, '分组不存在');
      }

      Db::startTrans();
      try {
        $update = model('corpwechat.CorpwechatChannelGroup')
          ->allowField(true)
          ->isUpdate(true)
          ->save(
            [
              'name' => $group_name
            ],
            [
              'id' => $group_id
            ]
          );
        if (false === $update) {
          throw new \Exception(model('corpwechat.CorpwechatChannelGroup')->getError());
        }

        // 日志
        $log_result = model('AdminLog')->record(
          '修改企微渠道活码分组【ID:' . $group_id . ';name:' . $group_name . '】',
          $this->admininfo
        );
        if (false === $log_result) {
          throw new \Exception(model('AdminLog')->getError());
        }

        // 提交事务
        Db::commit();
      } catch (\Exception $e) {
        // 回滚事务
        Db::rollBack();
        $this->ajaxReturn(500, $e->getMessage());
      }

      $this->ajaxReturn(200, '修改成功');
    } else {
      /** 新增渠道活码分组 */
      Db::startTrans();
      try {
        $insert = model('corpwechat.CorpwechatChannelGroup')
          ->allowField(true)
          ->isUpdate(false)
          ->save(
            [
              'name' => $group_name
            ],
            false
          );
        if (false === $insert) {
          throw new \Exception(model('corpwechat.CorpwechatChannelGroup')->getError());
        }
        $insert_id = model('corpwechat.CorpwechatChannelGroup')->getLastInsID();

        // 日志
        $log_result = model('AdminLog')->record(
          '添加企微渠道活码分组【ID:' . $insert_id . ';name:' . $group_name . '】',
          $this->admininfo
        );
        if (false === $log_result) {
          throw new \Exception(model('AdminLog')->getError());
        }

        // 提交事务
        Db::commit();
      } catch (\Exception $e) {
        // 回滚事务
        Db::rollBack();
        $this->ajaxReturn(500, $e->getMessage());
      }

      $this->ajaxReturn(200, '添加成功');
    }
  }


  /** 获取渠道活码分组【渠道活码】
   * @Method groupList()
   *
   * @param null
   *
   * @return Jsonp
   *
   * @throws null
   *
   * @link {domain}corpwechat/channel/groupList
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/11
   */
  public function groupList() {
    $list = model('corpwechat.CorpwechatChannelGroup')->getCache();
    $this->ajaxReturn(200, 'SUCCESS', $list);
  }


  /** 获取渠道活码分组详情【渠道活码】
   * @Method groupDetails()
   *
   * @param integer $group_id 渠道活码分组ID
   *
   * @return Jsonp
   *
   * @throws null
   *
   * @link {domain}corpwechat/channel/groupDetails
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/11
   */
  public function groupDetails() {
    $group_id = input('post.group_id/d', 0, 'intval');
    if (!isset($group_id) || empty($group_id)) {
      $this->ajaxReturn(500, '请选择分组');
    }

    $data = model('corpwechat.CorpwechatChannelGroup')
      ->field('id as group_id, name as group_name')
      ->find($group_id);

    if (isset($data) && !empty($data)) {
      $this->ajaxReturn(200, 'SUCCESS', $data);
    } else {
      $this->ajaxReturn(500, '分组不存在');
    }
  }


  /** 获取渠道活码分组列表页【渠道活码】
   * @Method groupIndex()
   *
   * @param integer $page_num 当前页
   * @param integer $page_size 每页显示条数
   * @param string $keyword 关键字检索[分组名称]
   *
   * @return Jsonp
   *
   * @throws null
   *
   * @link {domain}corpwechat/channel/groupIndex
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/11
   */
  public function groupIndex() {
    $map = array(); // 查询条件

    // 1.关键字
    $keyword = input('post.keyword/s', '', 'trim');
    if (isset($keyword) && !empty($keyword)) {
      // 关键字检索[分组名称]
      $map['name'] = ['like', '%' . $keyword . '%'];
    }

    // 2.排序
    $order = ['cg.id desc'];

    // 3.分页
    $page_num = input('post.page_num/d', 1, 'intval');
    $page_size = input('post.page_size/d', 10, 'intval');

    // 4.获取字段
    $field = 'cg.id as group_id, cg.name as group_name, cg.create_time, cg.update_time';
    $list = model('corpwechat.CorpwechatChannelGroup')
      ->getList($map, $order, $page_num, $page_size, $field);

    $this->ajaxReturn(200, 'SUCCESS', $list);
  }


  /** 删除渠道活码分组【渠道活码】
   * @Method delGroup()
   *
   * @param null
   *
   * @return Jsonp
   *
   * @throws \think\db\exception\DataNotFoundException
   * @throws \think\db\exception\ModelNotFoundException
   * @throws \think\exception\DbException
   *
   * @link {domain}corpwechat/channel/delGroup
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/11
   */
  public function delGroup() {
    // 接受请求参数
    $group_id = input('post.group_id/d', 0, 'intval');

    if ($group_id === 1) {
      $this->ajaxReturn(500, '默认分组不可编辑');
    }

    $isSet = model('corpwechat.CorpwechatChannelGroup')->find($group_id);
    if (!isset($isSet) || empty($isSet)) {
      $this->ajaxReturn(500, '分组不存在');
    }

    $del = model('corpwechat.CorpwechatChannelGroup')->destroy($group_id);

    if (isset($del) && !empty($del)) {
      $this->ajaxReturn(200, '删除成功');
    } else {
      $this->ajaxReturn(500, '删除失败');
    }
  }


  /** 新增/编辑渠道活码【渠道活码】
   * @Method editChannel()
   *
   * @param integer $channel_id 渠道活码ID[有：更新；无：新增]
   * @param string $channel_name 二维码名称
   * @param integer $group_id 所属分组
   * @param array $user_list 选择员工
   * @param integer $is_tag 是否打渠道标签[1:是;0:否]
   * @param array $channel_tag 渠道标签【is_tag=1时，必填】
   * @param integer $welcome_type 欢迎语类型[1:渠道欢迎语;2:默认欢迎语;3:不使用欢迎语;]
   * @param string $text_content 消息文本内容【welcome_type=1时，必填】
   * @param integer $type 附件类型[1:text;2:image;3:link;]【welcome_type=1时，必填】
   * @param string $pic_url 2:image.图片的链接【welcome_type=1&type=2时，必填】
   * @param integer $link_type 3:link.链接类型【welcome_type=1&type=3时，必填】
   * @param string $link_title 3:link.图文消息标题【welcome_type=1&type=3时，必填】
   * @param string $link_picurl 3:link.图文消息封面的url【welcome_type=1&type=3时，必填】
   * @param string $link_desc 3:link.图文消息的描述【welcome_type=1&type=3时，必填】
   * @param string $link_url 3:link.图文消息的链接【welcome_type=1&type=3时，必填】
   * @param integer $skip_verify 自动通过好友[1:true(默认);0:false]
   *
   * @param null
   *
   * @return Jsonp
   *
   * @throws \think\db\exception\DataNotFoundException
   * @throws \think\db\exception\ModelNotFoundException
   * @throws \think\exception\DbException
   *
   * @link {domain}corpwechat/channel/editChannel
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/14
   */
  public function editChannel() {
    // 接受请求参数
    $input_data = [
      'channel_id' => input('post.channel_id/d', 0, 'intval'),
      'channel_name' => input('post.channel_name/s', '', 'trim'),
      'group_id' => input('post.group_id/d', 0, 'intval'),
      'user_list' => input('post.user_list/a', []),
      'is_tag' => input('post.is_tag/d', 0, 'intval'),
      'channel_tag' => input('post.channel_tag/a', []),
      'welcome_type' => input('post.welcome_type/d', 0, 'intval'),
      'text_content' => input('post.text_content/s', '', 'trim'),
      'type' => input('post.type/d', 0, 'intval'),
      'pic_url' => input('post.pic_url/s', '', 'trim'),
      // 3:link.链接形式[1:内链;2:外链]
      'link_form' => input('post.link_form/d', 0, 'intval'),
      // 3.link.link_form=1:内链ID[link_form=1时生效]
      'inner_id' => input('post.inner_id/d', 0, 'intval'),
      // 3.link.link_form=1:内链名称[link_form=1时生效]
      'inner_name' => input('post.inner_name/s', '', 'trim'),
      // 3:link.图文消息标题
      'link_title' => input('post.link_title/s', '', 'trim'),
      // 3:link.链接类型
      'link_type' => input('post.link_type/s', '', 'trim'),
      // 3:link.图文消息封面的url
      'link_picurl' => input('post.link_picurl/s', '', 'trim'),
      // 3:link.图文消息的描述
      'link_desc' => input('post.link_desc/s', '', 'trim'),
      // 3:link.图文消息的链接
      'link_url' => input('post.link_url/s', '', 'trim'),
      'skip_verify' => input('post.skip_verify/b', 'true')
    ];

    // Validate验证规则
    $rule = [
      'channel_id' => 'require|integer',
      'channel_name' => 'require|length:1,30',
      'group_id' => 'require|integer|>=:1',
      'user_list' => 'require|array|min:1',
      'is_tag' => 'require|integer|in:0,1',
      'welcome_type' => 'require|integer|in:1,2,3',
      'skip_verify' => 'require|boolean'
    ];

    // 判断员工，并组装数据
    if (count($input_data['user_list']) < 1) {
      $this->ajaxReturn(500, '请选择员工');
    } else {
      $user = array_values($input_data['user_list']);
    }

    if ($input_data['is_tag'] === 1) {
      $rule['channel_tag'] = 'require|array|min:1';
      // 判断标签，并组装数据
      if (count($input_data['channel_tag']) < 1) {
        $this->ajaxReturn(500, '请选择渠道标签');
      }
    }

    /**
     * 1.判断欢迎语类型
     * [1:渠道欢迎语;2:默认欢迎语;3:不使用欢迎语;]
     */
    switch ($input_data['welcome_type']) {
      case CHANNEL_WORDS:
        /**
         * 1.1渠道欢迎语
         * 附件类型[1:text;2:image;3:link;]
         */
        $rule['type'] = 'require|integer|in:1,2,3';
        $rule['text_content'] = 'require|length:1,600';
        switch ($input_data['type']) {
          case TEXT_TYPE: // 纯文本
            break;

          case IMAGE_TYPE: // 图片
            $rule['pic_url'] = 'require|url';
            break;

          case LINK_TYPE: // 链接
            switch ($input_data['link_form']) {
              case 1:
                $input_data['link_url'] = model('corpwechat.CorpwechatWelcomeWords')
                  ->innerLinkInfo($input_data['link_type'], $input_data['inner_id']);
                if (false === $input_data['link_url']) {
                  $this->ajaxReturn(500, model('corpwechat.CorpwechatWelcomeWords')->getErrorMessage());
                }
                break;

              case 2:
                break;

              default:
                $this->ajaxReturn(500, '请正确选择欢迎语内链形式');
                break;
            }
            $rule['link_url'] = 'require|url';
            $rule['link_title'] = 'require|length:1,50';
            $rule['link_desc'] = 'length:0,200';
            $rule['link_picurl'] = 'url';
            break;

          default:
            $this->ajaxReturn(500, '请正确选择欢迎语2');
        }
        break;

      case DEFAULT_WORDS:
      case NO_WORDS:
        break;

      default:
        $this->ajaxReturn(500, '欢迎语类型错误');
        break;
    }

    // Validate报错信息
    $msg = [
      'channel_id' => '缺少channel_id',
      'channel_name' => '请输入1~30字内的二维码名称',
      'group_id' => '请选择所属分组',
      'user_list' => '请选择员工',
      'is_tag' => '请选择渠道标签',
      'channel_tag' => '请选择渠道标签',
      'welcome_type' => '请选择欢迎语类型',
      'text_content' => '请输入1~600字内的欢迎语',
      'type' => '请选择欢迎语2类型',
      'pic_url' => '请上传正确的欢迎语图片',
      'link_form.require' => '请选择正确的链接欢迎语形式',
      'inner_id' => '请先检索出正确的内链',
      'link_title' => '请输入1~200长度的图文消息标题',
      'link_type' => '请选择链接欢迎语的内链类型',
      'link_picurl' => '请上传正确的链接欢迎语封面',
      'link_desc' => '请输入1~200长度的链接欢迎语描述',
      'link_url' => '请填写正确的链接欢迎语链接',
      'skip_verify' => '请选择是否自动通过好友'
    ];

    // 实例化验证类
    $validate = new Validate($rule, $msg);
    if (!$validate->check($input_data)) {
      $this->ajaxReturn(500, $validate->getError());
    }

    /** 获取企微配置 */
    $apiConfig = config('global_config.corpwechat_api');
    if (empty($apiConfig) || !isset($apiConfig) || !is_array($apiConfig)) {
      $this->ajaxReturn(500, '请先完成企业微信配置');
    }
    $is_open = $apiConfig['is_open'] ? intval($apiConfig['is_open']) : -1;
    if (1 === $is_open) {
      $this->corpId = $apiConfig['corpid'] ? $apiConfig['corpid'] : '';
      $this->agentId = $apiConfig['agentid'] ? $apiConfig['agentid'] : '';
      $this->corpSecret = $apiConfig['corpsecret'] ? $apiConfig['corpsecret'] : '';
      $this->customerContactSecret = $apiConfig['customer_contact_secret'] ? $apiConfig['customer_contact_secret'] : '';
    } else {
      $this->ajaxReturn(500, '企微服务配置异常');
    }

    if (isset($input_data['channel_id']) && !empty($input_data['channel_id'])) {
      $channel_info = model('corpwechat.CorpwechatChannel')->find($input_data['channel_id']);
      if (null === $channel_info) {
        $this->ajaxReturn(500, '要修改的渠道活码不存在');
      }
      if (1 === $channel_info->is_del) {
        $this->ajaxReturn(500, '要修改的渠道活码已删除');
      }

      // 企业微信API请求参数
      $way_info = [
        'config_id' => $channel_info->config_id,
        'type' => 2,
        'scene' => 2,
        'remark' => $input_data['channel_name'],
        'skip_verify' => $input_data['skip_verify'],
        'state' => $channel_info->state,
        'user' => $user
      ];

      /** 调用企业微信API */
      $externalContact = new ExternalContact($this->corpId, $this->corpSecret);
      $data = $externalContact->updateContactWay($way_info);
      if (false === $data) {
        $this->ajaxReturn(500, '接口请求失败', $externalContact->getError());
      }

      Db::startTrans();
      try {
        $channel_result = model('corpwechat.CorpwechatChannel')
          ->allowField(true)
          ->isUpdate(true)
          ->save(
            $input_data,
            ['id' => $input_data['channel_id']]
          );
        if (false === $channel_result) {
          throw new \Exception(model('corpwechat.CorpwechatChannel')->getError());
        }

        // 日志
        $log_result = model('AdminLog')->record(
          '修改企微渠道活码【活码-ID:' . $input_data['channel_id'] . '-名称:' . $input_data['channel_name'] . '】',
          $this->admininfo
        );
        if (false === $log_result) {
          throw new \Exception(model('AdminLog')->getError());
        }

        // 提交事务
        Db::commit();
      } catch (\Exception $e) {
        // 回滚事务
        Db::rollBack();
        $this->ajaxReturn(500, $e->getMessage());
      }

      $this->ajaxReturn(200, '渠道活码修改成功');
    } else {
      // 生成STATAE
      $state = mb_substr(uuid(), 0, 30);
      // 企业微信API请求参数
      $way_info = [
        'type' => 2,
        'scene' => 2,
        'remark' => $input_data['channel_name'],
        'skip_verify' => $input_data['skip_verify'],
        'state' => $state,
        'user' => $user
      ];

      /** 调用企业微信API */
      $externalContact = new ExternalContact($this->corpId, $this->corpSecret);
      $data = $externalContact->addContactWay($way_info);
      if (false === $data) {
        $this->ajaxReturn(500, '接口请求失败', $externalContact->getError());
      }

      if (empty($data['config_id']) || empty($data['qr_code'])) {
        $this->ajaxReturn(500, '渠道活码创建失败');
      }

      $input_data['state'] = $state;
      $input_data['config_id'] = $data['config_id'];
      $input_data['qr_code'] = $data['qr_code'];

      Db::startTrans();
      try {
        $channel_result = model('corpwechat.CorpwechatChannel')
          ->allowField(true)
          ->isUpdate(false)
          ->save($input_data);
        if (false === $channel_result) {
          throw new \Exception(model('corpwechat.CorpwechatChannel')->getError());
        }

        $channel_id = model('corpwechat.CorpwechatChannel')->getLastInsID();
        // 日志
        $log_result = model('AdminLog')->record(
          '添加企微渠道活码【活码-ID:' . $channel_id . '-名称:' . $input_data['channel_name'] . '】',
          $this->admininfo
        );
        if (false === $log_result) {
          throw new \Exception(model('AdminLog')->getError());
        }

        // 提交事务
        Db::commit();
      } catch (\Exception $e) {
        // 回滚事务
        Db::rollBack();
        $this->ajaxReturn(500, $e->getMessage());
      }

      $this->ajaxReturn(200, '渠道活码创建成功');
    }
  }


  /** 获取渠道活码活码列表【渠道活码】
   * @Method channelIndxe()
   *
   * @param integer $page_num 当前页
   * @param integer $page_size 每页显示条数
   * @param string $keyword 关键字检索[二维码名称]
   * @param string $order 排序[create_time:创建时间|scan_num:扫码次数]
   * @param integer $is_del 状态[0:全部;1:未删除;2:已删除]
   * @param integer $group_id 分组ID[0:全部]
   *
   * @return Jsonp
   *
   * @throws null
   *
   * @link {domain}corpwechat/channel/channelIndex
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/14
   */
  public function channelIndex() {
    $map = array(); // 查询条件

    // 1.关键字
    $keyword = input('post.keyword/s', '', 'trim');
    if (isset($keyword) && !empty($keyword)) {
      // 关键字检索[分组名称]
      $map['channel_name'] = ['like', '%' . $keyword . '%'];
    }

    // 2.排序
    $order = input('post.order/s', 'create_time', 'trim');
    if (isset($order) && !empty($order)) {
      // 排序[创建时间|扫码次数]
      switch ($order) {
        case 'scan_num':
          $order = ['scan_num DESC'];
          break;

        case 'create_time':
          $order = ['create_time DESC'];
          break;

        default:
          $order = ['id DESC'];
          break;
      }
    } else {
      $order = ['id DESC'];
    }

    // 3.是否删除
    $is_del = input('post.is_del/d', 0, 'intval');
    switch ($is_del) {
      case 2:
        $map['is_del'] = ['=', 1];
        break;

      case 3:
        break;

      case 1:
      default:
        $map['is_del'] = ['=', 0];
        break;
    }

    // 4.分组
    $group_id = input('post.group_id/d', 0, 'intval');
    if (isset($group_id) && !empty($group_id)) {
      // 关键字检索[分组名称]
      $map['group_id'] = ['=', $group_id];
    }
    // 5.分页
    $page_num = input('post.page_num/d', 1, 'intval');
    $page_size = input('post.page_size/d', 10, 'intval');

    // 6.获取字段
    $field = 'id as channel_id, channel_name,qr_code, scan_num, channel_tag, is_del, create_time, update_time';
    $list = model('corpwechat.CorpwechatChannel')->getList($map, $order, $page_num, $page_size, $field);

    $this->ajaxReturn(200, 'SUCCESS', $list);
  }


  /** 获取渠道活码活码详情【渠道活码】
   * @Method channelDetails()
   *
   * @param integer $channel_id 渠道活码ID
   *
   * @return Jsonp
   *
   * @throws \think\db\exception\DataNotFoundException
   * @throws \think\db\exception\ModelNotFoundException
   * @throws \think\exception\DbException
   *
   * @link {domain}corpwechat/channel/channelDetails
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/14
   */
  public function channelDetails() {
    $channel_id = input('post.channel_id/d', 0, 'intval');
    if (!isset($channel_id) || empty($channel_id)) {
      $this->ajaxReturn(500, '请选择活码');
    }

    $field = 'id as channel_id,
        channel_name, 
        group_id, 
        user_list, 
        is_tag, 
        channel_tag, 
        welcome_type,
        text_content, 
        type, 
        pic_url, 
        link_form,
        link_type,
        inner_id,
        inner_name,
        link_title, 
        link_picurl, 
        link_desc, 
        link_url, 
        skip_verify, 
        qr_code,
        scan_num,
        is_del,
        create_time,
        update_time';

    $data = model('corpwechat.CorpwechatChannel')
      ->field($field)
      ->find($channel_id);

    if (isset($data) && !empty($data)) {
      $this->ajaxReturn(200, 'SUCCESS', $data);
    } else {
      $this->ajaxReturn(500, '渠道活码不存在');
    }
  }


  /** 删除渠道活码活码【渠道活码】
   * @Method delChannel()
   *
   * @param integer $channel_id 渠道活码ID
   *
   * @return Jsonp
   *
   * @throws \think\db\exception\DataNotFoundException
   * @throws \think\db\exception\ModelNotFoundException
   * @throws \think\exception\DbException
   *
   * @link {domain}corpwechat/channel/delChannel
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/14
   */
  public function delChannel() {
    $channel_id = input('post.channel_id/d', 0, 'intval');
    if (!isset($channel_id) || empty($channel_id)) {
      $this->ajaxReturn(500, '请选择要删除的渠道活码');
    }

    $channel_info = model('corpwechat.CorpwechatChannel')
      ->find($channel_id);
    if (null === $channel_info) {
      $this->ajaxReturn(500, '要删除的渠道活码不存在');
    }
    if (1 === $channel_info->is_del) {
      $this->ajaxReturn(500, '渠道活码已删除');
    }

    /** 获取企微配置 */
    $apiConfig = config('global_config.corpwechat_api');
    if (empty($apiConfig) || !isset($apiConfig) || !is_array($apiConfig)) {
      $this->ajaxReturn(500, '请先完成企业微信配置');
    }
    $is_open = $apiConfig['is_open'] ? intval($apiConfig['is_open']) : -1;
    if (1 === $is_open) {
      $this->corpId = $apiConfig['corpid'] ? $apiConfig['corpid'] : '';
      $this->agentId = $apiConfig['agentid'] ? $apiConfig['agentid'] : '';
      $this->corpSecret = $apiConfig['corpsecret'] ? $apiConfig['corpsecret'] : '';
      $this->customerContactSecret = $apiConfig['customer_contact_secret'] ? $apiConfig['customer_contact_secret'] : '';
    } else {
      $this->ajaxReturn(500, '企微服务配置异常');
    }

    $channel = [
      'config_id' => $channel_info->config_id
    ];
    /** 调用企业微信API */
    $externalContact = new ExternalContact($this->corpId, $this->corpSecret);
    $data = $externalContact->delContactWay($channel);
    if (false === $data) {
      $this->ajaxReturn(500, '接口请求失败', $externalContact->getError());
    }

    Db::startTrans();
    try {
      $channel_result = model('corpwechat.CorpwechatChannel')
        ->allowField(true)
        ->isUpdate(true)
        ->save(
          [
            'is_del' => 1
          ],
          [
            'id' => $channel_id
          ]
        );
      if (false === $channel_result) {
        throw new \Exception(model('corpwechat.CorpwechatChannel')->getError());
      }

      // 日志
      $log_result = model('AdminLog')->record(
        '删除企微渠道活码【活码-ID:' . $channel_id . '-名称:' . $channel_info->channel_name . '】',
        $this->admininfo
      );
      if (false === $log_result) {
        throw new \Exception(model('AdminLog')->getError());
      }

      // 提交事务
      Db::commit();
    } catch (\Exception $e) {
      // 回滚事务
      Db::rollBack();
      $this->ajaxReturn(500, $e->getMessage());
    }

    $this->ajaxReturn(200, '渠道活码删除成功');
  }


  /** 渠道活码数据统计【渠道活码】
   * @Method statistics()
   *
   * @param integer $channel_id 渠道活码ID
   *
   * @return Jsonp
   *
   * @throws \think\db\exception\DataNotFoundException
   * @throws \think\db\exception\ModelNotFoundException
   * @throws \think\exception\DbException
   *
   * @link {domain}corpwechat/channel/statistics
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/25
   */
  public function statistics() {
    $channel_id = input('post.channel_id/d', 0, 'intval');
    $channel_state = model('corpwechat.CorpwechatChannel')
      ->where('id', $channel_id)
      ->value('state');
    if (null === $channel_state) {
      $this->ajaxReturn(500, '要查看的渠道活码异常');
    }

    // 今日添加客户数（人）
    $add_today = model('corpwechat.corpwechatChannelLog')
      ->field('id')
      ->where('type', 1)
      ->where('state', $channel_state)
      ->whereTime('create_time', 'today')
      ->group('external_user_id')
      ->count('id');

    // 今日删除客户数（人）
    $del_today = model('corpwechat.corpwechatChannelLog')
      ->field('id')
      ->where('type', 'IN', [2, 3])
      ->where('state', $channel_state)
      ->whereTime('create_time', 'today')
      ->group('external_user_id')
      ->count('id');

    // 总添加客户数（人）
    $add_total = model('corpwechat.corpwechatChannelLog')
      ->field('id')
      ->where('type', 1)
      ->where('state', $channel_state)
      ->group('external_user_id')
      ->count('id');

    // 总删除客户数（人）
    $del_total = model('corpwechat.corpwechatChannelLog')
      ->field('id')
      ->where('type', 'IN', [2, 3])
      ->where('state', $channel_state)
      ->group('external_user_id')
      ->count('id');

    $return = array(
      'add_today' => $add_today,
      'del_today' => $del_today,
      'add_total' => $add_total,
      'del_total' => $del_total,
    );

    $this->ajaxReturn(200, 'SUCCESS', $return);
  }


  /** 渠道活码数据统计查看【渠道活码】
   * @Method information()
   *
   * @param integer $channel_id 渠道活码ID
   * @param integer $view_type 查看类型[date:按日期查看;staff:按员工查看;]
   *
   * @return Jsonp
   *
   * @throws null
   *
   * @link {domain}corpwechat/channel/information
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/25
   */
  public function information() {
    $channel_id = input('post.channel_id/d', 0, 'intval');
    $channel_info = model('corpwechat.CorpwechatChannel')
      ->field('user_list,state')
      ->find($channel_id);
    if (null === $channel_info) {
      $this->ajaxReturn(500, '要查看的渠道活码信息异常');
    }

    // 查看类型[date:按日期查看;staff:按员工查看;]
    $view_type = input('post.view_type/s', '', 'trim');
    switch ($view_type) {
      case 'staff';
        // staff.按员工查看
        $return = $this->_staffView($channel_info);
        break;

      case 'date';
        // date.按日期查看
        $return = $this->_dateView($channel_info);
        break;

      default:
        // 错误的用户选择条件
        $this->ajaxReturn(500, '统计数据查看参数错误');
        break;
    }

    $this->ajaxReturn(200, 'SUCCESS', $return);
  }


  /** 按员工查看渠道活码统计数据
   * 【统计通过客户userid去重统计】
   * @Method _staffView()
   *
   * @param $channelInfo
   *
   * @return array
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/25
   */
  public function _staffView($channelInfo) {
    $return = array();
    foreach ($channelInfo->user_list as $name => $userId) {
      // 今日添加客户数（人）
      $add_today = model('corpwechat.corpwechatChannelLog')
        ->field('id')
        ->where('type', 1)
        ->where('state', $channelInfo->state)
        ->where('userid', $userId)
        ->whereTime('create_time', 'today')
        ->group('external_user_id')
        ->count('id');

      // 今日删除客户数（人）
      $del_today = model('corpwechat.corpwechatChannelLog')
        ->field('id')
        ->where('type', 'IN', [2, 3])
        ->where('state', $channelInfo->state)
        ->where('userid', $userId)
        ->whereTime('create_time', 'today')
        ->group('external_user_id')
        ->count('id');

      // 总添加客户数（人）
      $add_total = model('corpwechat.corpwechatChannelLog')
        ->field('id')
        ->where('type', 1)
        ->where('state', $channelInfo->state)
        ->where('userid', $userId)
        ->group('external_user_id')
        ->count('id');

      // 总删除客户数（人）
      $del_total = model('corpwechat.corpwechatChannelLog')
        ->field('id')
        ->where('type', 'IN', [2, 3])
        ->where('state', $channelInfo->state)
        ->where('userid', $userId)
        ->group('external_user_id')
        ->count('id');

      $return[] = [
        'name' => $name,
        'userId' => $userId,
        'add_total' => $add_total,
        'add_today' => $add_today,
        'del_today' => $del_today,
        'del_total' => $del_total,
      ];
    }

    return $return;
  }


  /** 按时间查看渠道活码统计数据
   * 【统计未通过客户userid去重统计，取日记录日志】
   * @Method _dateView()
   *
   * @param $channelInfo
   *
   * @return array
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/25
   */
  public function _dateView($channelInfo) {
    $page_num = input('post.page_num/d', 1, 'intval');
    $page_size = input('post.page_size/d', 10, 'intval');

    $map['state'] = ['=', $channelInfo->state];

    $order = ['id desc'];

    // 查询字段
    $field = 'id,
        add_total,
        del_total,
        follow_del,
        external_del,
        create_time';

    try {
      $list = model('corpwechat.CorpwechatChannelDayLog')
        ->getList($map, $order, $page_num, $page_size, $field);
      if (false === $list) {
        throw new Exception(model('corpwechat.CorpwechatChannelDayLog')->getError());
      }
      return $list;
    } catch (Exception $e) {
      return array();
    }
  }
}
