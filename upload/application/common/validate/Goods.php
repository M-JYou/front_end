<?php

namespace app\common\validate;

use \app\common\validate\BaseValidate;

class Goods extends BaseValidate {
  protected $rule =   [
    'addtime'       => 'gt:0',        // '创建时间',
    'create'        => 'gt:0',        // '创建人id',
    'type'          => 'require|gt:0',// '类型',
    'type2'         => '>=:0',        // '类型',
    'name'          => 'max:32',      // '名称',32
    'examine'       => 'max:255',     // '审核文本',255
    'simple'        => 'max:255',     // '简介',255
    'content'       => 'max:65535',   // '正文',65535
    'cover'         => 'max:255',     // '封面',255
    'banner'        => 'max:510',     // '横幅',510
    'seo_title'     => 'max:255',     // 'seo标题',255
    'seo_desc'      => 'max:255',     // 'seo简介',255
    'seo_keywords'  => 'max:255',     // 'seo关键字',255
  ];
}
