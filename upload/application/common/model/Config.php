<?php

namespace app\common\model;

class Config extends \app\common\model\BaseModel {
  protected $readonly = ['id', 'name', 'note'];
  protected $type = [
    'id' => 'integer',
  ];
  protected static function init() {
    self::event('after_write', function () {
      cache('cache_config_frontend', null);
      cache('cache_config', null);
      cache('cache_config_all', null);
    });
    self::event('after_delete', function () {
      cache('cache_config_frontend', null);
      cache('cache_config', null);
      cache('cache_config_all', null);
    });
  }
  public function getCache($name = '', $set = false) {
    if ($set || (false === ($data = cache('cache_config')))) {
      $data = $this->where('is_secret', 0)->column('name,value');
      foreach ($data as $key => $value) {
        if (is_json($value)) {
          $data[$key] = json_decode($value, true);
        }
      }
      if (isset($data['live_app_key']) && $data['live_app_key'] != '') {
        $data['live_app_secret'] = md5(md5($data['live_app_key'] . $data['live_app_secret']) . 'ergFGsdfgf545');
      } else {
        $data['live_app_secret'] = '';
      }
      cache('cache_config', $data);
    }
    if ($name != '') {
      $data = $data[$name];
    }
    return $data;
  }
  public function getFrontendCache($name = '', $set = false) {
    if ($set || (false === ($data = cache('cache_config_frontend')))) {
      $data = $this->where('is_frontend', 1)->column('name,value');
      foreach ($data as $key => $value) {
        if (is_json($value)) {
          $data[$key] = json_decode($value, true);
        }
      }
      cache('cache_config_frontend', $data);
    }
    if ($name != '') {
      $data = $data[$name];
    }
    return $data;
  }
  public function getCacheAll($name = '', $set = false) {
    if ($set || (false === ($data = cache('cache_config_all')))) {
      $data = $this->column('name,value');
      foreach ($data as $key => $value) {
        if (is_json($value)) {
          $data[$key] = json_decode($value, true);
        }
      }
      cache('cache_config_all', $data);
    }
    if ($name != '') {
      $data = $data[$name];
    }
    return $data;
  }
}
