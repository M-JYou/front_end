<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/6/2
 * Time: 18:00
 */

namespace app\common\model\crm;


use app\common\model\BaseModel;
use Think\Db;

class CrmVisitLog extends BaseModel
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'addtime';
    protected $updateTime = 'updatetime';
    protected $readonly = ['id'];
    public $visit_status = [
        1 => '当面拜访',
        2 => '电话拜访',
        3 => '网络拜访'
    ];

    public function big(){
        return $this->hasOne('CrmVisitBigLog', 'visit_id', 'big_id')->bind([
            'big_result'=> 'result']);
    }
    public function linkmaninfo(){
        return $this->hasOne('CrmLinkman', 'id', 'linkman')->field('name as linkman_name,mobile,id');
    }
    public function customer(){
        return $this->hasOne('CrmCustomer', 'id', 'cid')->field('id,title,status,company_name,sales_consultant');
    }
    public function saveData($status, $data, $id, $admininfo){
        if(!$data['result'])exception('参数不完整');
        $cc = new CrmCustomer();
        $big = new CrmVisitBigLog();
        $cur = $cc->find($data['cid']);
        // if(isset($cur['status']) && ($cur['status'] == CrmCustomer::STATUS_COMPLETE) && ($status != $cur['status']))exception('已成交状态不可修改');
        $result = $data['result'];
        $has_big_id = false;
        if($id){
            $row = $this->find($id);
            $result = $row['result'];
            if($row['big_id']>0){
                $has_big_id = true;
                $data['big_id'] = $row['big_id'];
                $bigRow = $big->find($row['big_id']);
                $result = $bigRow['result'];
            }
            $result .= sprintf('<p>--------------------------------------------------<p/><p>%s  <span style="color:#6E86B1">%s</span> 追加跟进</p><p>%s</p>',
                date('Y-m-d H:i:s'), $admininfo->username, $data['result']);
        }
        if(mb_strlen($result)>=100){
            $data['result'] = '';
            if($has_big_id){
                $big->save(['result'=>$result], ['visit_id'=>$row['big_id']]);
            }else{
                $data['big_id'] = $big->insert(['result'=>$result], false, true);
            }
        }
        if($id){//追加
            $this->save(['result'=>$data['result'], 'big_id'=>isset($data['big_id'])? $data['big_id']:0], ['id'=>$id]);
        }else{
            $this->save($data);
            $id = $this->getLastInsID();
        }
        if($status != $cur['status']){
            $log = new CrmStatusLog();
            $log->save([
                'cid' => $data['cid'],
                'from_status' => $cur['status'],
                'status' => $status,
                'sc_id' => $admininfo->id,
                'addtime' => time()
            ]);
        }
        return $cc->save(['last_visit_id'=>$id,'last_visit_time'=>time(), 'status'=>$status], ['id'=>$data['cid']]);
    }

    public function getOne($id){
        $big = new CrmVisitBigLog();
        $row = $this->find($id);
        if($row['big_id']){
            $row['result'] = $big->where('visit_id', $row['id'])->value('result');
        }
        return $row;
    }

    public function getList($options, $hasWhere, $page, $pageSize){
        $with = 'big,linkmaninfo,customer';
        $count = $this->hasWhere('customer', $hasWhere)->where($options)->with($with)->count();
        $list = $this->hasWhere('customer', $hasWhere)->where($options)->with($with)->order('id desc')
            ->limit(($page-1)*$pageSize, $pageSize)
            ->select();
        foreach ($list as $key=>$value){
            $list[$key]['result'] = htmlspecialchars_decode($value['result'],ENT_QUOTES);
            $list[$key]['big_result'] = htmlspecialchars_decode($value['big_result'],ENT_QUOTES);
        }
        return ['list'=>$list, 'total'=>$count,  'page'=>$page, 'page_size'=>$pageSize];
    }
}
