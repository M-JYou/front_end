<?php

namespace app\common\model;

class AdminLog extends \app\common\model\BaseModel {
  public function record($content, $admin_info, $is_login = 0, $id = null, $username = null, $ip = null, $port = null) {
    if (!isset($id)) {
      $id = isset($admin_info->id) ? $admin_info->id : 0;
    }
    if (!isset($username)) {
      $username = isset($admin_info->username) ? $admin_info->username : '';
    }

    $data['admin_id'] = $id;
    $data['admin_name'] = $username;
    $data['content'] = $content;
    $data['is_login'] = $is_login;
    $data['addtime'] = time();
    $data['ip'] = isset($ip) ? $ip : get_client_ip();
    $data['ip_addr'] = get_client_ipaddress($data['ip']);
    $data['ip'] = $data['ip'] . ':' . (isset($port) ? $port : get_client_port());
    $this->save($data);
  }
}
