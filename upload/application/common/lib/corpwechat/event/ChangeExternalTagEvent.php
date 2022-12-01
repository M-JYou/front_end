<?php

namespace app\common\lib\corpwechat\event;

use app\common\lib\corpwechat\ExternalContact;
use think\Db;
use think\Log;

class ChangeExternalTagEvent extends Event {
  /**
   * @Purpose:
   * 同步企业客户标签
   * @Method synchronization()
   *
   * @param null
   *
   * @return null
   *
   * @throws null
   *
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/14
   */
  public function synchronization() {
    $externalContact = new ExternalContact($this->corpId, $this->corpSecret);
    $data = $externalContact->getCorpTagList();

    if (false === $data) {
      Log::error('【同步企业客户标签失败】：' . $externalContact->getError());
    }

    if (isset($data['tag_group']) && !empty($data['tag_group'])) {
      $tag_group = $data['tag_group'];
    } else {
      Log::error('【同步企业客户标签失败】：同步失败');
    }

    $insert_data = [];
    foreach ($tag_group as $groups) {
      $group_info = array(
        'id' => $groups['group_id'],
        'group_id' => 0,
        'name' => $groups['group_name'],
        'group_name' => $groups['group_name'],
        'type' => 1,
        'create_time' => $groups['create_time'],
        'order' => $groups['order']
      );
      array_push($insert_data, $group_info);
      unset($group_info);

      if (is_array($groups['tag']) && count($groups['tag']) > 0) {
        foreach ($groups['tag'] as $tags) {
          $tag_info = array(
            'id' => $tags['id'],
            'name' => $tags['name'],
            'group_id' => $groups['group_id'],
            'group_name' => $groups['group_name'],
            'type' => 2,
            'create_time' => $tags['create_time'],
            'order' => $tags['order']
          );
          array_push($insert_data, $tag_info);
          unset($tag_info);
        }
      } else {
        continue;
      }
    }

    Db::startTrans();
    try {
      $del_result = model('corpwechat.CorpwechatCorptag')
        ->where('id', '<>', 0)
        ->whereOr('group_id', '<>', 0)
        ->delete();
      if (false === $del_result) {
        throw new \Exception(model('corpwechat.CorpwechatCorptag')->getError());
      }

      $admin_result = model('corpwechat.CorpwechatCorptag')
        ->allowField(true)
        ->isUpdate(false)
        ->saveAll($insert_data, false);
      if (false === $admin_result) {
        throw new \Exception(model('corpwechat.CorpwechatCorptag')->getError());
      }

      // 提交事务
      Db::commit();
    } catch (\Exception $e) {
      // 回滚事务
      Db::rollBack();
      Log::error('【同步企业客户标签失败-DB事务】：' . $e->getMessage());
    }
  }
}
