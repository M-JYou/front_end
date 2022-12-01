<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/4/6
 * Time: 11:41
 */

namespace app\common\validate;


class FreelanceService extends BaseValidate {
    protected $rule =   [
        'title' => 'require|max:30',
        'price' => 'require|number',
    ];
    protected $message = [
        'title.require' => '服务名称不能为空',
        'title.max' => '服务名称长度不可超过30个字',
        'price' => '服务价格格式不正确',
    ];
}
