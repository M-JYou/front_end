<?php

/** 企业客户回调通知 */

namespace app\common\lib\corpwechat\event;

use app\common\lib\corpwechat\ExternalContact;
use app\common\lib\corpwechat\User;
use think\Exception;

class ChangeExternalContactEvent extends Event {
  /** 添加企业客户事件
   * @Method addExternalContact()
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/9
   */
  public function addExternalContact($decryMsg) {
    /** 1.调用企业微信【获取客户详情】API
     * 获取企微客户关系数据
     */
    $flag = false; // 分页查询标识符
    $next_cursor = ''; // 分页参数
    $follow_user_info = array(); // 添加了此外部联系人的企业成员信息
    $external_user_info = array(); // 外部联系人信息
    do {
      // 调用企业微信【获取客户详情】API
      $externalContact = new ExternalContact($this->corpId, $this->corpSecret);
      $data = $externalContact->externalContactGet($decryMsg['ExternalUserID'], $next_cursor);

      if (false === $data) {
        $this->errorMessage = '[external_contact_get]API请求失败。' . $externalContact->getError();
        return false;
      }

      // 获取外部联系人的信息
      if (isset($data['external_contact']) && !empty($data['external_contact'])) {
        $external_user_info = $data['external_contact'];
      } else {
        $this->errorMessage = '缺少`external_contact`字段信息';
        return false;
      }

      // 获取添加了此外部联系人的企业成员相关信息
      if (isset($data['follow_user']) && !empty($data['follow_user'])) {
        $follow_user = $data['follow_user'];
        foreach ($follow_user as $user) {
          if (!is_array($user) || count($user) < 1) {
            $this->errorMessage = '添加了此外部联系人的企业成员数据错误';
            return false;
          }
          if ($user['userid'] == $decryMsg['UserID']) {
            $flag = false;
            $follow_user_info = $user;
          } else {
            $flag = true;
            continue;
          }
        }
      }

      // 判断是否需要继续分页查询
      if (isset($data['next_cursor']) && !empty($data['next_cursor'])) {
        if ($flag) {
          $next_cursor = $data['next_cursor'];
        } else {
          $next_cursor = '';
        }
      } else {
        $next_cursor = '';
      }
    } while (!empty($next_cursor));


    /** 2.判断企业微信外部联系人的userid是否存在
     * 1.）不存在，写入
     * 2.）存在，更新
     */
    if (isset($external_user_info['avatar']) && !empty($external_user_info['avatar'])) {
      $avatar = $external_user_info['avatar'];
      $thumb_avatar = $this->getThumbAvatarByAvatar($avatar);
    } else {
      $avatar = '';
      $thumb_avatar = '';
    }
    $external_user_id_is_set = $this->userIDIsSet($decryMsg['ExternalUserID'], USER_TYPE_EXTERNAL);
    if (false === $external_user_id_is_set) {
      model('corpwechat.CorpwechatUserAll')
        ->data(
          [
            'userid' => $decryMsg['ExternalUserID'],
            'name' => isset($external_user_info['name']) ? $external_user_info['name'] : '',
            'user_type' => USER_TYPE_EXTERNAL,
            'register' => 3,
            'external_type' => isset($external_user_info['type']) ? $external_user_info['type'] : 0, // 外部联系人类型[1:微信用户;2:企业微信用户]
            'gender' => isset($external_user_info['gender']) ? $external_user_info['gender'] : 0,
            'avatar' => $avatar,
            'thumb_avatar' => $thumb_avatar,
            'unionid' => isset($external_user_info['unionid']) ? $external_user_info['unionid'] : '',
            'corp_name' => isset($external_user_info['corp_name']) ? $external_user_info['corp_name'] : '',
            'corp_full_name' => isset($external_user_info['corp_full_name']) ? $external_user_info['corp_full_name'] : '',
          ]
        )
        ->allowField(true)
        ->isUpdate(false)
        ->save();
    } else {
      model('corpwechat.CorpwechatUserAll')
        ->allowField(true)
        ->isUpdate(true)
        ->save(
          [
            'name' => isset($external_user_info['name']) ? $external_user_info['name'] : '',
            'external_type' => isset($external_user_info['type']) ? $external_user_info['type'] : 0, // 外部联系人类型[1:微信用户;2:企业微信用户]
            'gender' => isset($external_user_info['gender']) ? $external_user_info['gender'] : 0,
            'avatar' => $avatar,
            'thumb_avatar' => $thumb_avatar,
            'unionid' => isset($external_user_info['unionid']) ? $external_user_info['unionid'] : '',
            'corp_name' => isset($external_user_info['corp_name']) ? $external_user_info['corp_name'] : '',
            'corp_full_name' => isset($external_user_info['corp_full_name']) ? $external_user_info['corp_full_name'] : '',
          ],
          [
            'userid' => $decryMsg['ExternalUserID'],
          ]
        );
    }

    /** 3.判断企业微信成员UserID
     * 1.）不存在，写入
     * 2.）存在，跳过
     */
    $user_id_is_set = $this->userIDIsSet($decryMsg['UserID'], USER_TYPE_STAFF);
    if (false === $user_id_is_set) {
      model('corpwechat.CorpwechatUserAll')
        ->data(
          [
            'userid' => $decryMsg['UserID'],
            'user_type' => USER_TYPE_STAFF,
            'register' => 4
          ]
        )
        ->allowField(true)
        ->isUpdate(false)
        ->save();
    }

    /** 4.判断企业微信成员是否进入员工表`corpwechat_staff`
     * 1.）不存在，调用接口获取员工详情，并写入
     * 2.）存在，跳过
     */
    $staff_is_set = $this->staffIsSet($decryMsg['UserID']);
    if (false === $staff_is_set) {
      // 调用企业微信【读取成员】API
      $corp_user = new User($this->corpId, $this->corpSecret);
      $user_get = $corp_user->UserGet($decryMsg['UserID']);
      if (false === $user_get) {
        $this->errorMessage = '[user_get]API请求失败。' . $corp_user->getError();
        return false;
      }

      // 写入`corpwechat_staff`表
      model('corpwechat.CorpwechatStaff')
        ->data(
          [
            'userid' => $decryMsg['UserID'],
            'department' => isset($user_get['department']) ? $user_get['department'] : [],
            'mobile' => isset($user_get['mobile']) ? $user_get['mobile'] : '',
            'email' => isset($user_get['email']) ? $user_get['email'] : '',
            'status' => isset($user_get['status']) ? $user_get['status'] : '',
            'qr_code' => isset($user_get['qr_code']) ? $user_get['qr_code'] : '',
            'alias' => isset($user_get['alias']) ? $user_get['alias'] : '',
            'biz_mail' => isset($user_get['biz_mail']) ? $user_get['biz_mail'] : ''
          ]
        )
        ->allowField(true)
        ->isUpdate(false)
        ->save();

      // 更新`corpwechat_user_all`表
      model('corpwechat.CorpwechatUserAll')
        ->allowField(true)
        ->isUpdate(true)
        ->save(
          [
            'name' => isset($user_get['name']) ? $user_get['name'] : '',
            'gender' => isset($user_get['gender']) ? $user_get['gender'] : 0,
            'avatar' => isset($user_get['avatar']) ? $user_get['avatar'] : '',
            'thumb_avatar' => isset($user_get['thumb_avatar']) ? $user_get['thumb_avatar'] : '',
          ],
          [
            'userid' => $decryMsg['UserID'],
          ]
        );
    }

    /** 5.判断企业微信成员是否进入员工表`corpwechat_staff`
     * 1.）不存在，调用接口获取员工详情，并写入
     * 2.）存在，跳过
     */
    $external_user_is_set = $this->externalUserIsSet($decryMsg['UserID'], $decryMsg['ExternalUserID']);
    if (false === $external_user_is_set) {
      // 先清空标签库【外部联系人标签】
      model('corpwechat.CorpwechatExternalTag')
        ->where('external_user_id', $decryMsg['ExternalUserID'])
        ->where('userid', $decryMsg['UserID'])
        ->delete();

      // 判断企微客户标签信息
      $insert_tags = array();
      $follow_user_tags = array();
      $follow_user_tag_group = array();
      if (isset($user['tags']) && !empty($user['tags'])) {
        $tags = $user['tags'];
        foreach ($tags as $tag) {
          switch ($tag['type']) {
            case 1:
              // 1-企业设置
              $insert_tags[] = [
                'external_user_id' => $decryMsg['ExternalUserID'],
                'userid' => $decryMsg['UserID'],
                'group_name' => $tag['group_name'],
                'tag_name' => $tag['tag_name'],
                'type' => $tag['type'],
                'tag_id' => $tag['tag_id']
              ];
              $follow_user_tags[$tag['tag_name']] = $tag['tag_id'];
              $follow_user_tag_group[$tag['group_name']] = $tag['group_name'];
              break;

            default:
              // 2-用户自定义，3-规则组标签，以及其余情况忽略
              break;
          }
        }
        // 新标签入库
        model('corpwechat.CorpwechatExternalTag')
          ->allowField(true)
          ->isUpdate(false)
          ->saveAll($insert_tags, false);
      }

      // 写入企微客户表
      model('corpwechat.CorpwechatExternalUser')
        ->allowField(true)
        ->isUpdate(false)
        ->save([
          'external_user_id' => $decryMsg['ExternalUserID'],
          'userid' => $decryMsg['UserID'],
          'state' => isset($decryMsg['State']) ? $decryMsg['State'] : '',
          'remark' => isset($follow_user_info['remark']) ? $follow_user_info['remark'] : '',
          'add_way' => isset($follow_user_info['add_way']) ? $follow_user_info['add_way'] : 0,
          'add_time' => isset($follow_user_info['createtime']) ? $follow_user_info['createtime'] : 0,
          'oper_userid' => isset($follow_user_info['oper_userid']) ? $follow_user_info['oper_userid'] : 0,
          'tags' => $follow_user_tags,
          'tag_group' => $follow_user_tag_group
        ]);

      $staff_name = $this->getUserNameByUserId($decryMsg['UserID']);
      $external_name = isset($external_user_info['name']) ? $external_user_info['name'] : $decryMsg['ExternalUserID'];
      model('corpwechat.CorpwechatExternalLog')
        ->log(
          $external_name . '添加了员工【' . $staff_name . '】',
          $decryMsg['ExternalUserID'],
          ADD_EXTERNAL_CONTACT,
          $decryMsg['UserID']
        );
    }

    return true;
  }


