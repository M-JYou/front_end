<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/4/6
 * Time: 11:41
 */

namespace app\common\validate;


class FreelanceSubject extends BaseValidate {
    protected $rule =   [
        'title' => 'require|max:30',
        'price' => 'require|number',
        'endtime' => 'require|number',
        'period' =>  'require|number',
        'desc' =>  'require',
        'linkman' =>  'require',
        'mobile' => 'require|checkMobile',
    ];
    protected $message = [
        'title' => '请填写项目名称',
        'title.max' => '项目名称不能超过30个字',
        'price' => '请填写价格',
        'endtime.require' => '请填写截止日期',
        'endtime.number' => '截止日期格式不正确',
        'period' => '请填写预算工期',
        'desc' =>  '请填写项目描述',
        'linkman' => '请填写联系人',
    ];
}
