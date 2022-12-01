<?php

namespace app\common\lib\corpwechat;

use app\common\lib\corpwechat\promise\Corp;

class Tag extends Corp {
  /**
   * @Purpose
   * 创建标签
   * @var string
   */
  const TAG_CREATE = 'https://qyapi.weixin.qq.com/cgi-bin/tag/create';
  const TAG_UPDATE = 'https://qyapi.weixin.qq.com/cgi-bin/tag/update';
  const TAG_DELETE = 'https://qyapi.weixin.qq.com/cgi-bin/tag/delete';
  const TAG_GET_USER = 'https://qyapi.weixin.qq.com/cgi-bin/tag/get';
  const TAG_ADD_USER = 'https://qyapi.weixin.qq.com/cgi-bin/tag/addtagusers';
  const TAG_DELETE_USER = 'https://qyapi.weixin.qq.com/cgi-bin/tag/deltagusers';
  const TAG_GET_LIST = 'https://qyapi.weixin.qq.com/cgi-bin/tag/list';


  /**
   * @Purpose:
   * 创建标签
   * @Method tagCreate()
   *
   * @param array $tag
   *
   * @return false|mixed
   *
   * @throws null
   *
   * @link https://qyapi.weixin.qq.com/cgi-bin/tag/create?access_token=ACCESS_TOKEN
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/2/23
   */
  public function tagCreate($tag) {
    return $this->callPost(self::TAG_CREATE, $tag);
  }


  /**
   * @Purpose:
   * 更新标签名字
   * @Method tagUpdate()
   *
   * @param array $tag
   *
   * @return false|mixed
   *
   * @throws null
   *
   * @link https://qyapi.weixin.qq.com/cgi-bin/tag/update?access_token=ACCESS_TOKEN
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/2/23
   */
  public function tagUpdate($tag) {
    return $this->callPost(self::TAG_UPDATE, $tag);
  }


  /**
   * @Purpose:
   * 删除标签
   * @Method tagDelete()
   *
   * @param integer $tagid 标签ID
   *
   * @return false|mixed
   *
   * @throws null
   *
   * @link https://qyapi.weixin.qq.com/cgi-bin/tag/delete?access_token=ACCESS_TOKEN&tagid=TAGID
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/2/23
   */
  public function tagDelete($tagid) {
    return $this->callGet(self::TAG_DELETE, array('tagid' => $tagid));
  }

  public function TagGetUser($tagid) {
    return $this->callGet(self::TAG_GET_USER, array('tagid' => $tagid));
  }

  public function TagAddUser($tagid, $userIdList = array(), $partyIdList = array()) {
    return $this->callPost(self::TAG_ADD_USER, ['tagid' => $tagid, 'userlist' => $userIdList, 'partylist' => $partyIdList]);
  }


  public function TagDeleteUser($tagid, $userIdList, $partyIdList) {
    return $this->callPost(self::TAG_DELETE_USER, ['tagid' => $tagid, 'userlist' => $userIdList, 'partylist' => $partyIdList]);
  }

  public function TagGetList() {
    return $this->callGet(self::TAG_GET_LIST);
  }
}
