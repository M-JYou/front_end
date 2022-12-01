<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/6/3
 * Time: 16:52
 */

namespace app\common\model\crm;


use app\common\model\BaseModel;

class CrmGetLog extends BaseModel
{
    const ADD_NEW = 1;
    const TRANSFER = 2;
    const FETCH = 3;
    public $action_type = [
        1 => '新增',
        2 => '转交',
        3 => '领取'
    ];
    public function insertOne($cid, $sc_id, $from_sc_id, $action, $remark, $op_id){
        return $this->insert([
            'cid' => $cid,
            'sc_id' =>$sc_id,
            'from_sc_id' => $from_sc_id,
            'action_type' => $action,
            'remark' => $remark,
            'addtime' => time(),
            'get_time' => time(),
            'op_id' => $op_id
        ]);
    }

    public function getLastLog($cid){
        $row = $this->where(['cid'=>$cid])->find();
        $scMap = (new CrmConfig())->sales_consultant();
        $row['addtime_fmt'] = date('Y-m-d H:i:s', $row['addtime']);
        if($row && isset($row['action_type'])){
            if($row['action_type'] == 3){
                $row['action_fmt'] = sprintf('%s 从 公海 领取', $row['addtime_fmt']);
            }else if($row['action_type'] == 2){
                $row['action_fmt'] = sprintf('%s 由 %s 转交', $row['addtime_fmt'],  $row['from_sc_id']>0 ? $scMap[$row['from_sc_id']]: '公海'  );
            }else if($row['action_type'] == 1){
                $row['action_fmt'] = sprintf('%s 系统 新增', $row['addtime_fmt']);
            }
        }
        return $row;
    }

    public function getList($cid, $page, $pageSize){
        $where = [];
        if($cid){
            $where = ['cid'=>$cid];
        }

        $count = $this->where($where)->count();
        $list = $this->where($where)->order('id desc')
            ->limit(($page-1)*$pageSize, $pageSize)
            ->select();
        return ['list'=>$list, 'total'=>$count,  'page'=>$page, 'page_size'=>$pageSize];
    }

}
