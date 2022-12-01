<?php

/** 欢迎语管理 */

namespace app\apiadmin\controller\corpwechat;

use app\common\controller\Backend;
use app\common\model\corpwechat\CorpwechatWelcomeWords;
use app\common\model\Link;
use think\Db;
use think\Validate;

define('TEXT_TYPE', 1); // 纯文本-附件类型-text
define('IMAGE_TYPE', 2); // 图片-附件类型-image
define('LINK_TYPE', 3); // 链接-附件类型-link

class WelcomeWords extends Backend {
  /** 欢迎语首页 - 列表页
   * @Method index()
   *
   * @param integer $page_num 当前页
   * @param integer $page_size 每页显示条数
   * @param string $keyword 关键字检索[欢迎语名称|消息文本内容|图文消息的描述]
   * @param array $date_range 时间搜索 [开始时间，结束时间]
   *
   * @return Jsonp
   *
   * @throws null
   *
   * @link {domain}corpwechat/welcome_words/index
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/3
   */
  public function index() {
    $map = array(); // 查询条件

    // 1.关键字
    $keyword = input('post.keyword/s', '', 'trim');
    if (isset($keyword) && !empty($keyword)) {
      // 关键字检索[欢迎语名称|消息文本内容|图文消息的描述]
      $map['title|text_content|link_desc'] = ['like', '%' . $keyword . '%'];
    }

    // 2.时间查询
    $date_range = input('post.date_range/a', []);
    if (2 == count($date_range)) {
      $start_time = strtotime($date_range[0]);
      $end_time = strtotime($date_range[1]);
      $date_range = [$start_time, $end_time + 86400 - 1];
      $map['create_time'] = ['between time', $date_range];
    }

    $order = ['id desc'];

    $page_num = input('post.page_num/d', 1, 'intval');
    $page_size = input('post.page_size/d', 10, 'intval');

    $welcomeWordsModel = new CorpwechatWelcomeWords();
    #  获取字段
    $field = 'id, title, type, create_time';
    $list = $welcomeWordsModel->getList($map, $order, $page_num, $page_size, $field);

    $this->ajaxReturn(200, 'SUCCESS', $list);
  }


  /** 新增/编辑欢迎语【欢迎语】
   * @Method editTemplate()
   *
   * @param null
   *
   * @return Jsonp
   *
   * @throws \think\db\exception\DataNotFoundException
   * @throws \think\db\exception\ModelNotFoundException
   * @throws \think\exception\DbException
   *
   * @link XXXXXXXXXX
   *
   * @author  Mr.yx
   * @version 1.1
   * @since   2022/5/5 0005
   */
  public function editTemplate() {
    // 接收请求参数
    $input_data = [
      // 欢迎语ID
      'id' => input('post.id/d', 0, 'intval'),
      // 欢迎语名称
      'title' => input('post.title/s', '', 'trim'),
      // 使用成员
      'user_ids' => input('post.user_ids/a', []),
      // 消息文本内容-欢迎语
      'text_content' => input('post.text_content/s', '', 'trim'),
      // 附件类型[1:text;2:image;3:link;]
      'type' => input('post.type/d', 0, 'intval'),
      // 2:image.图片的链接
      'pic_url' => input('post.pic_url/s', '', 'trim'),
      // 3:link.链接形式[1:内链;2:外链]
      'link_form' => input('post.link_form/d', 0, 'intval'),
      // 3:link.链接类型
      'link_type' => input('post.link_type/s', '', 'trim'),
      // 3.link.link_form=1:内链ID[link_form=1时生效]
      'inner_id' => input('post.inner_id/d', 0, 'intval'),
      // 3.link.link_form=1:内链名称[link_form=1时生效]
      'inner_name' => input('post.inner_name/s', '', 'trim'),
      // 3:link.图文消息标题
      'link_title' => input('post.link_title/s', '', 'trim'),
      // 3:link.图文消息封面的url
      'link_picurl' => input('post.link_picurl/s', '', 'trim'),
      // 3:link.图文消息的描述
      'link_desc' => input('post.link_desc/s', '', 'trim'),
      // 3:link.图文消息的链接
      'link_url' => input('post.link_url/s', '', 'trim')
    ];

    // 验证规则
    $rule = [
      // 公共通用验证规则
      'id' => 'require|integer',
      'title' => 'require|length:1,50',
      'user_ids' => 'require|array|min:1',
      'text_content' => 'require|length:1,200',
      'type' => 'require|in:1,2,3'
    ];

    // 根据附件类型验证参数
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
        $this->ajaxReturn(500, '请正确选择欢迎语附件类型');
    }

