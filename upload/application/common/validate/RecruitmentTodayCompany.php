<?php

namespace app\common\validate;

use \app\common\validate\BaseValidate;

class RecruitmentTodayCompany extends BaseValidate {
    protected $rule =   [
        'recruitment_today_id'  => 'require|number|max:10',
        'company_id' => 'require|number|max:10',
        'sort_id' => 'require|number|max:10',
    ];
}
