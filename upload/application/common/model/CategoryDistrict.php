<?php

namespace app\common\model;

use app\common\lib\Http;

class CategoryDistrict extends \app\common\model\BaseModel {
  protected $readonly = ['id'];
  protected $pid = '';
  protected $type = [
    'id' => 'integer',
    'pid' => 'integer',
    'level' => 'integer',
    'sort_id' => 'integer'
  ];
  protected static function init() {
    self::event('after_write', function () {
      cache('cache_category_district', null);
    });
    self::event('after_delete', function () {
      cache('cache_category_district', null);
    });
  }
  public function getCache($pid = 'all') {
    if (false === ($data = cache('cache_category_district'))) {
      $list = $this->order('sort_id desc,id asc')->column('id,pid,name', 'id');
      $data = [];
      foreach ($list as $key => $value) {
        $data[$value['pid']][$value['id']] = $value['name'];
        $data['all'][$value['id']] = $value['name'];
      }
      cache('cache_category_district', $data);
    }
    if ($pid !== '') {
      $data = isset($data[$pid]) ? $data[$pid] : [];
    }
    return $data;
  }
  public function getTreeCache() {
    if (false === ($list = cache('cache_category_district_tree'))) {
      $list = [];
      $top = $this->getCache('0');
      foreach ($top as $key => $value) {
        $first = [];
        $first['id'] = $key;
        $first['label'] = $value;
        $first_children = $this->getCache($key);
        if ($first_children) {
          $i = 0;
          foreach ($first_children as $k => $v) {
            $second['id'] = $k;
            $second['label'] = $v;
            $second_children = $this->getCache($k);
            if ($second_children) {
              $j = 0;
              foreach ($second_children as $k1 => $v1) {
                $third['id'] = $k1;
                $third['label'] = $v1;
                $second['children'][$j] = $third;
                $third = [];
                $j++;
              }
            } else {
              $second['children'] = [];
            }
            $first['children'][$i] = $second;
            $second = [];
            $i++;
          }
        } else {
          $first['children'] = [];
        }
        $list[] = $first;
      }
      cache('cache_category_district_tree', $list);
    }
    return $list;
  }
  protected $auto = ['alias', 'spell'];
  protected function setAliasAttr() {
    if (isset($this->data['name'])) {
      return (new \app\common\lib\Pinyin())->getFirstPY($this->data['name']);
    }
  }
  protected function setSpellAttr() {
    $id = isset($this->data['id']) ? intval($this->data['id']) : 0;
    if (isset($this->data['name'])) {
      return $this->check_spell_repeat((new \app\common\lib\Pinyin())->getAllPY($this->data['name']), 0, $id);
    }
  }

  protected function getFullNameAttr($v) {
    $t = $this->getById($v, 'id,pid,name');
    $r = '';
    while ($t['id']) {
      $r = $t['name'] . '-' . $r;
      $t = $this->getById($t['pid'], 'id,pid,name');
    }
    return $r ? $r : '全国';
  }
  protected function getWeatherDataAttr($v) {
    try {
      $w = decode(
        (new Http)->get(
          // "http://t.weather.sojson.com/api/weather/city/$v"
          'https://api.map.baidu.com/weather/v1/?ak=' . config('global_config.map_server_ak')
            . "&data_type=fc&output=json&coordtype=wgs84&district_id=$v"
        )
      )['result'];
      // outp($w,$v);
      $d = $w['forecasts'][0];
      $t = $d['text_day'];
      $zw = '暂无';
      if ($d['text_night'] != $zw) {
        if ($t == $zw) {
          $t = $d['text_night'];
        } else {
          $t .= '转' . $d['text_night'];
        }
      }
      $r = [
        'name' => $w['location']['name'],
        'data' => "$t/ " . $d['low'] . '- ' . $d['high'] . getMd($d['date']) . '/' . $d['week']
        // 'data' => $d['type'] . getLast($d['low'], true) . getLast($d['high']) .  getMd($d['ymd']) . $d['week']
      ];
    } catch (\Throwable $th) {
      $r = [
        'name' => '获取异常',
        'data' => '获取气象数据异常'
      ];
    }
    return $r;
  }
  public function getWeather($id = null) {
    if (intval($id)) {
      cookie('cityId', $id);
    } else {
      $id = getCityId();
    }
    // $r = $this->where('id', $id)->field('id,name,img,`id` weatherData')->find();
    $k = "cache_category_district_weather_$id";
    if (!($r = cache($k))) {
      $r = $this->where('id', $id)->field('id,pid,name,img,`id` weatherData')->find();
      cache($k, $r, 600);
    }
    return $r;
  }
}
function getLast($str = '', $simple = false) {
  $ret = '';
  if (!empty($str)) {
    try {
      $ret = explode(" ", $str)[1];
      if ($simple) {
        $ret = '/' . floatval($ret) . '-';
      }
    } catch (\Throwable $th) {
    }
  }
  return $ret;
}
function getMd($str = '') {
  $d = explode('-', $str);
  return '℃/' . intval($d[1]) . '月' . intval($d[2]) . '日/';
}