  /** 编辑企业客户事件
   * @Method editExternalContact()
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/9
   */
  public function editExternalContact($decryMsg) {
    /** 1.调用企业微信【获取客户详情】API
     * 获取企微客户关系数据
     */
    $flag = false; // 分页查询标识符
    $next_cursor = ''; // 分页参数
    $follow_user_info = array(); // 添加了此外部联系人的企业成员信息
    $external_user_info = array(); // 外部联系人信息
    do {
      // 调用企业微信【获取客户详情】API
      $externalContact = new ExternalContact($this->corpId, $this->corpSecret);
      $data = $externalContact->externalContactGet($decryMsg['ExternalUserID'], $next_cursor);

      if (false === $data) {
        $this->errorMessage = '[external_contact_get]API请求失败。' . $externalContact->getError();
        return false;
      }

      // 获取外部联系人的信息
      if (isset($data['external_contact']) && !empty($data['external_contact'])) {
        $external_user_info = $data['external_contact'];
      } else {
        $this->errorMessage = '缺少`external_contact`字段信息';
        return false;
      }

      // 获取添加了此外部联系人的企业成员相关信息
      if (isset($data['follow_user']) && !empty($data['follow_user'])) {
        $follow_user = $data['follow_user'];
        foreach ($follow_user as $user) {
          if (!is_array($user) || count($user) < 1) {
            $this->errorMessage = '添加了此外部联系人的企业成员数据错误';
            return false;
          }
          if ($user['userid'] == $decryMsg['UserID']) {
            $flag = false;
            $follow_user_info = $user;
          } else {
            $flag = true;
            continue;
          }
        }
      }

      // 判断是否需要继续分页查询
      if (isset($data['next_cursor']) && !empty($data['next_cursor'])) {
        if ($flag) {
          $next_cursor = $data['next_cursor'];
        } else {
          $next_cursor = '';
        }
      } else {
        $next_cursor = '';
      }
    } while (!empty($next_cursor));


    /** 2.判断企业微信外部联系人的userid是否存在
     * 1.）不存在，写入
     * 2.）存在，更新
     */
    if (isset($external_user_info['avatar']) && !empty($external_user_info['avatar'])) {
      $avatar = $external_user_info['avatar'];
      $thumb_avatar = $this->getThumbAvatarByAvatar($avatar);
    } else {
      $avatar = '';
      $thumb_avatar = '';
    }
    $external_user_id_is_set = $this->userIDIsSet($decryMsg['ExternalUserID'], 2);
    if (false === $external_user_id_is_set) {
      model('corpwechat.CorpwechatUserAll')
        ->data(
          [
            'userid' => $decryMsg['ExternalUserID'],
            'name' => isset($external_user_info['name']) ? $external_user_info['name'] : '',
            'user_type' => 2,
            'register' => 3,
            'external_type' => isset($external_user_info['type']) ? $external_user_info['type'] : 0, // 外部联系人类型[1:微信用户;2:企业微信用户]
            'gender' => isset($external_user_info['gender']) ? $external_user_info['gender'] : 0,
            'avatar' => $avatar,
            'thumb_avatar' => $thumb_avatar,
            'unionid' => isset($external_user_info['unionid']) ? $external_user_info['unionid'] : '',
            'corp_name' => isset($external_user_info['corp_name']) ? $external_user_info['corp_name'] : '',
            'corp_full_name' => isset($external_user_info['corp_full_name']) ? $external_user_info['corp_full_name'] : '',
          ]
        )
        ->allowField(true)
        ->isUpdate(false)
        ->save();
    } else {
      model('corpwechat.CorpwechatUserAll')
        ->allowField(true)
        ->isUpdate(true)
        ->save(
          [
            'name' => isset($external_user_info['name']) ? $external_user_info['name'] : '',
            'external_type' => isset($external_user_info['type']) ? $external_user_info['type'] : 0, // 外部联系人类型[1:微信用户;2:企业微信用户]
            'gender' => isset($external_user_info['gender']) ? $external_user_info['gender'] : 0,
            'avatar' => $avatar,
            'thumb_avatar' => $thumb_avatar,
            'unionid' => isset($external_user_info['unionid']) ? $external_user_info['unionid'] : '',
            'corp_name' => isset($external_user_info['corp_name']) ? $external_user_info['corp_name'] : '',
            'corp_full_name' => isset($external_user_info['corp_full_name']) ? $external_user_info['corp_full_name'] : '',
          ],
          [
            'userid' => $decryMsg['ExternalUserID'],
          ]
        );
    }

    /** 3.判断企业微信成员UserID
     * 1.）不存在，写入
     * 2.）存在，跳过
     */
    $user_id_is_set = $this->userIDIsSet($decryMsg['UserID'], USER_TYPE_STAFF);
    if (false === $user_id_is_set) {
      model('corpwechat.CorpwechatUserAll')
        ->data(
          [
            'userid' => $decryMsg['UserID'],
            'user_type' => USER_TYPE_STAFF,
            'register' => 4
          ]
        )
        ->allowField(true)
        ->isUpdate(false)
        ->save();
    }

    /** 4.判断企业微信成员是否进入员工表`corpwechat_staff`
     * 1.）不存在，调用接口获取员工详情，并写入
     * 2.）存在，跳过
     */
    $staff_is_set = $this->staffIsSet($decryMsg['UserID']);
    if (false === $staff_is_set) {
      // 调用企业微信【读取成员】API
      $corp_user = new User($this->corpId, $this->corpSecret);
      $user_get = $corp_user->UserGet($decryMsg['UserID']);
      if (false === $user_get) {
        $this->errorMessage = '[user_get]API请求失败。' . $corp_user->getError();
        return false;
      }

      // 写入`corpwechat_staff`表
      model('corpwechat.CorpwechatStaff')
        ->allowField(true)
        ->isUpdate(false)
        ->save(
          [
            'userid' => $decryMsg['UserID'],
            'department' => isset($user_get['department']) ? $user_get['department'] : [],
            'mobile' => isset($user_get['mobile']) ? $user_get['mobile'] : '',
            'email' => isset($user_get['email']) ? $user_get['email'] : '',
            'status' => isset($user_get['status']) ? $user_get['status'] : '',
            'qr_code' => isset($user_get['qr_code']) ? $user_get['qr_code'] : '',
            'alias' => isset($user_get['alias']) ? $user_get['alias'] : '',
            'biz_mail' => isset($user_get['biz_mail']) ? $user_get['biz_mail'] : ''
          ]
        );

      // 更新`corpwechat_user_all`表
      model('corpwechat.CorpwechatUserAll')
        ->allowField(true)
        ->isUpdate(true)
        ->save(
          [
            'name' => isset($user_get['name']) ? $user_get['name'] : '',
            'gender' => isset($user_get['gender']) ? $user_get['gender'] : 0,
            'avatar' => isset($user_get['avatar']) ? $user_get['avatar'] : '',
            'thumb_avatar' => isset($user_get['thumb_avatar']) ? $user_get['thumb_avatar'] : '',
          ],
          [
            'userid' => $decryMsg['UserID'],
          ]
        );
    }

    // 先清空标签库【外部联系人标签】
    model('corpwechat.CorpwechatExternalTag')
      ->where('external_user_id', $decryMsg['ExternalUserID'])
      ->where('userid', $decryMsg['UserID'])
      ->delete();

    // 判断企微客户标签信息
    $insert_tags = array();
    $follow_user_tags = array();
    $follow_user_tag_group = array();
    if (isset($user['tags']) && !empty($user['tags'])) {
      $tags = $user['tags'];
      foreach ($tags as $tag) {
        // 判断标签类型
        switch ($tag['type']) {
          case 1:
            // 1-企业设置
            $insert_tags[] = [
              'external_user_id' => $decryMsg['ExternalUserID'],
              'userid' => $decryMsg['UserID'],
              'group_name' => $tag['group_name'],
              'tag_name' => $tag['tag_name'],
              'type' => $tag['type'],
              'tag_id' => $tag['tag_id']
            ];
            $follow_user_tags[$tag['tag_name']] = $tag['tag_id'];
            $follow_user_tag_group[$tag['group_name']] = $tag['group_name'];
            break;

          default:
            // 2-用户自定义，3-规则组标签，以及其余情况忽略
            break;
        }
      }
      // 新标签入库
      model('corpwechat.CorpwechatExternalTag')
        ->allowField(true)
        ->isUpdate(false)
        ->saveAll($insert_tags, false);
    }

    /** 5.判断企业微信成员是否进入员工表`corpwechat_staff`
     * 1.）不存在，调用接口获取员工详情，并写入
     * 2.）存在，更新
     */
    $external_user_is_set = $this->externalUserIsSet($decryMsg['UserID'], $decryMsg['ExternalUserID']);
    if (false === $external_user_is_set) {
      // 写入企微客户表
      model('corpwechat.CorpwechatExternalUser')
        ->allowField(true)
        ->isUpdate(false)
        ->save(
          [
            'external_user_id' => $decryMsg['ExternalUserID'],
            'userid' => $decryMsg['UserID'],
            'state' => isset($decryMsg['State']) ? $decryMsg['State'] : '',
            'remark' => isset($follow_user_info['remark']) ? $follow_user_info['remark'] : '',
            'add_way' => isset($follow_user_info['add_way']) ? $follow_user_info['add_way'] : 0,
            'add_time' => isset($follow_user_info['createtime']) ? $follow_user_info['createtime'] : 0,
            'oper_userid' => isset($follow_user_info['oper_userid']) ? $follow_user_info['oper_userid'] : 0,
            'tags' => $follow_user_tags,
            'tag_group' => $follow_user_tag_group
          ],
          false
        );
    } else {
      // 写入企微客户表
      model('corpwechat.CorpwechatExternalUser')
        ->allowField(true)
        ->isUpdate(true)
        ->save(
          [
            'state' => isset($decryMsg['State']) ? $decryMsg['State'] : '',
            'remark' => isset($follow_user_info['remark']) ? $follow_user_info['remark'] : '',
            'add_way' => isset($follow_user_info['add_way']) ? $follow_user_info['add_way'] : 0,
            'add_time' => isset($follow_user_info['createtime']) ? $follow_user_info['createtime'] : 0,
            'oper_userid' => isset($follow_user_info['oper_userid']) ? $follow_user_info['oper_userid'] : 0,
            'tags' => $follow_user_tags,
            'tag_group' => $follow_user_tag_group
          ],
          [
            'external_user_id' => $decryMsg['ExternalUserID'],
            'userid' => $decryMsg['UserID'],
          ]
        );
    }
    return true;
  }


