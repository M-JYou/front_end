<?php

namespace app\common\base;

use ArrayObject;

class Userinfo implements \ArrayAccess {
  public $uid = 0;
  public $utype = 0;
  public $mobile = 0;

  public function __construct($data = []) {
    $data && $this->init($data);
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
