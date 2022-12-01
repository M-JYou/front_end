<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/4/26
 * Time: 10:55
 */

namespace app\common\model;

class FreelanceAd extends BaseModel {
    protected $readonly = ['id', 'addtime'];
    protected $type = [
        'id' => 'integer',
        'cid' => 'integer',
        'is_display' => 'integer',
        'click' => 'integer',
        'addtime' => 'integer',
        'sort_id' => 'integer',
        'starttime' => 'integer',
        'deadline' => 'integer',
        'uid' => 'integer',
        'imageid' => 'integer'
    ];
    protected $insert = ['addtime'];
    protected function setAddtimeAttr() {
        return time();
    }
    public $innerLinks = [
        ['value' => 'index', 'label' => '首页'],
        ['value' => 'subjectlist', 'label' => '项目列表页'],
        ['value' => 'subjectshow', 'label' => '项目详情页'],
        ['value' => 'resumelist', 'label' => '简历列表页'],
        ['value' => 'resumeshow', 'label' => '简历详情页'],
    ];
    /** 处理链接 */
    public function handlerWebLink($item, $domain = '') {
        if ($item['link_url'] != '') {
            return $item['link_url'];
        } else if ($item['company_id'] > 0) {
            return $domain . url('index/company/show', ['id' => $item['company_id']]);
        } else if ($item['inner_link'] != '') {
            $path = '';
            switch ($item['inner_link']) {
                case 'index':
                    $path = 'freelance';
                    break;
                case 'subjectlist':
                    $path = 'freelance/subject';
                    break;
                case 'subjectshow':
                    $path = 'freelance/subject/';
                    break;
                case 'resumelist':
                    $path = 'freelance/resume';
                    break;
                case 'resumeshow':
                    $path = 'freelance/resume/';
                    break;
                default:
                    $path = '';
                    break;
            }
            if ($path != '') {
                if ($item['inner_link_params'] > 0) {
                    $path = $path . $item['inner_link_params'] . '.html';
                } else {
                    $path = $path;
                }
                return $domain . $path;
            } else {
                return '';
            }
        }
    }
}
