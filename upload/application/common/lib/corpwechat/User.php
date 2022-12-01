<?php

namespace app\common\lib\corpwechat;

use app\common\lib\corpwechat\promise\Corp;

class User extends Corp {
  /**
   * 创建成员请求地址
   * @var string
   */
  const USER_CREATE = 'https://qyapi.weixin.qq.com/cgi-bin/user/create';

  /**
   * 读取成员请求地址
   * @var string
   */
  const USER_GET = 'https://qyapi.weixin.qq.com/cgi-bin/user/get';

  /**
   * 更新成员請求地址
   * @var string
   */
  const USER_UPDATE = 'https://qyapi.weixin.qq.com/cgi-bin/user/update';

  const USER_DELETE = 'https://qyapi.weixin.qq.com/cgi-bin/user/delete';
  const USER_BATCH_DELETE = 'https://qyapi.weixin.qq.com/cgi-bin/user/batchdelete';
  const USER_SIMPLE_LIST = 'https://qyapi.weixin.qq.com/cgi-bin/user/simplelist';
  const USER_LIST = 'https://qyapi.weixin.qq.com/cgi-bin/user/list';
  const USERID_TO_OPENID = 'https://qyapi.weixin.qq.com/cgi-bin/user/convert_to_openid';
  const OPENID_TO_USERID = 'https://qyapi.weixin.qq.com/cgi-bin/user/convert_to_userid';
  const USER_AUTH_SUCCESS = 'https://qyapi.weixin.qq.com/cgi-bin/user/authsucc';

  /**
   * @Method 创建成员
   *
   * @param $user array 所创建用户的用户信息
   * @return false|mixed
   *
   * @link https://qyapi.weixin.qq.com/cgi-bin/user/create?access_token=ACCESS_TOKEN
   */
  public function UserCreate($user) {
    return $this->callPost(self::USER_CREATE, $user);
  }

  /**
   * @Method 读取成员
   *
   * @param $userid string 成员UserID
   * @return false|mixed
   *
   * @link https://qyapi.weixin.qq.com/cgi-bin/user/get?access_token=ACCESS_TOKEN&userid=USERID
   */
  public function UserGet($userid) {
    return $this->callGet(self::USER_GET, array('userid' => $userid));
  }

  /**
   * @Method 更新成员
   *
   * @param $user
   * @return false|mixed
   *
   * @link https://qyapi.weixin.qq.com/cgi-bin/user/update?access_token=ACCESS_TOKEN
   */
  public function UserUpdate($user) {
    return $this->callPost(self::USER_UPDATE, $user);
  }

  /**
   * @Method 删除成员
   *
   * @param $userid
   * @return false|mixed
   *
   * @link https://qyapi.weixin.qq.com/cgi-bin/user/delete?access_token=ACCESS_TOKEN&userid=USERID
   */
  public function UserDelete($userid) {
    return $this->callGet(self::USER_DELETE, array('userid' => $userid));
  }

  public function UserBatchDelete(array $userIdList) {
    return $this->callPost(self::USER_BATCH_DELETE, ['useridlist' => $userIdList]);
  }

  public function UserSimpleList($departmentId, $fetchChild = 0) {
    return $this->callGet(self::USER_SIMPLE_LIST, array('department_id' => $departmentId, 'fetch_child' => $fetchChild));
  }

  public function UserList($departmentId, $fetchChild = 0) {
    return $this->callGet(self::USER_LIST, array('department_id' => $departmentId, 'fetch_child' => $fetchChild));
  }

  public function UserId2OpenId($userid, &$openId, $agentid = null, &$appId = null) {
    return $this->callPost(self::USERID_TO_OPENID, array("userid" => $userid, "agentid" => $agentid));
  }

  public function openId2UserId($openId, &$userid) {
    return $this->callPost(self::OPENID_TO_USERID, array("openid" => $openId));
  }

  public function UserAuthSuccess($userid) {
    return $this->callGet(self::USER_AUTH_SUCCESS, array('userid' => $userid));
  }
}
