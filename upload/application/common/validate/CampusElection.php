<?php

namespace app\common\validate;

use \app\common\validate\BaseValidate;

class CampusElection extends BaseValidate {
    protected $rule =   [
        'school_id' => 'require|number',
        'subject'   => 'require|max:100',
        'display' => 'require|in:0,1',
        'address' => 'require|max:200',
        'starttime' => 'require|number',
        'endtime' => 'require|number',
        'introduction' => 'require',
        'company_count' => 'require|number',
        'graduate_count' => 'require|number',
    ];
}
