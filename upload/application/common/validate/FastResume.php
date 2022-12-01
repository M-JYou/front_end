<?php

namespace app\common\validate;

use \app\common\validate\BaseValidate;

class FastResume extends BaseValidate {
    protected $rule =   [
        'fullname' => 'require|max:15',
        'sex' => 'require|in:1,2',
        'experience' => 'require|number|gt:0',
        'wantjob' => 'max:30',
        'telephone' => 'require|checkMobile',
        'content' => 'require',
        'valid' => 'require|number',
        'adminpwd' => 'require|max:100',
        'addtime' => 'number',
        'refreshtime' => 'number'
    ];
    protected $message = [
        'fullname' => '请填写姓名',
        'sex' => '请选择性别',
        'experience' => '请选择工作年限',
        'wantjob' => '请填写想找工作',
        'telephone' => '请输入联系电话',
        'content' => '请输入具体描述',
        'valid' => '请选择有效期',
        'adminpwd' => '请输入管理密码'
    ];
    protected function checkMobile($value, $rule, $data) {
        if (fieldRegex($value, 'mobile')) {
            return true;
        } else {
            return '请输入正确的手机号码';
        }
    }
}
