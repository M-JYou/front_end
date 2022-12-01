<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/6/4
 * Time: 16:42
 */

namespace app\common\model\crm;


use app\common\model\BaseModel;
use Think\Db;

class CrmResume extends BaseModel
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'addtime';
    protected $updateTime = 'updatetime';
    protected $readonly = ['id'];

    public function getList($option, $page, $pageSize){
        $list = Db::table(config('database.prefix').'resume')->alias('a')
            ->join($this->getTable(). ' b', 'a.uid=b.uid', 'left')
            ->join(config('database.prefix').'resume_contact c', 'a.uid=c.uid')
            ->where($option)
            ->field('a.id,a.uid,a.audit,a.display_name,c.mobile,c.email,c.qq,b.sc_id,a.photo_img,a.birthday,a.sex,a.birthday,a.education,a.enter_job_time,a.platform,a.fullname,a.refreshtime,a.remark')
            ->order('a.refreshtime desc')
            ->limit(($page-1)*$pageSize, $pageSize)
            ->select();
        $total = Db::table(config('database.prefix').'resume')->alias('a')
            ->join($this->getTable(). ' b', 'a.uid=b.uid', 'left')
            ->join(config('database.prefix').'resume_contact c', 'a.uid=c.uid')
            ->where($option)
            ->count();
        $this->processResumeList($list);
        return ['list'=>$list, 'total'=>$total,  'page'=>$page, 'page_size'=>$pageSize];
    }

    public function getInfo($uid){
        return Db::table(config('database.prefix').'resume')->alias('a')
            ->join($this->getTable(). ' b', 'a.uid=b.uid', 'left')
            ->join(config('database.prefix').'resume_contact c', 'a.uid=c.uid')
            ->where(['a.uid'=>$uid])
            ->field('a.id,a.uid,a.audit,a.display_name,c.mobile,c.email,c.qq,b.sc_id')
            ->find();
    }

    public function getList2($option, $page, $pageSize){
        $list = $this->alias('a')
            ->join(config('database.prefix').'resume b', 'a.uid=b.uid' )
            ->join(config('database.prefix').'resume_contact c', 'b.uid=c.uid')
            ->where($option)
            ->field('a.sc_id,c.mobile,c.email,c.qq,b.id,b.uid,b.display_name,b.audit,b.photo_img,b.birthday,b.sex,b.birthday,b.education,b.enter_job_time,b.platform,b.fullname,b.refreshtime,a.remark')
            ->order('b.refreshtime desc')
            ->limit(($page-1)*$pageSize, $pageSize)
            ->select();
        $total = $this->alias('a')
            ->join(config('database.prefix').'resume b', 'a.uid=b.uid' )
            ->join(config('database.prefix').'resume_contact c', 'b.uid=c.uid')
            ->where($option)
            ->count();
        $this->processResumeList($list);
        return ['list'=>$list, 'total'=>$total,  'page'=>$page, 'page_size'=>$pageSize];
    }

    public function processResumeList(&$list){
        $ridarr = [];
        $uidarr = [];
        $complete_list = [];
        $photo_arr = $photo_id_arr = [];
        foreach ($list as $key => $value) {
            $ridarr[] = $value['id'];
            $uidarr[] = $value['uid'];
            $value['photo_img'] > 0 && ($photo_id_arr[] = $value['photo_img']);
        }
        if (!empty($photo_id_arr)) {
            $photo_arr = model('Uploadfile')->getFileUrlBatch(
                $photo_id_arr
            );
        }
        if (!empty($ridarr)) {
            $complete_list = model('Resume')->countCompletePercentBatch(
                $ridarr
            );
        }

        foreach ($list as &$value) {
            $value['photo_img_src'] = isset($photo_arr[$value['photo_img']])
                ? $photo_arr[$value['photo_img']]
                : default_empty('photo');
            $value['age'] =
                intval($value['birthday']) == 0
                    ? '年龄未知'
                    : date('Y') - intval($value['birthday']) . '岁';
            $value['sex_cn'] = isset(model('Resume')->map_sex[$value['sex']])
                ? model('Resume')->map_sex[$value['sex']]
                : '性别未知';
            $value['education_cn'] = isset(
                model('BaseModel')->map_education[$value['education']]
            )
                ? model('BaseModel')->map_education[$value['education']]
                : '学历未知';
            $value['experience_cn'] =
                $value['enter_job_time'] == 0
                    ? '无经验'
                    : format_date($value['enter_job_time']);

            $value['complete_percent'] = isset($complete_list[$value['id']])
                ? $complete_list[$value['id']]
                : 0;
            $value['link'] = url('index/resume/show', ['id' => $value['id']]);
            $value['bind_weixin'] = isset($bindarr[$value['uid']])?1:0;
            $value['platform_cn'] = isset(model('BaseModel')->map_platform[$value['platform']])?model('BaseModel')->map_platform[$value['platform']]:'未知平台';
        }
    }

    public function saveData($data){
        if(!isset($data['uid']) || empty($data['uid'])){
            exception('无效的简历');
        }
        $row = $this->where(['uid'=>$data['uid']])->find();
        if($row){
            return $this->save(['sc_id'=>$data['sc_id']], ['id'=>$row['id']]);
        }else{
            return $this->save($data);
        }
    }
}
