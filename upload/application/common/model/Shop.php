<?php

namespace app\common\model;

class Shop extends \app\common\model\BaseModel {
  protected $readonly = ['id', 'addtime', 'create'];
  protected $type = [
    'id' => 'integer', // INT id
    'addtime' => 'integer', // INT 创建时间
    'create' => 'integer', // INT 用户id
    'sort' => 'integer', // INT 排序
    'is_display' => 'integer', // TINYINT(1) 是否通过
    'evaluate' => 'integer', // TINYINT 评价(满分100)
    'service' => 'integer', // TINYINT 服务(满分100)
    'logistics' => 'integer', // TINYINT 物流(满分100)

    'name' => 'string', // CHAR(64) 名称
    'photo' => 'string', // CHAR(128) 头像
    'addr' => 'string', // VARCHAR(2048) 地址
    'credit' => 'string', // CHAR(64) 信用代码
    'legal' => 'string', // CHAR(64) 法人
    'email' => 'string', // CHAR(64) 邮箱
    'weixin' => 'string', // CHAR(64) 微信号
    'background' => 'string', // CHAR(255) 背景
    'autograph' => 'string', // CHAR(255) 签名
    'other' => 'array', // TEXT 其他json
  ];
  protected $insert = ['addtime'];
  protected function setAddtimeAttr() {
    return time();
  }
  protected function getGoodsAttr($v) {
    return $this->retOtherAttr($v);
  }
  protected function getType2Attr($v) {
    return $this->retOtherAttr($v, 'GoodsType2');
  }
}
