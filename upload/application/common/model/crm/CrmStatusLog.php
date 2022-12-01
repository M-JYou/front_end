<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/6/8
 * Time: 16:02
 */

namespace app\common\model\crm;


use app\common\model\BaseModel;

class CrmStatusLog extends BaseModel
{
    public function getList($option, $page, $pageSize){
        $scMap = (new CrmConfig())->sales_consultant();
        $where = $option;
        $count = $this->where($where)->count();
        $list = $this->where($where)->order('id desc')
            ->limit(($page-1)*$pageSize, $pageSize)
            ->select();
        foreach($list as &$v){
            $v['sc_name'] = $scMap[$v['sc_id']];
            $v['addtime_fmt'] = date('Y-m-d H:i:s', $v['addtime']);

        }
        return ['list'=>$list, 'total'=>$count,  'page'=>$page, 'page_size'=>$pageSize];
    }

}
