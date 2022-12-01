<?php

namespace app\common\validate;

use \app\common\validate\BaseValidate;

class Link extends BaseValidate {
  protected $rule =   [
    // 'id' => 'integer', // INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    'is_display' => 'in:0,1', // TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
    'name' => 'require|max:20', // VARCHAR(32) NOT NULL,
    'link_url' => 'require|max:255', // VARCHAR(255) NOT NULL,
    'link_ico' => 'max:255', // VARCHAR(255) NOT NULL,
    'sort_id' => 'number', // INT(10) UNSIGNED NOT NULL DEFAULT 0,
    'notes' => 'max:100', // VARCHAR(100) NOT NULL DEFAULT '',
    'content' => 'max:65535', // TEXT NOT NULL COMMENT '正文',
    'type' => 'number', // TINYINT NOT NULL DEFAULT 1 COMMENT '类型',
  ];

  protected $message  =   [
    'name.require'   => '请填写名称',
    'name.max'  => '名称不能超过20个字',
    'link_url.require'  => '请填写跳转链接',
    'link_url.max'  => '跳转链接不能超过255个字',
    'notes'  => '备注不能超过100个字',
    'sort_id'  => '排序必须是数字',
    'is_display'  => '是否显示必须是0,1',
  ];

  protected $scene = [
    'default'  =>  ['is_display', 'name', 'link_url', 'link_ico',  'sort_id', 'notes', 'content', 'type'],
  ];
}
