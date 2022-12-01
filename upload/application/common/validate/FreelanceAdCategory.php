<?php

namespace app\common\validate;

use app\common\validate\BaseValidate;

class FreelanceAdCategory extends BaseValidate {
    protected $rule = [
        'alias' => 'require|max:30|unique:freelance_ad_category',
        'name' => 'require|max:30',
        'ad_num' => 'require|number|gt:0'
    ];
}
