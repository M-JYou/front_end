<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/5/6
 * Time: 14:59
 */

namespace app\common\model;

class FastJob extends BaseModel {
    public $map_audit = [
        0 => '待审核',
        1 => '已通过',
        2 => '未通过',
    ];
    public $map_top = [0 => '否', 1 => '是'];
    public $map_rec = [0 => '否', 1 => '是'];
    public function setAudit($idarr, $audit, $reason = '') {
        $timestamp = time();
        $this->where('id', 'in', $idarr)->setField('audit', $audit);
        $audit_log = [];
        $list = $this->where('id', 'in', $idarr)->column('*', 'id');
        foreach ($list as $key => $value) {
            $arr['jobid'] = $key;
            $arr['audit'] = $audit;
            $arr['reason'] = $reason;
            $arr['addtime'] = $timestamp;
            $audit_log[] = $arr;
        }
        model('fastJobAuthLog')->saveAll($audit_log);
        return;
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
