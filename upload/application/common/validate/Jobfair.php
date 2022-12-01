<?php

namespace app\common\validate;

use app\common\validate\BaseValidate;

class Jobfair extends BaseValidate {
    protected $rule = [
        'title' => 'require|max:200',
        'holddate_start' => 'require|integer',
        'holddate_end' => 'require|integer',
        'predetermined_start' => 'require|integer',
        'predetermined_end' => 'require|integer',
        'sponsor' => 'require',
        'contact' => 'require|max:100',
        'phone' => 'require|max:100',
        'thumb' => 'integer',
        'address' => 'require|max:200',
        'bus' => 'max:200',
        'map_lat' => 'require|number',
        'map_lng' => 'require|number',
        'map_zoom' => 'require|integer',
        'display' => 'integer|in:0,1',
        'intro_img' => 'integer',
        'ordid' => 'integer',
        'tpl_id' => 'integer'
    ];
    protected $message = [
        'title.require' => '招聘会标题不能为空',
        'title.max' => '招聘会标题应在200个字符内',
        'holddate_start.require' => '请选择举办时间',
        'holddate_end.require' => '请选择举办时间',
        'predetermined_start.require' => '请选择报名时间',
        'predetermined_end.require' => '请选择报名时间',
        'holddate_start.integer' => '请正确选择举办时间',
        'holddate_end.integer' => '请正确选择举办时间',
        'predetermined_start.integer' => '请正确选择报名时间',
        'predetermined_end.integer' => '请正确选择报名时间',
        'sponsor.require' => '招聘会举办方不能为空',
        'contact.require' => '联系人不能为空',
        'phone.require' => '联系电话不能为空',
        'contact.max' => '联系人应在100个字符内',
        'phone.max' => '联系电话应在100个字符内',
        'thumb.integer' => '请正确上传招聘会缩略图',
        'address.require' => '举办地址不能为空',
        'address.max' => '举办地址应在200个字符内',
        'bus.max' => '乘车路线应在200个字符内',
        'map_lat.require' => '请选择招聘会地图标注',
        'map_lng.require' => '请选择招聘会地图标注',
        'map_zoom.require' => '请选择招聘会地图缩放级别',
        'map_lat.number' => '请正确选择招聘会地图标注',
        'map_lng.number' => '请正确选择招聘会地图标注',
        'map_zoom.integer' => '请正确选择招聘会地图缩放级别',
        'display.integer' => '请正确选择招聘会显示状态',
        'display.in' => '请正确选择招聘会显示状态',
        'intro_img.integer' => '请正确上传招聘会简介配图',
        'ordid.integer' => '请正确填写排序值',
        'tpl_id.integer' => '请正确选择招聘会展位模板'
    ];
}
