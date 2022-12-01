<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/6/3
 * Time: 14:34
 */

namespace app\common\model\crm;


use app\common\model\BaseModel;

class CrmReserveVisit extends BaseModel
{
    public function saveData($data, $id){
        $cc = new CrmCustomer();
        if($id){
            return $this->save(['content'=>$data['content'], 'pre_time'=>$data['pre_time']], ['id'=>$id]);
        }else{
            $pk = $this->insert($data, false, true);
            return $cc->save(['last_pre_visit_id'=>$pk], ['id'=>$data['cid']]);
        }
    }

    public function getList($cid, $page, $pageSize){
        $where = ['cid'=>$cid];
        $count = $this->where($where)->count();
        $list = $this->where($where)->order('id desc')
            ->limit(($page-1)*$pageSize, $pageSize)
            ->select();
        return ['list'=>$list, 'total'=>$count,  'page'=>$page, 'page_size'=>$pageSize];
    }

    public function setLastReserve($cid){
        $last = $this->where(['cid'=>$cid, 'visit_time'=>0])->find();
        $cc = new CrmCustomer();
        $cc->save(['last_pre_visit_id'=>$last?$last['id']:0], ['id'=>$cid]);
    }
}
