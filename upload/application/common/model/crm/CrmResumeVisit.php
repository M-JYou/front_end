<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/6/4
 * Time: 16:41
 */

namespace app\common\model\crm;


use app\common\model\BaseModel;

class CrmResumeVisit extends BaseModel
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
        return $this->hasOne(CrmResumeVisitBig::class, 'visit_id', 'big_id')->bind([
            'big_result'=> 'result']);
    }
    public function saveData($data, $id, $admininfo){
        $cc = new CrmResume();
        $big = new CrmResumeVisitBig();
        $result = $data['result'];
        $row = $this->find($id);
        if($id){
            $result = $row['result'];
            $result .= sprintf('<p>--------------------------------------------------<p/><p>%s  <span style="color:#6E86B1">%s</span> 追加跟进</p><p>%s</p>',
                date('Y-m-d H:i:s'), $admininfo->username, $data['result']);
        }
        if(mb_strlen($result)>=100){
            $data['result'] = '';
            if(isset($row['big_id'])){
                $data['big_id'] = $row['big_id'];
                $big->save(['result'=>$result], ['visit_id'=>$row['big_id']]);
            }else{
                $data['big_id'] = $big->insert(['result'=>$result], false, true);
            }
        }
        if($id){//追加
            $this->save(['result'=>$data['result'], 'big_id'=>isset($data['big_id'])? $data['big_id']:0], ['id'=>$id]);
        }else{
            $data['addtime'] = time();
            $data['updatetime'] = time();
            $id =  $this->insert($data, false, true);
        }
        return $cc->save(['last_visit_id'=>$id,'last_visit_time'=>time()], ['uid'=>$data['uid']]);
    }

    public function getOne($id){
        $big = new CrmVisitBigLog();
        $row = $this->find($id);
        if($row['big_id']){
            $row['result'] = $big->where('visit_id', $row['id'])->value('result');
        }
        return $row;
    }

    public function getList($option, $page, $pageSize){
        $where = $option;
        $count = $this->where($where)->count();
        $list = $this->where($where)->with('big')->order('id desc')
            ->limit(($page-1)*$pageSize, $pageSize)
            ->select();
        return ['list'=>$list, 'total'=>$count,  'page'=>$page, 'page_size'=>$pageSize];
    }
}
