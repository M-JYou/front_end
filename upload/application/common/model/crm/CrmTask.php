<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/6/4
 * Time: 11:07
 */

namespace app\common\model\crm;


use app\common\model\BaseModel;

class CrmTask extends BaseModel
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'addtime';
    protected $updateTime = 'updatetime';
    protected $readonly = ['id'];

    public function saveData($data, $id=0){
        if($id){
            unset($data['create_sc_id']);
            return $this->save($data, ['id'=> $id]);
        }else{
            if(empty($data['title']) || empty($data['type']))exception('参数不完整');
            return $this->save($data);
        }
    }

    public function getList($option, $page, $pageSize){
        $where = $option;
        $count = $this->where($where)->count();
        $list = $this->where($where)->order('id desc')
            ->limit(($page-1)*$pageSize, $pageSize)
            ->select();
        return ['list'=>$list, 'total'=>$count,  'page'=>$page, 'page_size'=>$pageSize];
    }

    public function change_sc($id, $tosc){
        return $this->save(['resolve_sc_id'=>$tosc], [ 'id'=>$id]);
    }

    public function complete($id, $result){
        return $this->save(['result'=>$result, 'complete_time'=>time()], [ 'id'=>$id]);
    }

}
