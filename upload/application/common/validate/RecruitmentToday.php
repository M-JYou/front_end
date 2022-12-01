<?php

namespace app\common\validate;

use \app\common\validate\BaseValidate;

class RecruitmentToday extends BaseValidate {
    protected $rule =   [
        'subject_name'   => 'require|max:9',
        'click' => 'number|max:10',
        'is_display' => 'require|number|max:1',
    ];
    protected $message = [
        'subject_name.max' =>  '主题名称最多9个字'
    ];
}
