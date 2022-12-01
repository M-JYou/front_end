<?php

namespace app\common\validate;

use \app\common\validate\BaseValidate;

class LinkType extends BaseValidate {
  protected $rule =   [
    // 'id' => 'integer', // INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    'name' => 'require|max:32', // VARCHAR(32) NOT NULL,
    'is_sys' => 'in:0,1', // TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  ];

  protected $message  =   [
    'name.require'   => '请填写名称',
    'name.max'  => '名称不能超过32个字',
    'is_sys'  => '是否显示必须是0,1',
  ];

  protected $scene = [
    'default'  =>  ['name', 'is_sys'],
  ];
}
