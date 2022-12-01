<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/4/7
 * Time: 8:50
 */

namespace app\common\validate;


class FreelanceSkill extends BaseValidate {
    protected $rule =   [
        'resume_id' => 'require|number',
        'skill_id' => 'require|number',
        'level' => 'require|number',
    ];
    protected $message = [
        'skill_id' => '技能类型不能为空',
        'level' => '熟练度不能为空',
        'resume_id' => '简历不能为空'
    ];
}
