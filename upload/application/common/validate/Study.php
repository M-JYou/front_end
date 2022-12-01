<?php

namespace app\common\validate;

use \app\common\validate\BaseValidate;

class Study extends BaseValidate {
  protected $rule =   [
    // 'id'              => '>:0',         // INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
    'addtime'         => '>:0',         // INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
    'create'          => '>:0',         // INT(10) UNSIGNED NOT NULL COMMENT '创建人id',
    'sort_id'         => '>:0',         // INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
    'type'            => 'in:0,1',      // TINYINT(1) UNSIGNED NOT NULL COMMENT '类型',0:财税问答,1:人社问答
    'expense'         => 'integer',     // INT(10) UNSIGNED  NOT NULL DEFAULT 0 COMMENT '价格',
    'simple'          => 'max:1024',    // VARCHAR(1024) NOT NULL DEFAULT '' COMMENT '简介',
    'simple_teacher'  => 'max:1024',    // VARCHAR(1024) NOT NULL DEFAULT '' COMMENT '简介-讲师',
    'video'           => 'max:1024',    // VARCHAR(1024) NOT NULL DEFAULT '' COMMENT '视频url',
    'cover'           => 'max:1024',    // VARCHAR(1024) NOT NULL DEFAULT '' COMMENT '封面url',
    'pid'             => 'integer',     // INT(10) NOT NULL DEFAULT 0 COMMENT '隶属id;评论需要设置此值为目标id',
    'name'            => 'max:32',      // CHAR(32) NOT NULL COMMENT '名称',
    'content'         => 'string',      // MEDIUMTEXT NOT NULL COMMENT '正文富文本',
    'other'           => 'max:2048',    // VARCHAR(2048) NOT NULL COMMENT '其他',
  ];
}
