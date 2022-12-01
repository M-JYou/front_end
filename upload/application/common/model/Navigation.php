<?php

namespace app\common\model;

class Navigation extends \app\common\model\BaseModel {
    public $map_page = [
        'index' => '首页',
        'job' => '职位列表页',
        'resume' => '简历列表页',
        'company' => '企业列表页',
        'jobfair' => '招聘会列表页',
        'article' => '资讯列表页',
        'hrtool' => 'Hr工具箱',
        'map' => '地图找工作',
        'help' => '帮助页',
        'jobfairol' => '网络招聘会列表页',
        'campus' => '校园招聘首页',
        'freelance' => '自由职业',
        'fastjob' => '快速招聘',
        'fastresume' => '快速求职',
        'shortvideo' => '视频招聘',
        'job_register' => '求职登记',
        'daily' => '今日招聘',
        'live' => '直播招聘',
        'videoRecruitment' => '视频招聘'
    ];
    protected static function init() {
        self::event('after_write', function () {
            cache('cache_navigation', null);
        });
        self::event('after_delete', function () {
            cache('cache_navigation', null);
        });
    }
    public function getCache() {
        if (false === ($data = cache('cache_navigation'))) {
            $data = $this->order('sort_id desc,id asc')->where('is_display', 1)->column('id,title,link_type,page,url,target');
            cache('cache_navigation', $data);
        }
        return $data;
    }
    public function getList() {
        $list = $this->getCache();
        foreach ($list as $key => $value) {
            $value['url'] = str_replace('{domain}', config('global_config.sitedomain') . config('global_config.sitedir'), $value['url']);
            $list[$key] = $value;
        }
        return $list;
    }
}
