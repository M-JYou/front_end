<?php

namespace app\common\lib\corpwechat;

use app\common\lib\corpwechat\promise\Corp;

class ExternalContact extends Corp {
  /**
   * @Purpose
   * 获取客户列表
   * @var string
   */
  const EXTERNAL_CONTACT_LIST = 'https://qyapi.weixin.qq.com/cgi-bin/externalcontact/list';

  /**
   * @Purpose
   * 获取客户详情
   * @var string
   */
  const EXTERNAL_CONTACT_GET = 'https://qyapi.weixin.qq.com/cgi-bin/externalcontact/get';

  /**
   * @Purpose
   * 获取企业标签库
   * @var string
   */
  const GET_CORP_TAG_LIST = 'https://qyapi.weixin.qq.com/cgi-bin/externalcontact/get_corp_tag_list';

  /**
   * @Purpose
   * 添加企业客户标签
   * @var string
   */
  const ADD_CORP_TAG = 'https://qyapi.weixin.qq.com/cgi-bin/externalcontact/add_corp_tag';

  /**
   * @Purpose
   * 添加企业客户标签
   * @var string
   */
  const DEL_CORP_TAG = 'https://qyapi.weixin.qq.com/cgi-bin/externalcontact/del_corp_tag';

  /**
   * @Purpose
   * 编辑企业客户标签
   * @var string
   */
  const EDIT_CORP_TAG = 'https://qyapi.weixin.qq.com/cgi-bin/externalcontact/edit_corp_tag';

  /**
   * @Purpose
   * 编辑客户企业标签
   * @var string
   */
  const MARK_TAG = 'https://qyapi.weixin.qq.com/cgi-bin/externalcontact/mark_tag';

  /**
   * @Purpose
   * 发送新客户欢迎语
   * @var string
   */
  const SEND_WELCOME_MSG = 'https://qyapi.weixin.qq.com/cgi-bin/externalcontact/send_welcome_msg';

  /**
   * @Purpose
   * 创建企业群发
   * @var string
   */
  const ADD_MSG_TEMPLATE = 'https://qyapi.weixin.qq.com/cgi-bin/externalcontact/add_msg_template';

  /**
   * @Purpose
   * 获取「联系客户统计」数据
   * @var string
   */
  const GET_USER_BEHAVIOR_DATA = 'https://qyapi.weixin.qq.com/cgi-bin/externalcontact/get_user_behavior_data';

  /**
   * @Purpose
   * 获取客户群列表
   * @var string
   */
  const GROUP_CHAT_LIST = 'https://qyapi.weixin.qq.com/cgi-bin/externalcontact/groupchat/list';

  /**
   * @Purpose:
   * 获取客户群详情
   * @var string
   */
  const GROUP_CHAT_GET = 'https://qyapi.weixin.qq.com/cgi-bin/externalcontact/groupchat/get';

  const ADD_MOMENT_TASK = 'https://qyapi.weixin.qq.com/cgi-bin/externalcontact/add_moment_task';

  /**
   * @Purpose:
   * 配置客户联系「联系我」方式
   * @var string
   */
  const ADD_CONTACT_WAY = 'https://qyapi.weixin.qq.com/cgi-bin/externalcontact/add_contact_way';

  /**
   * @Purpose:
   * 更新企业已配置的「联系我」方式
   * @var string
   */
  const UPDATE_CONTACT_WAY = 'https://qyapi.weixin.qq.com/cgi-bin/externalcontact/update_contact_way';

  /**
   * @Purpose:
   * 删除企业已配置的「联系我」方式
   * @var string
   */
  const DEL_CONTACT_WAY = 'https://qyapi.weixin.qq.com/cgi-bin/externalcontact/del_contact_way';

  /**
   * @Purpose:
   * 获取群发成员发送任务列表
   * @var string
   */
  const GET_GROUPMSG_TASK = 'https://qyapi.weixin.qq.com/cgi-bin/externalcontact/get_groupmsg_task';

  /**
   * @Purpose:
   * 获取企业群发成员执行结果
   * @var string
   */
  const GET_GROUPMSG_TASK_SEND_RESULT = 'https://qyapi.weixin.qq.com/cgi-bin/externalcontact/get_groupmsg_send_result';


