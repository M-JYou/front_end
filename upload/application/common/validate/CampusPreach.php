<?php

namespace app\common\validate;

use \app\common\validate\BaseValidate;

class CampusPreach extends BaseValidate {
    protected $rule =   [
        'school_id' => 'require|number',
        'subject'   => 'require|max:100',
        'display' => 'require|in:0,1',
        'address' => 'require|max:200',
        'starttime' => 'require|number',
        'introduction' => 'require'
    ];
}
