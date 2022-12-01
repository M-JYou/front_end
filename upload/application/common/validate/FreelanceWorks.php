<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/4/6
 * Time: 11:41
 */

namespace app\common\validate;


class FreelanceWorks extends BaseValidate {
    protected $rule =   [
        'title' => 'require|max:30',
        'img' => 'require|number',
    ];
    protected $message = [
        'title.require' => '作品名称不能为空',
        'title.max' => '作品名称长度不可超过30个字',
        'img' => '作品图片不能为空',
    ];
}
