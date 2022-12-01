<?php

namespace app\common\model;

class Apply extends \app\common\model\BaseModel {
  protected $readonly = ['id', 'addtime'];
  protected $type     = [
    "id" => "string", // INT UNSIGNED id
    "addtime" => "string", // INT UNSIGNED 创建时间
    "status" => "string", // TINYINT 状态;0:未处理
    "create" => "string", // CHAR(32) 创建人id
    "type" => "string", // CHAR(32) 类型
    "content" => "string", // TEXT 正文
    "other" => "array", // TEXT 正文
  ];
  protected $insert = ['addtime'];
  protected function setAddtimeAttr() {
    return time();
  }
  public $type_ = [
    '意见反馈',
    '商务合作',
    '代招人才',
    '代找工作',
    '代找账',
    '帮找实习单位',
    '线下实操培训',
    '财务外包',
    '包就业',
    '劳务派遣用工',
    '劳务派遣就业',
    '人才测评',
    '个人背景调查',
    '单位背景调查',
  ];
}
