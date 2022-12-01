<?php

namespace app\common\validate;

use \app\common\validate\BaseValidate;

class Ask extends BaseValidate {
  protected $rule =   [
    'is_display' => 'require|in:0,1',
    'power'  => 'number',
    'point'  => 'number',
    'create'  => 'require|max:9',
    'answer' => 'number', // int(10) COMMENT '采纳回答id',
    'state' => 'number|between:0,255',
    'name'   => 'require|max:30',
    'content' => 'require',
  ];
}
