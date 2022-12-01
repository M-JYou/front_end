<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/4/6
 * Time: 11:41
 */

namespace app\common\validate;



class FreelanceProject extends BaseValidate {
    protected $rule =   [
        'startdate' =>  'require|number',
        'enddate' => 'require|number',
        'name' => 'require',
        'description' =>  'require',
        'role' =>  'require',
    ];

    protected $message = [
        'startdate' =>  '开始日期不能为空',
        'enddate' => '结束日期不能为空',
        'name' => '项目名称不能为空',
        'description' =>  '项目描述不能为空',
        'role' =>  '角色不能为空',
    ];
}
