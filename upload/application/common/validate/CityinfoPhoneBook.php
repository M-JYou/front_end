<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/4/6
 * Time: 11:41
 */

namespace app\common\validate;

class CityinfoPhoneBook extends BaseValidate {
    protected $rule =   [
        'name' => 'require',
        'mobile' => 'require|checkMobile',
        'type_id' => 'number|gt:0',
    ];
    protected $message = [
        'name' => '请填写名称',
        'mobile' => '请输入手机号',
        'type_id' => '请选择分类'
    ];
}