    $msg = [
      'id.require' => '请选择要修改的欢迎语',
      'id.integer' => '请正确选择要修改的欢迎语',
      'title.require' => '请输入欢迎语名称',
      'title.length' => '欢迎语名称长度为1~50',
      'user_ids.require' => '请选择使用成员',
      'user_ids.array' => '请正确选择使用成员',
      'user_ids.min' => '请至少选择一个使用成员',
      'text_content.require' => '请输入欢迎语',
      'text_content.length' => '欢迎语长度为1~200',
      'type.require' => '请选择欢迎语附件类型',
      'type.in' => '请选择正确的欢迎语附件类型',
      'pic_url.require' => '请上传欢迎语图片',
      'pic_url.url' => '请上传正确的欢迎语图片',
      'link_form.require' => '请选择链接欢迎语的形式',
      'link_form.in' => '请正确选择链接欢迎语的形式',
      'inner_id.require' => '请先检索内链',
      'inner_id.integer' => '请先检索出正确的内链',
      'link_title.require' => '请输入链接欢迎语的标题',
      'link_title.length' => '链接欢迎语的标题长度为1~50',
      'link_type.require' => '请选择链接欢迎语的内链类型',
      'link_picurl.url' => '请上传正确的链接欢迎语的封面',
      'link_desc.length' => '链接欢迎语的描述长度为0~200',
      'link_url.require' => '请填写链接欢迎语的链接',
      'link_url.url' => '请填写正确的链接欢迎语的链接'
    ];

    $validate = new Validate($rule, $msg);
    if (!$validate->check($input_data)) {
      $this->ajaxReturn(500, $validate->getError());
    }

