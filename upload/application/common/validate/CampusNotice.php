<?php

namespace app\common\validate;

use \app\common\validate\BaseValidate;

class CampusNotice extends BaseValidate {
    protected $rule =   [
        'title'   => 'require|max:100',
        'content' => 'require',
        'is_display' => 'require|in:0,1',
        'link_url' => 'max:200',
        'seo_keywords' => 'max:100',
        'seo_description' => 'max:200',
        'addtime' => 'require|number',
        'holddate_start' => 'require|number',
        'holddate_end' => 'require|number',
        'click' => 'number',
        'sort_id' => 'number'
    ];
}
