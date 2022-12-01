<?php

namespace app\common\validate;

use \app\common\validate\BaseValidate;

class FastJob extends BaseValidate {
    protected $rule =   [
        'jobname' => 'require|max:100',
        'comname' => 'require|max:100',
        'contact' => 'require',
        'telephone' => 'require|checkMobile',
        'address' => 'require',
        'content' => 'require',
        'valid' => 'require|number',
        'adminpwd' => 'require|max:100',
        'addtime' => 'number',
        'refreshtime' => 'number'
    ];
    protected $message = [
        'jobname' => '请填写我想招聘',
        'comname' => '请填写店面名称',
        'contact' => '请填写联系人',
        'telephone' => '请输入联系电话',
        'address' => '请输入联系地址',
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
