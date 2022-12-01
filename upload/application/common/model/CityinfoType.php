<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/5/6
 * Time: 14:59
 */

namespace app\common\model;


class CityinfoType extends BaseModel {
    protected $autoWriteTimestamp = true;
    protected $createTime = 'addtime';
    protected $updateTime = 'updatetime';
    protected $readonly = ['id'];
    public function getCache() {
        if (false === ($data = cache('cache_city_info_type'))) {
            $list = $this->where('is_display', 1)->order('sort_id desc,id asc')->column(
                'id,pid,title,logo',
                'id'
            );
            $data = [];
            $tmp = [];
            foreach ($list as $key => $value) {
                if ($value['pid']) {
                    if (isset($data[$value['pid']])) {
                        $data[$value['pid']]['children'][] = $value;
                    } else {
                        if (!isset($tmp[$value['pid']])) $tmp[$value['pid']] = [];
                        $tmp[$value['pid']][] = $value;
                    }
                } else {
                    $value['children'] = isset($tmp[$value['id']]) ? $tmp[$value['id']] : [];
                    unset($tmp[$value['id']]);
                    $data[$value['id']] = $value;
                }
            }
            $data = array_values($data);
            (new Uploadfile())->getFileUrlBatch2($data, 'logo', 'logo_url');
            cache('cache_city_info_type', $data);
        }

        return $data;
    }
    public function getNoCache() {
        $list = $this->order('pid asc, sort_id desc,id asc')->column(
            'id,pid,title',
            'id'
        );
        $data = [];
        foreach ($list as $key => $value) {
            $value['value'] = $value['id'];
            $value['label'] = $value['title'];
            if ($value['pid']) {
                $data[$value['pid']]['children'][] = $value;
            } else {
                $value['children'] = [];
                $data[$value['id']] = $value;
            }
        }
        $data = array_values($data);

        return $data;
    }

    public function getPInfoByTypeId($id) {
        if (!$id) return false;
        $c = $this->find($id);
        if (!$c['pid']) return false;
        $p = $this->find($c['pid']);
        $p['pay_for_create'] = json_decode($p['pay_for_create'], 1);
        return $p;
    }

    public function getAll() {
        $list = $this->order('sort_id desc,id asc')->select();
        foreach ($list as &$v) {
            $v['pay_for_create'] = json_decode($v['pay_for_create'], 1);
        }
        return $list;
    }
}
