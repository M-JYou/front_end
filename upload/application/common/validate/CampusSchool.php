<?php

namespace app\common\validate;

use \app\common\validate\BaseValidate;

class CampusSchool extends BaseValidate {
    protected $rule =   [
        'name'   => 'require|max:100',
        'logo' => 'number',
        'display' => 'require|in:0,1',
        'district1' => 'require|number',
        'district2' => 'require|number',
        'district3' => 'require|number',
        'level' => 'require|number',
        'type' => 'require|number',
        'introduction' => 'require',
        'address' => 'require|max:200',
        'tel' => 'max:100',
    ];
}
