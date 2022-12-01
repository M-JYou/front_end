<?php

namespace app\common\model;

class SceneQrcode extends \app\common\model\BaseModel {
    public $platform_arr = [
        0 => '公众号二维码',
        1 => '触屏二维码',
        2 => '微信小程序码'
    ];
    public $type_arr = [
        'index' => [
            'name' => '首页',
            'alias' => 'index',
            'offiaccount_param_name' => '',
            'miniprogram_page' => 'pages/index/index',
            'miniprogram_param' => '',
            'mobile_page' => ''
        ],
        'reg_personal' => [
            'name' => '求职者注册页',
            'alias' => 'reg_personal',
            'offiaccount_param_name' => '',
            'miniprogram_page' => 'pages/members/register/register',
            'miniprogram_param' => '',
            'mobile_page' => 'member/reg/personal'
        ],
        'reg_company' => [
            'name' => '企业注册页',
            'alias' => 'reg_company',
            'offiaccount_param_name' => '',
            'miniprogram_page' => 'pages/members/register/register',
            'miniprogram_param' => 'utype=1',
            'mobile_page' => 'member/reg/company'
        ],
        'company' => [
            'name' => '公司详情页',
            'alias' => 'company',
            'offiaccount_param_name' => 'comid',
            'miniprogram_page' => 'pages/jobs/company_show/company_show',
            'miniprogram_param' => 'id=:id',
            'mobile_page' => 'company/:id'
        ],
        'job' => [
            'name' => '职位详情页',
            'alias' => 'job',
            'offiaccount_param_name' => 'jobid',
            'miniprogram_page' => 'pages/jobs/jobs_show/jobs_show',
            'miniprogram_param' => 'id=:id',
            'mobile_page' => 'job/:id'
        ],
        'resume' => [
            'name' => '简历详情页',
            'alias' => 'resume',
            'offiaccount_param_name' => 'resumeid',
            'miniprogram_page' => 'pages/resume/show/show',
            'miniprogram_param' => 'id=:id',
            'mobile_page' => 'resume/:id'
        ],
        'notice' => [
            'name' => '公告详情页',
            'alias' => 'notice',
            'offiaccount_param_name' => 'noticeid',
            'miniprogram_page' => 'pages/notice/show/show',
            'miniprogram_param' => 'id=:id',
            'mobile_page' => 'notice/:id'
        ],
        'jobfair' => [
            'name' => '招聘会详情页',
            'alias' => 'jobfair',
            'offiaccount_param_name' => 'jobfairid',
            'miniprogram_page' => '',
            'miniprogram_param' => '',
            'mobile_page' => 'jobfair/:id'
        ],
        'jobfairol' => [
            'name' => '网络招聘会详情页',
            'alias' => 'jobfairol',
            'offiaccount_param_name' => 'jobfairolid',
            'miniprogram_page' => '',
            'miniprogram_param' => '',
            'mobile_page' => 'jobfairol/:id'
        ],
        'news' => [
            'name' => '资讯详情页',
            'alias' => 'news',
            'offiaccount_param_name' => 'newsid',
            'miniprogram_page' => 'pages/newsid/show/show',
            'miniprogram_param' => 'id=:id',
            'mobile_page' => 'news/:id'
        ],
        'live' => [
            'name' => '直播详情',
            'alias' => 'live',
            'offiaccount_param_name' => 'live',
            'miniprogram_page' => '',
            'miniprogram_param' => 'live=:live_id&appkey=:appkey&appsecret=:appsecret',
            'mobile_page' => 'm/#/'
        ],
    ];
}
