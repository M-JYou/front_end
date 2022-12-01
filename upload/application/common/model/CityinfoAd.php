<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/4/26
 * Time: 10:55
 */

namespace app\common\model;

class CityinfoAd extends BaseModel {
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
        ['value' => 'infoshow', 'label' => '信息详情页'],
    ];

    public function process(&$target) {
        $up = new Uploadfile();
        $field = 'imageid';
        $newField = 'imgurl';

        $id_arr = [];
        if (!empty($target) && is_array($target)) {
            foreach ($target as &$v) {
                if (isset($v[$field]) && $v[$field] > 0) {
                    $id_arr[] = intval($v[$field]);
                }
                $v['web_link_url'] = $this->handlerWebLink($v);
            }
        }
        if (!empty($id_arr)) {
            $file_arr = $up->where(['id' => ['in', $id_arr]])->column('id,save_path,platform');
            foreach ($target as &$v) {
                if (isset($v[$field]) && $v[$field] > 0 && isset($file_arr[$v[$field]])) {
                    $vv = $file_arr[$v[$field]];
                    $v[$newField ?: $field] = make_file_url($vv['save_path'], $vv['platform']);
                }
            }
        }
        return $target;
    }

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
                    $path = 'cityinfo';
                    break;
                case 'infoshow':
                    $path = 'cityinfo/info/';
                    break;
                default:
                    $path = '';
                    break;
            }
            if ($path != '') {
                if ($item['inner_link_params'] > 0) {
                    $path = $path . $item['inner_link_params'];
                }
                return $domain . $path;
            } else {
                return '';
            }
        }
    }
}
