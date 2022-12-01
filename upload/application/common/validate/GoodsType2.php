<?php

namespace app\common\validate;

use \app\common\validate\BaseValidate;

class GoodsType2 extends BaseValidate {
  protected $rule =   [
    'pid'       => 'require',         // '上级id',
    // 'status'    => 'in:0,1,3',          // '状态',
    // 'addtime'   => 'require|gt:0',    // '创建时间',
    'create'    => 'require|gt:0',    // '创建人id',
    'name'      => 'require|max:32',  // '名称',
    'simple'    => 'require|max:255', // '简介',
  ];
  protected $message  =   [
    'create.require' => '请登录',
    'create.gt' => '请登录',
  ];

}
