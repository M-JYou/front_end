<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/4/6
 * Time: 11:41
 */

namespace app\common\validate;

class FreelanceResume extends BaseValidate {
    protected $rule =   [
        'name' => 'require',
        'gender' => 'require|in:1,2',
        'education' => 'require|number',
        'start_work_date' =>  'require',
        'mobile' => 'require',
        'professional_title' => 'require|max:100',
        'brief_intro' => 'require|max:100'
    ];
    protected $message = [
        'name' => '请填写姓名',
        'gender.require' => '性别不能为空',
        'gender.between' => '性别填写有误',
        'education' => '请填写学历',
        'mobile'    => '请输入手机号',
        'start_work_date' =>  '请填写开始工作时间',
        'professional_title' => '请填写职称',
        'brief_intro.require' => '请填写个人介绍',
        'brief_intro.max' => '个人介绍不可超过100字'
    ];
}
