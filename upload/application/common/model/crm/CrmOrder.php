<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/6/3
 * Time: 18:20
 */

namespace app\common\model\crm;


use app\common\model\BaseModel;
use Think\Db;

class CrmOrder extends BaseModel
{
    public function saveData($data, $id=0){
        if($id){
            return $this->save($data, ['id'=>$id]);
        }else{
            if(isset($data['sys_order_id']) && $data['sys_order_id']){
                $row = $this->where(['sys_order_id'=>$data['sys_order_id']])->find();
                if($row){
                    exception('订单'.$data['sys_order_id'].'已实收,请不要重复实收');
                }
            }
            if(!$data['paytype'] || $data['amount']<0 || !$data['ordertype'] || (!$data['cid'] && !$data['crm_title'] ))exception('参数不完整');
            $pk = $this->insert($data, false, true);
            return $pk;
        }
    }

    public function getTotalOrder($scId){
        $where['paytime'] = ['gt', strtotime(date('Y-m'))];
        $where[] = ['EXP', Db::raw(sprintf('uid in (select uid from %s where uid>0 %s )', config('database.prefix').'crm_customer', $scId?" and sales_consultant=$scId ":''))];
        $total = Db::table(config('database.prefix'). 'order')->where($where)->count();
        return $total;
    }

    public function totalPermToday($scId){
        $where = [];
        $where['addtime'] = ['gt', strtotime(date('Y-m'))];
        if($scId)$where['sc_id'] = $scId;
        return $this->where($where)->sum('amount') / 100;
    }

    public function getSysList($option, $page, $pageSize){
        $where = $option;
        $where['a.utype'] = 1;
        if(!isset($where['a.paytime'])){
            $where['a.paytime'] = ['gt', 0];
        }

        $where['d.uid'] = ['gt', 0];
        $where[] = ['EXP', Db::raw(sprintf('a.oid not in (select sys_order_id from %s)', $this->getTable()))];
        $total = Db::table(config('database.prefix'). 'order')->alias('a')
            ->join(sprintf('%s d', config('database.prefix').'crm_customer'), 'd.uid=a.uid')
            ->where($where)->count();
        $list = Db::table(config('database.prefix'). 'order')->alias('a')
            ->join(sprintf('%s c', config('database.prefix'). 'company'), 'a.uid=c.uid')
            ->join(sprintf('%s d', config('database.prefix').'crm_customer'), 'd.uid=a.uid')
            ->where($where)->order('a.id desc')
            ->limit(($page-1)*$pageSize, $pageSize)
            ->field('a.id,a.oid,a.amount,a.service_name,a.paytime,a.addtime,a.payment as paytype,a.service_type as ordertype,a.note as remark,a.status,c.id as comid,c.companyname,d.id as cid')
            ->select();

        return ['list'=>$list, 'total'=>$total, 'page'=>$page, 'page_size'=> $pageSize];
    }

    public function getList($option, $page, $pageSize){
        $where = $option;

        $total = $this->where($where)->count();
        $list = $this->with('customer')
            ->where($where)->order('id desc')
            ->limit(($page-1)*$pageSize, $pageSize)
            ->field('id,sys_order_id,amount,cid,paytype,paytime,ordertype,remark,status,sc_id,crm_title')
            ->order('id desc')
            ->select();
        foreach($list as &$l){
            $l['amount'] /= 100;
            if(!$l['title'])$l['title'] = $l['crm_title'];
        }

        return ['list'=>$list, 'total'=>$total,  'page'=>$page, 'page_size'=>$pageSize];
    }

    public function getSysOrderInfo($oid){
        $info = Db::table(config('database.prefix'). 'order')->alias('a')
            ->join(sprintf('%s b', config('database.prefix'). 'crm_customer'), 'a.uid=b.uid', 'left')
            ->join(sprintf('%s c', config('database.prefix'). 'company'), 'a.uid=c.uid', 'left')
            ->where(['oid'=>$oid])
            ->field('a.id,a.oid,a.amount,a.service_name,a.paytime,c.id as comid,c.companyname,b.cid,b.title,b.sc_id')
            ->order('a.id desc')
            ->find();
        return $info;
    }

    public function customer(){
        return $this->hasOne('CrmCustomer', 'id', 'cid')->bind('company_name,title,comid');
    }

    public function change_sc($orderid, $cid, $tosc){
        return $this->save(['sc_id'=>$tosc], ['cid'=>$cid, 'id'=>$orderid]);
    }

    public function confirm($orderid){
        $orderid = array_map('intval', $orderid);
        return $this->save(['status'=>1], [ 'id'=>['in', $orderid]]);
    }

    public function remark($orderid, $remark){
        return $this->save(['remark'=>$remark], ['id'=>$orderid]);
    }

}
