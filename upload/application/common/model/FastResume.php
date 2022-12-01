<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/5/6
 * Time: 14:59
 */

namespace app\common\model;

class FastResume extends BaseModel {
    public $experience = [
        1 => '应届生',
        2 => '1年',
        3 => '2年',
        4 => '3年',
        5 => '3-5年',
        6 => '5-10年',
        7 => '10年以上',
    ];

    public $map_audit = [
        0 => '待审核',
        1 => '已通过',
        2 => '未通过',
    ];
    public $map_sex = [1 => '男', 2 => '女'];
    public $map_top = [0 => '否', 1 => '是'];
    public $map_rec = [0 => '否', 1 => '是'];

    public function getList($type_id, $audit, $key, $key_type, $page, $pagesize) {
        $where = [];
        if ($type_id > 0) {
            $where['type_id'] = intval($type_id);
        }
        if ($audit >= 0) {
            $where['audit'] = intval($audit);
        }
        if ($key) {
            if ($key_type == 1) {
                $where['name'] = ['like', '%' . $key . '%'];
            } else if ($key_type == 2) {
                $where['mobile'] = $key;
            } else if ($key_type == 3) {
                $where['publish_tel'] = $key;
            }
        }
        $list = $this->where($where)->order('updatetime desc')->limit(($page - 1) * $pagesize, $pagesize)->select();
        if (!empty($list)) {
            (new Uploadfile())->getFileUrlBatch2($list, 'qrcode', 'qrcode_url');
        }
        return [
            'list' => $list,
            'total' => $this->where($where)->count()
        ];
    }


    public function setAudit($idarr, $audit, $reason = '') {
        $timestamp = time();
        $this->where('id', 'in', $idarr)->setField('audit', $audit);
        $audit_log = [];
        $list = $this->where('id', 'in', $idarr)->column('*', 'id');
        foreach ($list as $key => $value) {
            $arr['resumeid'] = $key;
            $arr['audit'] = $audit;
            $arr['reason'] = $reason;
            $arr['addtime'] = $timestamp;
            $audit_log[] = $arr;
        }
        model('fastResumeAuthLog')->saveAll($audit_log);
        return true;
    }
    public function setRecommend($idarr, $recommend) {
        $timestamp = time();
        $this->where('id', 'in', $idarr)->setField('is_recommend', $recommend);
        return true;
    }
    public function setTop($idarr, $top) {
        $timestamp = time();
        $this->where('id', 'in', $idarr)->setField('is_top', $top);
        return true;
    }
    public function setRefresh($idarr) {
        $timestamp = time();
        $this->where('id', 'in', $idarr)->setField('refreshtime', $timestamp);
        return true;
    }
    public function delAll($ids) {
        $this->where(['id' => ['in', $ids]])->delete();
    }
}
