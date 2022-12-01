<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/5/31
 * Time: 10:03
 */

namespace app\common\model\crm;


use app\common\model\BaseModel;
use Think\Db;

class CrmConfig extends BaseModel
{
    protected $cache_key = 'crm_config';
    public function saveData($id, $category){
        $name = input('post.name/s', '', 'trim');
        $param =  input('post.param/a', []);
        $remark =  input('post.remark/s', '', 'trim,htmlspecialchars');
        $sort_id = input('post.sort_id/d', 0, 'intval');
        cache($this->cache_key, null);
        if(input('post.is_del', 0, 'intval')){
            if(!$id){
                exception('数据不完整');
            }
            return $this->where(['id'=>$id])->delete();
        }
        if(!$category || !$name){
            exception('数据不完整');
        }
        $data = ['category'=>$category, 'name'=>$name, 'param'=>json_encode($param), 'remark'=>$remark, 'sort_id'=>$sort_id];
        if($id){
            return $this->save($data, ['id'=>$id]);
        }else{
            return $this->insert($data, false, true);
        }
    }

    public function saveAll2($data){
        $data2 = [];
        if($data){
            foreach($data as $v){
                if(isset($v['id']) && isset($v['name'])){
                    $data2[] = [
                        'id' => $v['id'],
                        'name' => $v['name']
                    ];
                }
            }
        }
        if(empty($data2))exception('参数错误');
        cache($this->cache_key, null);
        return $this->saveAll($data2);
    }

    public function sales_consultant($admininfo = null){
        static $res;
        if(!$res){
            if (!is_null($admininfo))
            {
                $access = model('AdminRole')->where(['id'=>$admininfo->role_id])->value('access');
                if ($access != 'all')
                {
                    return array($admininfo->username);
                }
            }
            $res = Db::table(config('database.prefix').'admin')->where('is_sc', 1)->column('id,username');
        }
        return $res;
    }

    public function getData2($category, $id=0){
        $allCache = $this->getAll();
        if($id || $category){
            $arr = [];
            foreach($allCache as $v){
                if($v['category'] == $category || $v['id']==$id){
                    $v['param'] = json_decode($v['param'], 1)?:$v['param'];
                    $arr[] = $v;
                }
            }
            return $arr;
        }
        return $allCache;
    }

    public function getRemainDays(){
        $row = $this->getData2('remained_days');
        return $row[0]['name'];
    }

    public function getCustomerLimit(){
        $row = $this->getData2('customer_limit');
        return $row[0]['name'];
    }

    protected function getAll(){
        $c = cache($this->cache_key);
        if(!$c){
            $c = $this->select();
            cache($this->cache_key, $c, 86400);
        }
        return $c;
    }
}