  public function addMomentTask($task) {
    return $this->callPost(self::ADD_MOMENT_TASK, $task);
  }

  public function sendWelcomeMsg($msg) {
    return $this->callPost(self::SEND_WELCOME_MSG, $msg);
  }

  /**
   * @Purpose:
   * 创建企业群发
   * @Method addMsgTemplate()
   *
   * @param array $template
   *
   * @return array|false|mixed
   *
   * @throws null
   *
   * @link https://qyapi.weixin.qq.com/cgi-bin/externalcontact/add_msg_template?access_token=ACCESS_TOKEN
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/4
   */
  public function addMsgTemplate($template) {
    return $this->callPostMore(self::ADD_MSG_TEMPLATE, $template);
  }

  /**
   * @Purpose:
   * 获取「联系客户统计」数据
   * @Method getUserBehaviorData()
   *
   * @param $behavior
   *
   * @return false|mixed
   *
   * @throws null
   *
   * @link https://qyapi.weixin.qq.com/cgi-bin/externalcontact/get_user_behavior_data?access_token=ACCESS_TOKEN
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/2/23
   */
  public function getUserBehaviorData($behavior) {
    return $this->callPost(self::GET_USER_BEHAVIOR_DATA, $behavior);
  }


  /**
   * @Purpose:
   * 获取客户列表
   * @Method externalContactList()
   *
   * @param integer $userId 企业成员的userid
   *
   * @return false|mixed
   *
   * @throws null
   *
   * @link https://qyapi.weixin.qq.com/cgi-bin/externalcontact/list?access_token=ACCESS_TOKEN&userid=USERID
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/2/23
   */
  public function externalContactList($userId) {
    return $this->callGet(self::EXTERNAL_CONTACT_LIST, array('userid' => $userId));
  }


  /**
   * @Purpose:
   * 获取客户详情
   * @Method externalContactGet()
   *
   * @param string $external_userid 外部联系人的userid
   * @param string $cursor 上次请求返回的next_cursor
   *
   * @return false|mixed
   *
   * @throws null
   *
   * @link https://qyapi.weixin.qq.com/cgi-bin/externalcontact/get?access_token=ACCESS_TOKEN&external_userid=EXTERNAL_USERID&cursor=CURSOR
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/2/24
   */
  public function externalContactGet($external_userid, $cursor = '') {
    return $this->callGet(self::EXTERNAL_CONTACT_GET, array('external_userid' => $external_userid, 'cursor' => $cursor));
  }


  /**
   * @Purpose:
   * 获取企业标签库
   * @Method getCorpTagList()
   *
   * @param string $query tag_id:要查询的标签id|group_id:要查询的标签组id
   *
   * @return false|mixed
   *
   * @throws null
   *
   * @link https://qyapi.weixin.qq.com/cgi-bin/externalcontact/get_corp_tag_list?access_token=ACCESS_TOKEN
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/2/24
   */
  public function getCorpTagList($query = []) {
    return $this->callPost(self::GET_CORP_TAG_LIST, $query);
  }


  /**
   * @Purpose:
   * 编辑客户企业标签
   * @Method markTag()
   *
   * @param array $tag_info 编辑客户企业标签信息
   *
   * @return false|mixed
   *
   * @throws null
   *
   * @link https://qyapi.weixin.qq.com/cgi-bin/externalcontact/mark_tag
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/2/24
   */
  public function markTag($tag_info) {
    return $this->callPost(self::MARK_TAG, $tag_info);
  }


  /**
   * @Purpose:
   * 获取客户群列表
   * @Method groupChatList()
   *
   * @param array $query
   *
   * @return false|mixed
   *
   * @throws null
   *
   * @link https://qyapi.weixin.qq.com/cgi-bin/externalcontact/groupchat/list?access_token=ACCESS_TOKEN
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/2/24
   */
  public function groupChatList($query) {
    return $this->callPostMore(self::GROUP_CHAT_LIST, $query);
  }


  /**
   * @Purpose:
   * 获取客户群详情
   * @Method groupChatGet()
   *
   * @param array $query
   *
   * @return false|mixed
   *
   * @throws null
   *
   * @link https://qyapi.weixin.qq.com/cgi-bin/externalcontact/groupchat/get?access_token=ACCESS_TOKEN
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/2/24
   */
  public function groupChatGet($query) {
    return $this->callPost(self::GROUP_CHAT_GET, $query);
  }