  /** 删除跟进成员事件/删除企业客户事件
   * @Method delExternalContact()
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/9
   */
  public function delExternalContact($decryMsg) {
    /** 1.判断企业微信外部联系人的userid是否存在
     * 1.）不存在，写入
     * 2.）存在，更新
     */
    $external_user_id_is_set = $this->userIDIsSet($decryMsg['ExternalUserID'], 2);
    if (false === $external_user_id_is_set) {
      model('corpwechat.CorpwechatUserAll')
        ->data(
          [
            'userid' => $decryMsg['ExternalUserID'],
            'user_type' => 2,
            'register' => 3,
          ]
        )
        ->allowField(true)
        ->isUpdate(false)
        ->save();
    }

    /** 2.判断企业微信成员UserID
     * 1.）不存在，写入
     * 2.）存在，跳过
     */
    $user_id_is_set = $this->userIDIsSet($decryMsg['UserID'], 1);
    if (false === $user_id_is_set) {
      model('corpwechat.CorpwechatUserAll')
        ->data(
          [
            'userid' => $decryMsg['UserID'],
            'user_type' => 1,
            'register' => 4
          ]
        )
        ->allowField(true)
        ->isUpdate(false)
        ->save();
    }

    // 判断员工和客户关系是否存在
    $customerIsSet = $this->externalUserIsSet($decryMsg['UserID'], $decryMsg['ExternalUserID']);
    if (!$customerIsSet) {
      $this->errorMessage = '企微客户关系不存在';
      return false;
    }

    // 判断是否有`state`参数
    $external_state = model('corpwechat.CorpwechatExternalUser')
      ->where('external_user_id', $decryMsg['ExternalUserID'])
      ->where('userid', $decryMsg['UserID'])
      ->value('state');

    // 删除企微
    model('corpwechat.CorpwechatExternalUser')
      ->where('external_user_id', $decryMsg['ExternalUserID'])
      ->where('userid', $decryMsg['UserID'])
      ->delete();

    // 删除用户标签
    model('corpwechat.CorpwechatExternalTag')
      ->where('external_user_id', $decryMsg['ExternalUserID'])
      ->where('userid', $decryMsg['UserID'])
      ->delete();

    $staff_name = $this->getUserNameByUserId($decryMsg['UserID']);
    $contact_name = $this->getUserNameByUserId($decryMsg['ExternalUserID']);

    switch ($decryMsg['ChangeType']) {
      case 'del_external_contact':
        model('corpwechat.CorpwechatExternalLog')
          ->log(
            $contact_name . '被员工【' . $staff_name . '】删除了',
            $decryMsg['ExternalUserID'],
            DEL_EXTERNAL_CONTACT,
            $decryMsg['UserID']
          );
        if (isset($external_state) && !empty($external_state)) {
          model('corpwechat.corpwechatChannelLog')
            ->data(
              [
                'external_user_id' => $decryMsg['ExternalUserID'],
                'userid' => $decryMsg['UserID'],
                'state' => $external_state,
                'type' => 2,
                'content' => '被解除渠道活码好友关系'
              ]
            )
            ->allowField(true)
            ->isUpdate(false)
            ->save();

          $is_day_log = model('corpwechat.CorpwechatChannelDayLog')
            ->where('state', $decryMsg['State'])
            ->whereTime('create_time', 'today')
            ->find();
          if (null === $is_day_log) {
            model('corpwechat.CorpwechatChannelDayLog')
              ->data([
                'state' => $decryMsg['State'],
                'del_total' => 1,
                'external_del' => 1
              ])
              ->allowField(true)
              ->isUpdate(false)
              ->save();
          } else {
            model('corpwechat.CorpwechatChannelDayLog')
              ->where('state', $decryMsg['State'])
              ->whereTime('create_time', 'today')
              ->update([
                'del_total' => ['inc', 1],
                'external_del' => ['inc', 1]
              ]);
          }
        }
        break;

      case 'del_follow_user':
        model('corpwechat.CorpwechatExternalLog')
          ->log(
            $contact_name . '删除了员工【' . $staff_name . '】',
            $decryMsg['ExternalUserID'],
            DEL_FOLLOW_USER,
            $decryMsg['UserID']
          );
        if (isset($external_state) && !empty($external_state)) {
          model('corpwechat.corpwechatChannelLog')
            ->data(
              [
                'external_user_id' => $decryMsg['ExternalUserID'],
                'userid' => $decryMsg['UserID'],
                'state' => $external_state,
                'type' => 3,
                'content' => '主动解除渠道活码好友关系'
              ]
            )
            ->allowField(true)
            ->isUpdate(false)
            ->save();

          $is_day_log = model('corpwechat.CorpwechatChannelDayLog')
            ->where('state', $decryMsg['State'])
            ->whereTime('create_time', 'today')
            ->find();
          if (null === $is_day_log) {
            model('corpwechat.CorpwechatChannelDayLog')
              ->data([
                'state' => $decryMsg['State'],
                'del_total' => 1,
                'follow_del' => 1
              ])
              ->allowField(true)
              ->isUpdate(false)
              ->save();
          } else {
            model('corpwechat.CorpwechatChannelDayLog')
              ->where('state', $decryMsg['State'])
              ->whereTime('create_time', 'today')
              ->update([
                'del_total' => ['inc', 1],
                'follow_del' => ['inc', 1]
              ]);
          }
        }
        break;

      default:
        break;
    }
    return true;
  }


  /** 返回错误信息
   * @Method getError()
   *
   * @param null
   *
   * @return string
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/18
   */
  public function getError() {
    return $this->errorMessage;
  }
}
