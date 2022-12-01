<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/6/1
 * Time: 14:50
 */

namespace app\common\model\crm;


use app\common\model\BaseModel;

class CrmLinkman extends BaseModel
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'addtime';
    protected $updateTime = 'updatetime';
    protected $readonly = ['id'];

    public function checkMobile($value)
    {
        if (fieldRegex($value, 'mobile')) {
            $info = $this
                ->where([
                    'mobile' => $value,
                ])
                ->find();
            if (null === $info) {
                return true;
            } else {
                $cc = new CrmCustomer();
                $info['com'] = $cc->where(['id'=>$info['cid']])->find();
                return $info;
            }
        } else {
            return '请输入正确的手机号码';
        }
    }

    public function linkman_list($cid){
        $cc = new CrmCustomer();
        $row = $cc->find($cid);
        if(!$row){
            return [];
        }
        $com_contact = false;
        if($row['comid']){
            $com_contact = $cc->get_com_contact($row['comid']);
        }
        $res = $this->where(['cid'=>$cid])->select();
        if(!$res)$res = [];
        if($row['master_linkman']){
            foreach($res as &$v){
                if($v['id'] == $row['master_linkman'])$v['is_master'] = 1;
            }
        }
        if($com_contact){
            $com_contact['is_sys'] = 1;
            array_unshift($res, $com_contact);
        }
        return $res;
    }

    public function saveData(){
        $id = input('post.id/d', 0, 'intval');
        $data = [
            'cid' =>  input('post.cid/d', 0, 'intval'),
            'name' => input('post.name/s', '', 'trim,htmlspecialchars'),
            'gender' =>  input('post.gender/d', 0, 'trim,intval'),
            'position' => input('post.position/s', '', 'trim,htmlspecialchars'),
            'appellation' => input('post.appellation/s', '', 'trim,htmlspecialchars'),
            'mobile' =>  input('post.mobile/d', 0, 'trim,intval'),
            'telephone' => input('post.telephone/s', '', 'trim,htmlspecialchars'),
            'email' =>  input('post.email/s', '', 'trim,htmlspecialchars'),
            'qq' => input('post.qq/d', 0, 'intval'),
        ];
        if(!$data['cid'] ){
            exception('请指定客户id');
        }
        if(!$data['mobile'] && !$data['telephone'])exception('手机号与座机号不能都为空');
        if(!$data['name'] || !$data['gender'])exception('姓名与性别必填');
        if($data['mobile']){
            $row = $this->where(['mobile'=>$data['mobile']])->find();
            if($row){
                if($row['id'] != $id){
                    exception('手机号重复');
                }
            }
        }
        if($data['telephone']){
            $row = $this->where(['telephone'=>$data['telephone']])->find();
            if($row){
                if($row['id'] != $id){
                    exception('座机号重复');
                }
            }
        }

        if($id){
            return $this->save($data, ['id'=>$id]);
        }else{
            return $this->save($data);
        }
    }

    public function set_master_linkman($cid, $linkman_id){
        $row = $this->find($linkman_id);
        if($row['cid'] != $cid){
            exception('参数非法');
        }
        $cc = new CrmCustomer();
        return $cc->save(['master_linkman'=>$linkman_id], ['id'=>$cid]);
    }

    public function del_linkman($linkman_id){
        $row = $this->find($linkman_id);
        $cc = new CrmCustomer();
        $cus = $cc->find($row['cid']);
        if($cus['master_linkman'] == $linkman_id){
            exception('主要联系人不可以删除');
            //$cc->save(['master_linkman'=>0], ['id'=>$cus['id']]);
        }
        return $this->where(['id'=>$linkman_id])->delete();
    }
}
