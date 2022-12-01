<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/5/8
 * Time: 14:02
 */

namespace app\common\validate;


use think\Validate;

class CityinfoPhoneBookType extends Validate {
    protected $rule =   [
        'title' => 'require',
    ];
    protected $message = [
        'title' => '请填写名称',
    ];
}
