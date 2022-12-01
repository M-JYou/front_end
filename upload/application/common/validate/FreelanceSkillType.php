<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/4/9
 * Time: 17:05
 */

namespace app\common\validate;


class FreelanceSkillType extends BaseValidate {
    protected $rule =   [
        'title' => 'require',
        'sort_id' => 'require|number'
    ];
    protected $message = [
        'title' => '技能类型不能为空',
        'sort_id.require' => '排序不能为空',
        'sort_id.number' => '排序需要是数字'
    ];
}
