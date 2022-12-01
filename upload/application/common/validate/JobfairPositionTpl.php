<?php

namespace app\common\validate;

use app\common\validate\BaseValidate;

class JobfairPositionTpl extends BaseValidate {
    protected $rule = [
        'title' => 'require|max:30',
        'area' => 'require',
        'position' => 'require',
        'status' => 'integer|in:0,1'
    ];
    protected $message = [
        'title.require' => '请填写模板名称',
        'title.max' => '模板名称应在30个字符内',
        'area.require' => '请选择展区',
        'position.require' => '请填写展位',
        'status.integer' => '请正确选择展位状态',
        'recommend.in' => '请正确选择展位状态'
    ];
}