    if (isset($input_data['id']) && !empty($input_data['id'])) {
      // 存在欢迎语ID，更新欢迎语
      $template_info = model('corpwechat.CorpwechatWelcomeWords')
        ->find($input_data['id']);

      if (!isset($template_info) || empty($template_info)) {
        $this->ajaxReturn(500, '要修改的欢迎语不存在');
      } else {
        $welcome_id = $input_data['id'];
      }

      // 开启事务
      Db::startTrans();
      try {
        // 更新欢迎语
        $update_result = model('corpwechat.CorpwechatWelcomeWords')
          ->allowField(true)
          ->isUpdate(true)
          ->save($input_data, ['id' => $welcome_id]);
        if (false === $update_result) {
          throw new \Exception(model('corpwechat.CorpwechatWelcomeWords')->getError());
        }

        // 更新欢迎语使用成员
        $staff_del_result = model('corpwechat.CorpwechatStaff')
          ->where('welcome_id', $welcome_id)
          ->update(['welcome_id' => 0]);
        $staff_add_result = model('corpwechat.CorpwechatStaff')
          ->where('userid', 'in', $input_data['user_ids'])
          ->update(['welcome_id' => $welcome_id]);
        if (false === $staff_del_result || false === $staff_add_result) {
          throw new \Exception(model('corpwechat.CorpwechatStaff')->getError());
        }
        if (0 === $staff_del_result && 0 === $staff_add_result) {
          throw new \Exception('欢迎语使用成员更新失败');
        }

        // 日志
        $log_result = model('AdminLog')->record(
          '修改企微欢迎语【欢迎语-ID:' . $welcome_id . '-名称:' . $input_data['title'] . '】',
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

      $this->ajaxReturn(200, '欢迎语修改成功');
    } else {
      // 开启事务
      Db::startTrans();
      try {
        // 写入新欢迎语
        $insert_result = model('corpwechat.CorpwechatWelcomeWords')
          ->allowField(true)
          ->save($input_data);
        if (false === $insert_result) {
          throw new \Exception(model('corpwechat.CorpwechatWelcomeWords')->getError());
        }
        $welcome_id = model('corpwechat.CorpwechatWelcomeWords')->getLastInsID();

        // 更新欢迎语使用成员
        $update_result = model('corpwechat.CorpwechatStaff')
          ->where('userid', 'in', $input_data['user_ids'])
          ->update(['welcome_id' => $welcome_id]);
        if (false === $update_result) {
          throw new \Exception(model('corpwechat.CorpwechatStaff')->getError());
        }
        if (0 === $update_result) {
          throw new \Exception('欢迎语使用成员更新失败');
        }

        // 日志
        $log_result = model('AdminLog')->record(
          '添加企微欢迎语【欢迎语-ID:' . $welcome_id . '-名称:' . $input_data['title'] . '】',
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

      $this->ajaxReturn(200, '欢迎语新增成功');
    }
  }


  /** 新增欢迎语
   * @Method addTemplate()
   *
   * @param string $title 欢迎语名称
   * @param array $user_ids 使用成员
   * @param string $text_content 消息文本内容-欢迎语
   * @param integer $type 附件类型[1:text;2:image;3:link;]
   * @param string $pic_url 2:image.图片的链接
   * @param string $link_title 3:link.图文消息标题
   * @param string $link_picurl 3:link.图文消息封面的url
   * @param string $link_desc 3:link.图文消息的描述
   * @param string $link_url 3:link.图文消息的链接
   *
   * @return Jsonp
   *
   * @throws null
   *
   * @link {domain}corpwechat/welcome_words/addTemplate
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/3
   */
  public function addTemplate() {
    // 接收请求参数
    $input_data = [
      // 欢迎语名称
      'title' => input('post.title/s', '', 'trim'),
      // 使用成员
      'user_ids' => input('post.user_ids/a', []),
      // 消息文本内容-欢迎语
      'text_content' => input('post.text_content/s', '', 'trim'),
      // 附件类型[1:text;2:image;3:link;]
      'type' => input('post.type/d', 0, 'intval'),
      // 2:image.图片的链接
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
      'link_url' => input('post.link_url/s', '', 'trim')
    ];

    // 验证规则
    $rule = [
      'title' => 'require|length:1,50',
      'user_ids' => 'require|array|min:1',
      'text_content' => 'require|length:1,200',
      'type' => 'require|in:1,2,3'
    ];
    // 根据附件类型验证参数
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
            $rule['inner_id'] = 'require|integer';
            break;

          default:
            break;
        }
        $rule['link_form'] = 'require|in:0,1,2';
        $rule['link_title'] = 'require|length:1,50';
        $rule['link_type'] = 'require|alpha|length:1,45';
        $rule['link_picurl'] = 'url';
        $rule['link_desc'] = 'length:0,200';
        $rule['link_url'] = 'require|url';
        break;

      default:
        $this->ajaxReturn(500, '请正确选择欢迎语附件类型');
    }

    $msg = [
      'title.require' => '请输入欢迎语名称',
      'title.length' => '欢迎语名称长度为1~50',
      'user_ids.require' => '请选择使用成员',
      'user_ids.array' => '请正确选择使用成员',
      'user_ids.min' => '请至少选择一个使用成员',
      'text_content.require' => '请输入欢迎语',
      'text_content.length' => '欢迎语长度为1~200',
      'type.require' => '请选择附件类型',
      'type.in' => '请选择正确的附件类型',
      'pic_url.require' => '请上传欢迎语图片',
      'pic_url.url' => '请上传正确的欢迎语图片',
      'link_form.require' => '请选择链接形式',
      'link_form.in' => '请正确选择的链接形式',
      'inner_id.require' => '请选择内链',
      'inner_id.integer' => '请选择内链',
      'link_title.require' => '请输入图文消息标题',
      'link_title.length' => '图文消息标题长度为1~50',
      'link_type.require' => '请选择内链类型',
      'link_type.alpha' => '内链类型错误',
      'link_type.length' => '内链类型错误',
      'link_picurl.url' => '请上传正确的图文消息封面',
      'link_desc.length' => '图文消息的描述长度为0~200',
      'link_url.require' => '请填写图文消息的链接',
      'link_url.url' => '请填写正确的图文消息的链接'
    ];

    $validate = new Validate($rule, $msg);
    if (!$validate->check($input_data)) {
      $this->ajaxReturn(500, $validate->getError());
    }

    // 开启事务
    Db::startTrans();
    try {
      // 写入新欢迎语
      $insert_result = model('corpwechat.CorpwechatWelcomeWords')
        ->allowField(true)
        ->save($input_data);
      if (false === $insert_result) {
        throw new \Exception(model('corpwechat.CorpwechatWelcomeWords')->getError());
      }
      $welcome_id = model('corpwechat.CorpwechatWelcomeWords')->getLastInsID();

      // 更新欢迎语使用成员
      $update_result = model('corpwechat.CorpwechatStaff')
        ->where('userid', 'in', $input_data['user_ids'])
        ->update(['welcome_id' => $welcome_id]);
      if (false === $update_result) {
        throw new \Exception(model('corpwechat.CorpwechatStaff')->getError());
      }
      if (0 === $update_result) {
        throw new \Exception('欢迎语使用成员更新失败');
      }

      // 日志
      $log_result = model('AdminLog')->record(
        '添加企微欢迎语【欢迎语-ID:' . $welcome_id . '-名称:' . $input_data['title'] . '】',
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

    $this->ajaxReturn(200, '欢迎语新增成功');
  }


  /** 修改欢迎语
   * @Method editTemplate()
   *
   * @param integer $id 欢迎语ID
   * @param string $title 欢迎语名称
   * @param array $user_ids 使用成员
   * @param string $text_content 消息文本内容-欢迎语
   * @param integer $type 附件类型[1:text;2:image;3:link;]
   * @param string $pic_url 2:image.图片的链接
   * @param string $link_title 3:link.图文消息标题
   * @param string $link_picurl 3:link.图文消息封面的url
   * @param string $link_desc 3:link.图文消息的描述
   * @param string $link_url 3:link.图文消息的链接
   *
   * @return Jsonp
   *
   * @throws null
   *
   * @link {domain}corpwechat/welcome_words/editTemplate
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/3
   */
  public function editTemplate1() {
    // 接收请求参数
    $input_data = [
      // 欢迎语ID
      'id' => input('post.id/d'),
      // 欢迎语名称
      'title' => input('post.title/s', '', 'trim'),
      // 使用成员
      'user_ids' => input('post.user_ids/a', []),
      // 消息文本内容-欢迎语
      'text_content' => input('post.text_content/s', '', 'trim'),
      // 附件类型[1:text;2:image;3:link;]
      'type' => input('post.type/d'),
      // 2:image.图片的链接
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
      'link_url' => input('post.link_url/s', '', 'trim')
    ];

    // 验证规则
    $rule = [
      'id' => 'require|integer',
      'title' => 'require|length:1,50',
      'user_ids' => 'require|array|min:1',
      'text_content' => 'require|length:1,200',
      'type' => 'require|in:1,2,3'
    ];
    // 根据附件类型验证参数
    switch ($input_data['type']) {
      case TEXT_TYPE: // 纯文本
        break;

      case IMAGE_TYPE: // 图片
        $rule['pic_url'] = 'require|url';
        break;

      case LINK_TYPE: // 链接
        $rule['link_title'] = 'require|length:1,50';
        $rule['link_type'] = 'chs|length:1,45';
        $rule['link_picurl'] = 'url';
        $rule['link_desc'] = 'length:0,200';
        $rule['link_url'] = 'require|url';
        break;

      default:
        $this->ajaxReturn(500, '请正确选择欢迎语附件类型');
    }

    $msg = [
      'id.require' => '请选择要修改的欢迎语',
      'id.integer' => '请选择要修改的欢迎语',
      'title.require' => '请输入欢迎语名称',
      'title.length' => '欢迎语名称长度为1~50',
      'user_ids.require' => '请选择使用成员',
      'user_ids.array' => '请正确选择使用成员',
      'user_ids.min' => '请至少选择一个使用成员',
      'text_content.require' => '请输入欢迎语',
      'text_content.length' => '欢迎语长度为1~200',
      'type.require' => '请选择附件类型',
      'type.in' => '请选择正确的附件类型',
      'pic_url.require' => '请上传欢迎语图片',
      'pic_url.url' => '请上传正确的欢迎语图片',
      'link_title.require' => '请输入图文消息标题',
      'link_title.length' => '图文消息标题长度为1~50',
      'link_type.chs' => '链接类型只能为汉字',
      'link_type.length' => '图文消息标题长度为1~45',
      'link_picurl.url' => '请上传正确的图文消息封面',
      'link_desc.length' => '图文消息的描述长度为0~200',
      'link_url.require' => '请填写图文消息的链接',
      'link_url.url' => '请填写正确的图文消息的链接'
    ];

    $validate = new Validate($rule, $msg);
    if (!$validate->check($input_data)) {
      $this->ajaxReturn(500, $validate->getError());
    }

    $template_info = model('corpwechat.CorpwechatWelcomeWords')
      ->find($input_data['id']);

    if (!isset($template_info) || empty($template_info)) {
      $this->ajaxReturn(500, '要修改的欢迎语不存在');
    } else {
      $welcome_id = $input_data['id'];
    }

    // 开启事务
    Db::startTrans();
    try {
      // 更新欢迎语
      $update_result = model('corpwechat.CorpwechatWelcomeWords')
        ->allowField(true)
        ->isUpdate(true)
        ->save($input_data, ['id' => $welcome_id]);
      if (false === $update_result) {
        throw new \Exception(model('corpwechat.CorpwechatWelcomeWords')->getError());
      }

      // 更新欢迎语使用成员
      $staff_del_result = model('corpwechat.CorpwechatStaff')
        ->where('welcome_id', $welcome_id)
        ->update(['welcome_id' => 0]);
      $staff_add_result = model('corpwechat.CorpwechatStaff')
        ->where('userid', 'in', $input_data['user_ids'])
        ->update(['welcome_id' => $welcome_id]);
      if (false === $staff_del_result || false === $staff_add_result) {
        throw new \Exception(model('corpwechat.CorpwechatStaff')->getError());
      }
      if (0 === $staff_del_result || 0 === $staff_add_result) {
        throw new \Exception('欢迎语使用成员更新失败');
      }

      // 日志
      $log_result = model('AdminLog')->record(
        '修改企微欢迎语【欢迎语-ID:' . $welcome_id . '-名称:' . $input_data['title'] . '】',
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

    $this->ajaxReturn(200, '欢迎语修改成功');
  }


  /** 删除欢迎语
   * @Method delTemplate()
   *
   * @param integer $id 欢迎语ID
   *
   * @return Jsonp
   *
   * @throws null
   *
   * @link {domain}corpwechat/welcome_words/delTemplate
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/3
   */
  public function delTemplate() {
    $template_id = input('post.template_id/d');

    $template_info = model('corpwechat.CorpwechatWelcomeWords')
      ->find($template_id);

    if (!isset($template_info) || empty($template_info)) {
      $this->ajaxReturn(500, '要删除的欢迎语不存在');
    }

    // 开启事务
    Db::startTrans();
    try {
      // 写入新欢迎语
      $del_result = model('corpwechat.CorpwechatWelcomeWords')
        ->where('id', $template_id)
        ->delete();
      if (false === $del_result) {
        throw new \Exception(model('corpwechat.CorpwechatWelcomeWords')->getError());
      }

      // 更新欢迎语使用成员
      $staff_result = model('corpwechat.CorpwechatStaff')
        ->where('welcome_id', '=', $template_id)
        ->update(['welcome_id' => 0]);
      if (false === $staff_result) {
        throw new \Exception(model('corpwechat.CorpwechatStaff')->getError());
      }

      // 日志
      $log_result = model('AdminLog')->record(
        '删除企微欢迎语【欢迎语-ID:' . $template_id . '-名称:' . $template_info['title'] . '】',
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

    $this->ajaxReturn(200, '欢迎语删除成功');
  }


  /** 欢迎语详情
   * @Method details()
   *
   * @param integer $template_id 欢迎语ID
   *
   * @return Jsonp
   *
   * @throws \think\Exception
   * @throws \think\db\exception\DataNotFoundException
   * @throws \think\db\exception\ModelNotFoundException
   * @throws \think\exception\DbException
   *
   * @link {domain}corpwechat/welcome_words/details
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/10
   */
  public function details() {
    $template_id = input('post.template_id/d', 0, 'intval');

    if (!isset($template_id) || empty($template_id)) {
      $this->ajaxReturn(500, '请选择要查看的欢迎语');
    }

    $template_info = model('corpwechat.CorpwechatWelcomeWords')
      ->getDetails($template_id);

    if (!isset($template_info) || empty($template_info)) {
      $this->ajaxReturn(500, '要查看的欢迎语不存在');
    } else {
      $this->ajaxReturn(200, 'SUCCESS', $template_info);
    }
  }


  /** 获取员工列表
   * @Method staffList()
   *
   * @param null
   *
   * @return Jsonp
   *
   * @throws null
   *
   * @link {domain}corpwechat/welcome_words/staffList
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/3
   */
  public function staffList() {
    $list = model('corpwechat.CorpwechatStaff')
      ->getCache();

    $this->ajaxReturn(200, 'SUCCSS', $list);
  }
}