  /**
   * @Purpose:
   * 添加企业客户标签
   * @Method add_corp_tag()
   *
   * @param array $tag_info
   *
   * @return array|false|mixed
   *
   * @throws null
   *
   * @link https://qyapi.weixin.qq.com/cgi-bin/externalcontact/add_corp_tag?access_token=ACCESS_TOKEN
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/1
   */
  public function addCorpTag($tag_info) {
    return $this->callPost(self::ADD_CORP_TAG, $tag_info);
  }

  /**
   * @Purpose:
   * 删除企业客户标签
   * @Method del_corp_tag()
   *
   * @param array $tagIds 标签/标签组的id列表
   *
   * @return array|false|mixed
   *
   * @throws null
   *
   * @link https://qyapi.weixin.qq.com/cgi-bin/externalcontact/del_corp_tag?access_token=ACCESS_TOKEN
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/1
   */
  public function delCorpTag($tagIds) {
    return $this->callPost(self::DEL_CORP_TAG, $tagIds);
  }


  /**
   * @Purpose:
   * 编辑企业客户标签
   * @Method edit_corp_tag()
   *
   * @param array $tag_info 标签或标签组修改信息
   *
   * @return array|false|mixed
   *
   * @throws null
   *
   * @link https://qyapi.weixin.qq.com/cgi-bin/externalcontact/edit_corp_tag?access_token=ACCESS_TOKEN
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/1
   */
  public function editCorpTag($tag_info) {
    return $this->callPost(self::EDIT_CORP_TAG, $tag_info);
  }


  /**
   * @Purpose:
   * 配置客户联系「联系我」方式【新增渠道活码】
   * @Method addContactWay()
   *
   * @param array $wayInfo
   *
   * @return false|mixed
   *
   * @throws null
   *
   * @link https://qyapi.weixin.qq.com/cgi-bin/externalcontact/add_contact_way?access_token=ACCESS_TOKEN
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/11
   */
  public function addContactWay($wayInfo = []) {
    return $this->callPost(self::ADD_CONTACT_WAY, $wayInfo);
  }


  /**
   * @Purpose:
   * 更新企业已配置的「联系我」方式
   * @Method updateContactWay()
   *
   * @param array $wayInfo
   *
   * @return false|mixed
   *
   * @throws null
   *
   * @link https://qyapi.weixin.qq.com/cgi-bin/externalcontact/update_contact_way?access_token=ACCESS_TOKEN
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/14
   */
  public function updateContactWay($wayInfo = []) {
    return $this->callPost(self::UPDATE_CONTACT_WAY, $wayInfo);
  }


  /**
   * @Purpose:
   * 删除企业已配置的「联系我」方式
   * @Method delContactWay()
   *
   * @param $channel
   *
   * @return false|mixed
   *
   * @throws null
   *
   * @link https://qyapi.weixin.qq.com/cgi-bin/externalcontact/del_contact_way?access_token=ACCESS_TOKEN
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/14
   */
  public function delContactWay($channel) {
    return $this->callPost(self::DEL_CONTACT_WAY, $channel);
  }


  /**
   * @Purpose:
   * 获取群发成员发送任务列表
   * @Method getGroupmsgTask()
   *
   * @param array $query
   *
   * @return false|mixed
   *
   * @throws null
   *
   * @link https://qyapi.weixin.qq.com/cgi-bin/externalcontact/get_groupmsg_task?access_token=ACCESS_TOKEN
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/30
   */
  public function getGroupmsgTask($query) {
    return $this->callPost(self::GET_GROUPMSG_TASK, $query);
  }


  /**
   * @Purpose:
   * 获取企业群发成员执行结果
   * @Method getGroupmsgSendResult()
   *
   * @param $query
   *
   * @return false|mixed
   *
   * @throws null
   *
   * @link https://qyapi.weixin.qq.com/cgi-bin/externalcontact/get_groupmsg_send_result?access_token=ACCESS_TOKEN
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/30
   */
  public function getGroupmsgSendResult($query) {
    return $this->callPost(self::GET_GROUPMSG_TASK_SEND_RESULT, $query);
  }
}
