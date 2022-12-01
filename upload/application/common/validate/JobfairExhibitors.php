<?php

namespace app\common\validate;

use app\common\validate\BaseValidate;

class JobfairExhibitors extends BaseValidate {
    protected $rule = [
        'audit' => 'integer|in:1,2,3',
        'etype' => 'integer|in:1,2,3',
        'recommend' => 'integer|in:0,1',
        'note' => 'max:200'
    ];
    protected $message = [
        'audit.integer' => '请正确选择参会企业预定状态',
        'audit.in' => '请正确选择参会企业预定状态',
        'etype.integer' => '请正确选择参会企业预定方式',
        'etype.in' => '请正确选择参会企业预定方式',
        'recommend.integer' => '请正确选择是否推荐名企',
        'recommend.in' => '请正确选择是否推荐名企',
        'note.integer' => '参会企业备注应在200个字符内'
    ];
    protected $scene = [
        'edit'  =>  ['audit', 'etype', 'recommend', 'note']
    ];
}
