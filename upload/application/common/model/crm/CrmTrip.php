<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/6/4
 * Time: 11:36
 */

namespace app\common\model\crm;


use app\common\model\BaseModel;

class CrmTrip extends BaseModel
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'addtime';
    protected $updateTime = 'updatetime';
    protected $readonly = ['id'];

    public function saveData($data, $id=0){
        if($id){
            return $this->save($data, ['id'=> $id]);
        }else{
            return $this->save($data);
        }
    }

    public function getList($option, $page, $pageSize){
        $where = $option;
        $count = $this->where($where)->count();
        $list = $this->where($where)->order('id desc')
            ->limit(($page-1)*$pageSize, $pageSize)
            ->select();
        foreach($list as &$v){
            $v['out_time'] = $v['out_time']? date('Y-m-d H:i:s', $v['out_time']): '';
            $v['back_time'] = $v['back_time']? date('Y-m-d H:i:s', $v['back_time']): '';
        }
        return ['list'=>$list, 'total'=>$count, 'page'=>$page, 'page_size'=>$pageSize];
    }

    public function getInfo($id){
        return $this->find($id);
    }

}
