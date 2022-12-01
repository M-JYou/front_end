<?php

namespace app\common\validate;

use \app\common\validate\BaseValidate;

class Xtime extends BaseValidate {
  protected $rule =   [
    // 'id'              => '>:0',         // INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
    'addtime'         => '>:0',         // INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
    'create'          => '>:0',         // INT(10) UNSIGNED NOT NULL COMMENT '创建人id',
    'sort_id'         => '>:0',         // INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
    'is_display'      => '>=:0',        // TINYINT(1) NOT NULL DEFAULT 1 COMMENT '是否显示',
    'xtime'           => '>=:0',        // INT(10) NOT NULL DEFAULT 0 COMMENT '隶属id;评论需要设置此值为目标id',
    'name'            => 'max:32',      // CHAR(32) NOT NULL COMMENT '名称',
    'content'         => 'string',      // MEDIUMTEXT NOT NULL COMMENT '正文富文本',
    'other'           => 'array',       // 其他
  ];
}
