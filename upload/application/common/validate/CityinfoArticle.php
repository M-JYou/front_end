<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/5/7
 * Time: 9:24
 */

namespace app\common\validate;


class CityinfoArticle extends BaseValidate {
    protected $rule =   [

        'desc' => 'require',
        'linkman' => 'require',
        'mobile' => 'require',
    ];
    protected $message = [

        'desc' => '内容不能为空',
        'linkman' => '联系人不能为空',
        'mobile'    => '请输入手机号',
    ];
}
