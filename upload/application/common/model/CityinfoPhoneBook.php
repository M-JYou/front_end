<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/5/6
 * Time: 14:59
 */

namespace app\common\model;

class CityinfoPhoneBook extends BaseModel {
    protected $autoWriteTimestamp = true;
    protected $createTime = 'addtime';
    protected $updateTime = 'updatetime';
    protected $readonly = ['id'];

    public $map_audit = [
        0 => '未审核',
        2 => '审核未通过',
        1 => '已审核'
    ];

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
            $arr['phone_book_id'] = $key;
            $arr['audit'] = $audit;
            $arr['reason'] = $reason;
            $arr['addtime'] = $timestamp;
            $audit_log[] = $arr;
        }
        $logModel = new CityinfoPhoneBookAuditLog();
        $logModel->saveAll($audit_log);
        return;
    }

    public function delAll($ids) {
        $this->where(['id' => ['in', $ids]])->delete();
    }
}
