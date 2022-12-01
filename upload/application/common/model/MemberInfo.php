<?php

namespace app\common\model;

class MemberInfo extends \app\common\model\BaseModel {
  protected $readonly = ['id'];
  protected $type     = [
    'id' => 'integer', //  INT UNSIGNED 用户id
    'auth_face' => 'integer', //  TINYINT(1) UNSIGNED 人脸验证
    'auth_card' => 'integer', //  TINYINT(1) UNSIGNED 身份证验证
    'auth_weixin' => 'integer', //  TINYINT(1) UNSIGNED 微信转款验证

    'online' => 'integer', //  TINYINT(1) UNSIGNED 是否在线
    'name' => 'string', //  CHAR(64) 名称
    'card' => 'string', //  CHAR(18) 身份证
    'photo' => 'string', //  CHAR(128) 头像
    'addr' => 'array', //  VARCHAR(2048) 地址
    'nickname' => 'string', //  CHAR(64) 昵称
    'weixin' => 'string', //  CHAR(64) 微信号
    'background' => 'string', //  CHAR(255) 背景
    'autograph' => 'string', //  CHAR(255) 签名
  ];
  protected function setNameAttr($v, $d) {
    if ($v === '' && isset($d['id'])) {
      $v = $d['id'];
    }
    if (isset($d['#'])) {
      $this->allowField(true);
    } else {
      if (isset($d['id'])) {
        $t = model('Member')->get($d['id']);
        if ($t) {
          $where = ['uid' => $d['id']];
          if ($t['utype'] == 1) { // 企业用户
            model('Company')->update(['companyname' => $v, '#' => 1], $where);
          } elseif ($t['utype'] == 2) { // 个人用户
            model('Resume')->update(['fullname' => $v, '#' => 1], $where);
          }
        }
      }
    }

    return $v;
  }
  protected function setPhotoAttr($v, $d) {
    if (!$v) {
      $v = 'https://www.zixia6.com/static/applet/empty_photo.png';
    }
    if (isset($d['#'])) {
      $this->allowField(true);
    } else {
      if (isset($d['id'])) {
        $t = model('Member')->get($d['id']);
        if ($t && $t['utype'] == 2) {
          model('Resume')->allowField(true)->update([
            'photo_img' => model('Uploadfile')->getIdByValue($v), '#' => 1
          ], ['uid' => $d['id']]);
        }
      }
    }

    return $v;
  }
  protected function setBackground($v) {
    if ($v) {
      $v = config('global_config.sitedomain') . '/assets/images/xtime.png';
    }
    return $v;
  }

  // protected function getPointsAttr($v) {
  //   $m = model('MemberPoints');
  //   $r = $m->where('uid', $v)->find();
  //   if (!$r) {
  //     $r = [['uid' => $v, 'points' => 0, 'note' => '没有用户信息,新建']];
  //     $m->setPoints($r);
  //     $r = $r[0];
  //   }
  //   return $r['points'];
  // }
  protected function getAddressAttr($v) {
    $r = model('CategoryDistrict')->getById($v, 'id,id as fullName');
    return $r['fullName'] ? $r['fullName'] : $r['name'];
  }
  protected function getShopAttr($v) {
    $v = decode($v);
    if (!is_array($v)) {
      $v = ['create' => $v];
    }
    return $this->retOtherAttr($v);
  }
  protected function getExpertAttr($v) {
    $v = decode($v);
    if (!is_array($v)) {
      $v = ['create' => $v];
    }
    return $this->retOtherAttr($v);
  }

  public function saveById($data) {
    if ($this->get($data['id'])) {
      $this->allowField(true)->update($data, ['id' => $data['id']]);
    } else {
      $this->allowField(true)->insert($data);
    }
  }
  public function _find($id) {
    $where = [];
    if ($id) {
      $where['uid'] = is_array($id) ? ['in', $id] : $id;
    }
    $id = model('Member')->where($where)->order('uid asc')->column('uid');
    $r = $this->field('id,online,nickname,photo')->where('id', 'in', $id)->select();
    if (count($id) > count($r)) {
      $rid = $this->where('id', 'in', $id)->column('id');
      foreach ($id as $v) {
        if (!in_array($v, $rid)) {
          $t = ['id' => $v, 'name' => $v, 'photo' => '/upload/resource/empty_photo.png', 'addr' => '[]'];
          $this->allowField(true)->insert($t);
          $r[] = $t;
        }
      }
    }
    $mmp = model('MemberPoints');
    $up = $mmp->where('uid', 'in', $id)->column('uid,points');
    foreach ($r as $v) {
      if (isset($up[$v['id']])) {
        $v['points'] = $up[$v['id']];
      } else {
        $v['points'] = 0;
        $mmp->insert(['uid' => $v['id'], 'points' => 0]);
      }
    }

    return $r;
  }

  public function fetch() {
    $us = model('Member')->column('uid,utype');
    $ms = $this->column(join(',', array_keys($this->type)));

    $company = model('Company')->column('uid,companyname');
    $resume = model('Resume')->column('uid,fullname,photo_img');
    $arr = [];
    foreach ($resume as $k => $v) {
      if ($v && !in_array($v['photo_img'], $arr)) {
        $arr[] = $v['photo_img'];
      }
    }
    $arr = model('Uploadfile')->where('id', 'in', $arr)->column('id,save_path');
    $arr[0] = '/upload/resource/empty_photo.png';
    foreach ($resume as $k => $v) {
      $resume[$k]['photo_img'] = $arr[$v['photo_img']];
    }

    $sql = '';
    $arr = [];
    foreach ($us as $k => $v) {
      $photo = '/upload/resource/empty_photo.png';
      $addr = [];
      if (isset($ms[$k])) {
        if ($ms[$k]['photo']) {
          $photo = $ms[$k]['photo'];
        }
        $addr = $ms[$k]['addr'];
      }
      if ($v == 1) {
        $name = $company[$k];
      } else {
        try {
          $name = $resume[$k]['fullname'];
        } catch (\Throwable $th) {
          $name = $k;
        }
        try {
          $photo = $resume[$k]['photo_img'];
        } catch (\Throwable $th) {
        }
      }
      $arr[] = ['id' => $k, 'name' => $name, 'photo' => $photo, 'addr' => $addr];
      $sql .= ",\n( $k, " . safeSql($name) . ', ' . safeSql($photo) . ', ' . safeSql($addr) . ')';
    }
    $sql = "TRUNCATE `qs_member_info`;\nINSERT INTO `qs_member_info` VALUES" .
      substr($sql, 1) . ';';
    $sql = 'TRUNCATE `qs_member_info`;';
    $this->query($sql);
    return $this->insertAll($arr);
  }
  public function MemberPoints() {
    return $this->hasOne('MemberPoints', 'uid', 'id');
  }
  public function getById($id = null, $field = '*', $except = false) {
    $r = $this->field($field, $except)->find($id);
    if (!$r) {
      $r = $this->field($field, $except)->find();
      foreach ($r as $k => $v) {
        if ($v) {
          if (is_numeric($v)) {
            $r[$k] = 0;
          } elseif (is_string($v)) {
            $r[$k] = '';
          } else {
            $r[$k] = [];
          }
        }
      }
      $r['id'] = 0;
      $r['name'] = '系统';
      $r['photo'] = '/upload/resource/empty_photo.png';
    }
    return $r;
  }
}
