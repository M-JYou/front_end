<?php

namespace app\common\base;

use ArrayObject;

class Admininfo implements \ArrayAccess {
  public $id = 0;
  public $username = "";
  public $password = "";
  public $pwd_hash = "";
  public $role_id = 0;
  public $addtime = 0;
  public $last_login_time = 0;
  public $last_login_ip = "127.0.0.1:12701";
  public $last_login_ipaddress = "内网ip";
  public $openid = "";
  public $is_sc = 0;
  public $qy_userid = "";
  public $qy_openid = "";
  public $bind_qywx = 0;
  public $bind_qywx_time = 0;
  public $mobile = "";
  public $avatar = "";
  public $access = "";
  public $access_mobile = "";
  public $access_export = 0;
  public $access_delete = 0;
  public $access_set_service = 0;
  public $rolename = "空用户";

  public function __construct($data = null) {
    $data && $this->init($data);
    try {
      $this->init($data->getData());
    } catch (\Throwable $th) {
      //throw $th;
    }
  }
  public function init($data = []) {
    foreach ($data as $k => $v) {
      $this->$k = $v;
      $this[$k] = $v;
    }
  }
  public function offsetExists($offset): bool {
    return array_key_exists($offset, get_object_vars($this));
  }
  public function offsetUnset($key): void {
    if (array_key_exists($key, get_object_vars($this))) {
      unset($this->{$key});
    }
  }
  public function offsetSet($offset, $value): void {
    $this->$offset = $value;
  }
  public function offsetGet($var) {
    return $this->$var;
  }
}
