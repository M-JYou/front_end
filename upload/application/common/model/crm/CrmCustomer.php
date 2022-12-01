<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/6/1
 * Time: 9:12
 */

namespace app\common\model\crm;


use app\common\model\BaseModel;
use app\common\model\Company;
use app\common\model\CompanyAuth;
use app\common\model\CustomerService;
use app\common\model\Member;
use app\common\model\MemberBind;
use app\common\model\MemberSetmeal;
use app\common\model\Uploadfile;
use Think\Db;

class CrmCustomer extends BaseModel
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'addtime';
    protected $updateTime = 'updatetime';
    protected $readonly = ['id'];

    const STATUS_COMPLETE = 1;//已成交
    public $order_map_payment = [
        'free' => '免费开通',
        'wxpay' => '微信支付',
        'alipay' => '支付宝',
        'coupon' => '优惠券兑换',
        'points' => '积分兑换',
        'backend' => '后台开通',
    ];
    public $order_map_service_type_company = [
        'setmeal' => '开通套餐',
        'points' => '充值积分',
        'jobstick' => '职位置顶',
        'emergency' => '职位紧急',
        'im' => '职聊增值包',
        'resume_package' => '简历增值包',
        'refresh_job_package' => '职位智能刷新',
        'single_job_refresh' => '快捷支付-刷新职位',
        'single_resume_down' => '快捷支付-下载简历',
    ];

    public function remark($cid, $remark){
        if(!$cid || !$remark)exception('参数不完整');
        return $this->save(['remark'=>$remark], ['id'=>$cid]);
    }

    public function checkTimeoutedBind(){
        $config = new CrmConfig();
        $getLog = new CrmGetLog();
        $remainDays = $config->getRemainDays();
        $list = $this->where(['sales_consultant'=>['gt', 0], 'last_visit_time'=>['lt', time()-86400*$remainDays], 'status'=>['neq', self::STATUS_COMPLETE]])->select();
        foreach($list as $v){
            if($v['bind_change_time']>(time()-86400*$remainDays))continue;
            $this->save(['sales_consultant'=>0, 'bind_change_time'=>time()], ['id'=>$v['id']]);
            $getLog->insertOne($v['id'], 0, $v['sales_consultant'], CrmGetLog::TRANSFER, '达到到期未续费和未跟进条件，掉公客',1 );
        }
    }

    public function perm_top($start){
        $m = new CrmOrder();
        $res = $m->where(['addtime'=>['gt', $start], 'status'=>1])->group('sc_id')->field('sum(amount) as sa,sc_id')->order('sa desc,sc_id asc')->select();
        foreach($res as &$v){
            $v['sa'] /= 100;
        }
        return $res;
    }

    public function new_customer_top($start){
        $res = $this->where(['bind_change_time'=>['gt', $start],'sales_consultant'=>['gt', 0] ])->group('sales_consultant')->field('count(*) as c,sales_consultant')->order('c desc')->select();
        return $res;
    }

    public function edit_title($id, $title){
        if(empty($title)){
            exception('公司名称不能为空');
        }
        return $this->save(['title'=>$title], ['id'=>$id]);
    }
    public function edit_addr($id, $addr){
        if(empty($addr)){
            exception('地址不能为空');
        }
        return $this->save(['com_addr'=>$addr], ['id'=>$id]);
    }

    public function renew($id){
        return $this->save(['deletetime'=>0], ['id'=>$id]);
    }

    public function getConsumeLog($cid, $type, $page, $pageSize){
        $row = $this->find($cid);
        if(!$row || !isset($row['uid']))return ['list'=>[], 'total'=>0];
        $uid = $row['uid'];
        $where = ['uid'=>$uid];
        if(!$type){
            $total = Db::table(config('database.prefix').'member_points_log')->where($where)->count();
            $list = Db::table(config('database.prefix').'member_points_log')->where($where)
                ->order('id desc')->limit(($page-1)*$pageSize, $pageSize)->select();
        }else{
            $where['content'] = ['like', '下载简历-%'];
            $total = Db::table(config('database.prefix').'member_setmeal_log')->where($where)->count();
            $list = Db::table(config('database.prefix').'member_setmeal_log')->where($where)
                ->order('id desc')->limit(($page-1)*$pageSize, $pageSize)->select();
        }
        return ['list'=>$list, 'total'=>$total];
    }

    public function getSysOrder($paytype, $serviceType, $cid, $page, $pageSize){
        $row = $this->find($cid);
        if(!$row || !isset($row['uid']))return ['list'=>[], 'total'=>0];
        $uid = $row['uid'];
        $where = ['uid'=>$uid, 'utype'=>1, 'paytime'=>['gt', 0]];

        if($paytype){
            $where['payment'] = $paytype;
        }
        if($serviceType){
            $where['service_type'] = $serviceType;
        }

        $total = Db::table(config('database.prefix').'order')->where($where)->count();
        $list = Db::table(config('database.prefix').'order')
            ->where($where)
            ->limit(($page-1)*$pageSize, $pageSize)
            ->field('id,oid,service_type,service_name,amount,payment,paytime,addtime')
            ->select();
        if($list){
            foreach($list as &$v){
                $v['payment_str'] = isset($this->order_map_payment[$v['payment']])? $this->order_map_payment[$v['payment']]: $v['payment'];
            }
        }
        return ['list'=>$list, 'total'=>$total, 'page'=>$page, 'page_size'=>$pageSize];
    }

    public function delCustomer($ids){
        return  $this->save(['sales_consultant'=>0,
            'bind_change_time'=>time(),
            'last_visit_time'=>0,
            'deletetime'=>time(),
            'labels'=> ''],
            ['id'=> ['in', $ids]]);
    }
    public function bind_customer($id, $comid){
        $row = $this->where(['comid'=>$comid])->find();
        $row2 = $this->where(['id'=>$id])->find();
        if($row2['comid'] == $comid)return ;
        if(isset($row['id'])){
            exception('该公司已被 '.$row['title'].' 绑定');
        }
        $comInfo = $this->com_info($comid);
        return $this->save(['comid'=>$comid, 'company_name'=>$comInfo['companyname'], 'uid'=>$comInfo['uid']], ['id'=>$id]);
    }

    public function check_com_title($title){
        $where = [];
        $where[] = [ 'EXP',  Db::raw(sprintf('id not in (select comid from %s)', $this->getTable() ))];
        $where['companyname'] =  ['like', "%$title%"];
        return Db::table(config('database.prefix').'company')->where($where)->field('id,companyname')->limit(10)->select();
    }

    public function unbind_customer($id){
        return $this->save(['comid'=>0, 'company_name'=>'', 'uid'=>0], ['id'=>$id]);
    }

    public function set_labels($cids, $labels){
        $cids = array_map('intval', $cids);
        $labels = array_map('intval', $labels);
        if(!$cids){
            exception('参数错误');
        }
        $rows = $this->where(['id'=> ['in', $cids]])->select();
        if($rows){
            foreach($rows as $row){
// $cur = $row['labels']? explode(',', $row['labels']): [];
                $cur = $labels;
                if(count($cids)>1){
                    $end =  array_unique($cur);
                }else{
                    $end = $labels;
                }
                $this->where(['id'=>$row['id']])->update(['labels'=>implode(',', $end)] );
            }
        }
        return true;
    }

    public function set_status($cid, $status, $scId){
        if(!$cid || !$status){
            exception('参数错误');
        }
        $row = $this->find($cid);
        if(!$row || !isset($row['id']))exception('客户不存在');
        //if($row['status'] == self::STATUS_COMPLETE)exception('已成交状态不可修改');
        $this->save(['status'=>$status], ['id'=>$cid]);
        $log = new CrmStatusLog();
        $log->insert([
            'cid' => $row['id'],
            'from_status' => $row['status'],
            'status' => $status,
            'sc_id' => $scId,
            'addtime' => time()
        ]);
        return true;
    }
    public function set_level($cid, $level, $scId){
        if(!$cid || !$level){
            exception('参数错误');
        }
        $row = $this->find($cid);
        if(!$row || !isset($row['id']))exception('客户不存在');
        $this->save(['level'=>$level], ['id'=>$cid]);
        $log = new CrmLevelLog();
        $log->insert([
            'cid' => $row['id'],
            'from_level' => $row['level'],
            'level' => $level,
            'sc_id' => $scId,
            'addtime' => time()
        ]);
        return true;
    }

    public function getInfo($cid){
        $cv = new CrmVisitLog();
        $row = $this->find($cid)->toArray();
        if(!$row || !isset($row['id']))exception('客户不存在');
        $comInfo = Db::table(config('database.prefix').'company')->where('id', $row['comid'])->find();
        if (empty($comInfo))
        {
// return [];
            $comInfo['uid'] = 0;
            $comInfo['setmeal_id'] = 0;
        }
        $member =  Db::table(config('database.prefix').'member')->where(['uid'=> $row['uid'], 'utype'=> 1])->find();
        $memberBind = Db::table(config('database.prefix').'member_bind')->where(['uid'=> $row['uid'], 'type'=>'weixin'])->find();
        $setmealLog = Db::table(config('database.prefix').'member_setmeal')
            ->where(['uid' => $comInfo['uid'], 'setmeal_id'=>$comInfo['setmeal_id']])
            ->order('id desc')
            ->find();
        $setMeal = Db::table(config('database.prefix').'setmeal')->column('id,name');
        $visitLog = $cv->where(['cid'=>$cid])->order('id desc')->find();
        $visitTotal = $cv->where(['cid'=>$cid])->count();
        $getLog = (new CrmGetLog())->getLastLog($cid);
        $scMap = (new CrmConfig())->sales_consultant();

        $last_visit_sc_id = 0;
        if ($visitLog&&$visitLog['sc_id'])
        {
            $last_visit_sc_id = isset($scMap[$visitLog['sc_id']])?$scMap[$visitLog['sc_id']]:0;
        }
        $sc_name = '';
        if ( $row['sales_consultant']>0)
        {
            $sc_name = isset($scMap[$row['sales_consultant']])?$scMap[$row['sales_consultant']]:'';
        }
        return [
            'title' => $row['title'],
            'uid' => $row['uid'],
            'company_name' => $row['company_name'],
            'comid' => $row['comid'],
            'status' => $row['status'],
            'level' => $row['level'],
            'logo_url' => isset($comInfo['logo'])? (new Uploadfile())->getFileUrl($comInfo['logo']):default_empty('logo') ,
            'labels' => $row['labels'],
            'setmeal' => $comInfo['setmeal_id']>0 ? $setMeal[$comInfo['setmeal_id']]: '',
            'deadline' => $setmealLog&&$setmealLog['deadline']? date('Y-m-d H:i:s',$setmealLog['deadline']): '',
            'last_visit' => $row['last_visit_id']>0 ? date('Y-m-d H:i:s',$row['last_visit_time']): '',
            'last_visit_sc_id' =>  $last_visit_sc_id,
            'visit_total' => $visitTotal,
            'com_addr' => $row['com_addr'],
            'receive_type' => $getLog && isset($getLog['action_fmt'])? $getLog['action_fmt']: '',
            'last_get_time' => $getLog && isset($getLog['addtime'])?  date('Y-m-d H:i:s', $getLog['addtime']):'',
            'sc_name' => $sc_name,
            'sales_consultant' => $row['sales_consultant'],
            'addtime' => $row['addtime'],
            'last_login_time' => (isset($member['last_login_time']) && $member['last_login_time']>0)?$member['last_login_time']:'',
            'last_login_time_fmt' => (isset($member['last_login_time']) && $member['last_login_time']>0) ? date('Y-m-d H:i:s', $member['last_login_time']): '-',
            'username' => isset($member['username'])?$member['username']:'',
            'member_bind' => $memberBind?($memberBind['openid']? true: false): false,
            'remark' => $row['remark'],
            'active_job_total' => isset($row['comid'])?  Db::table(config('database.prefix').'job')->where(['company_id'=>$row['comid']])->count(): 0
        ];
    }

    public function getActiveJobs($cid, $page, $pageSize){
        $row = $this->find($cid);
        if(!$row || !isset($row['comid']))return [];
        $total = Db::table(config('database.prefix').'job')->where(['company_id'=>$row['comid']])->count();
        $list = Db::table(config('database.prefix').'job')->alias('a')
            ->join(config('database.prefix').'job_contact b', 'a.id=b.jid', 'left')
            ->join(config('database.prefix').'category_job c', 'a.category=c.id', 'left')
            ->where(['a.company_id'=>$row['comid']])
            ->order('refreshtime desc')
            ->limit(($page-1)*$pageSize, $pageSize)
            ->field('a.id,a.jobname,a.minwage,a.maxwage,a.negotiable,a.education,a.experience,a.amount,a.audit,a.category,a.refreshtime,b.contact,b.mobile,b.telephone,c.name as category_name,a.is_display,b.use_company_contact')
            ->select();
        $comContact = Db::table(config('database.prefix').'company_contact')->where(['comid'=>$row['comid']])->find();
        foreach($list as &$v){
            $v['url'] = url('index/job/show',['id'=>$v['id']]);
            $v['education'] = isset($this->map_education[$v['education']])? $this->map_education[$v['education']]: '不限';
            $v['experience'] = isset($this->map_experience[$v['experience']])? $this->map_experience[$v['experience']]: '不限';
            if($v['negotiable']){
                $v['wage'] = '面议';
            }else{
                $v['wage'] = $v['minwage'] . ' - '. $v['maxwage'];
            }
            if($v['use_company_contact']){
                $v['contact'] = $comContact['contact'];
                $v['mobile'] = $comContact['mobile'];
                $v['telephone'] = $comContact['telephone'];
            }
        }
        return ['list'=>$list, 'total'=>$total, 'page'=>$page, 'page_size'=>$pageSize];
    }
    public function getList($options=[]){
        $config = new CrmConfig();
        $remainDays = $config->getRemainDays();
        $page = input('get.page/d', 1, 'intval');
        $pageSize = input('get.page_size/d', 10, 'intval');
        $status = input('get.status/d', 0, 'intval');
        $levels = input('get.levels/a', [], 'filter_empty_array');
        $labelsArr = input('get.labels/a', [], 'filter_empty_array');
        $sales_consultant = input('get.sales_consultant/d', 0, 'intval');
        $bind = input('get.bind/d', -1, 'intval');
        $search_type = input('get.search_type/d', 0, 'intval');
        $keywords = input('get.keywords/s', '', 'trim');
        $type = input('get.type/d', 0, 'intval');
        $audit = input('get.audit/d', -1, 'intval');
        $jobing = input('get.jobing/d', -1, 'intval');
        $sortByVisit = input('get.sort_by_visit/s', '', 'trim');
        $where = $options;
        $hasWhere = [];
        $where['realdelete'] = 0;
        if($bind==1){
            $where['comid'] = ['gt', 0];
        }else if(!$bind){
            $where['comid'] = 0;
        }
        if($status)$where['status'] = $status;
        if($levels)$where['level'] = ['in', $levels];
        if($labelsArr){
            // 问题修复 改为二维数组的取值方式 chenyang 2022年3月16日17:23:27
            foreach($labelsArr as $labelsInfo){
                foreach ($labelsInfo as $labelsId) {
                    if(intval($labelsId)<=0)continue;
                    $where[] = [ 'EXP',  Db::raw(sprintf('find_in_set(%d,labels)', $labelsId))];
                }
            }
        }
        if( in_array($audit, [0,1,2]) ){
            $where[] = ['EXP', Db::raw(sprintf('comid in (select id from %s where audit=%d)', config('database.prefix').'company', $audit ))];
        }
        if($audit == 3){
            $where[] = ['EXP', Db::raw(sprintf('comid in (select id from %s where audit=%d)', config('database.prefix').'company', 0 ))];
            $where[] = ['EXP', Db::raw(sprintf('comid in (select comid from %s)', config('database.prefix').'company_auth'))];
        }
        if($jobing == 1){
            $where[] = ['EXP', Db::raw(sprintf('comid in (select company_id from %s where audit=1 and is_display=%d)', config('database.prefix').'job', $jobing ))];
        }else if(!$jobing){
            $where[] = ['EXP', Db::raw(sprintf('comid not in (select company_id from %s where audit=1 and is_display=%d)', config('database.prefix').'job', $jobing ))];
        }
        if($sales_consultant){
            $where['sales_consultant'] = $sales_consultant;
        }
        if($search_type==0 && $keywords){
            $where['title'] = ['like','%'.$keywords.'%'];
        }
        $order = 'updatetime desc';
        if(in_array($sortByVisit, ['desc', 'asc']))$order = 'last_visit_time '.$sortByVisit;
        switch ($type){
            case 1://联系中的客户
                $where['last_visit_time'] = ['gt', 0];
                break;
            case 2://预约回访
                $where['last_pre_visit_id'] = ['gt', 0];
                break;
            case 3://今日新增
                $where['addtime'] = ['gt', strtotime('today')];
                break;
            case 4://今日跟进过
                $where['last_visit_time'] = ['gt', strtotime('today')];
                break;
            case 5://30天未跟进
                $where['last_visit_time'] = [ 'between', [0, time()-86400*30]];
                break;
            case 6://从未跟进
                $where['last_visit_time'] = 0;
                break;
            case 7://15天到期
              // $where['last_visit_time'] = ['lt', strtotime('today')-86400*($remainDays-15)];
                $where[] = ['EXP', Db::raw(sprintf('uid in (select uid from %s where deadline between %d and %d) ', config('database.prefix').'member_setmeal', time()-15*86400, time() ))];
                break;
            case 8://30天跟进过
                $where['last_visit_time'] = [ 'gt', time()-86400*30];
                break;
            case 9: //今日转为公客
                $where['last_visit_time'] = ['lt', strtotime('today')-86400*($remainDays-1)];
                $where['sales_consultant'] = 0;
                break;
            case 10://今日登录客户
                $where[] = ['EXP', Db::raw(sprintf('uid in (select uid from %s where last_login_time>%d)', config('database.prefix').'member', strtotime('today') ))];
                break;
        }
        $with = 'linkman,visitlog,visitlog.big,previsit,company,companybind,setmeal,member,companyauth';
        if($keywords && $search_type==1){
            $hasWhere['mobile'] = intval($keywords);
            $list = $this->hasWhere('linkman', $hasWhere)->where($where)->with($with)
                ->order($order)
                ->limit(($page-1)*$pageSize, $pageSize)
                ->select();
            $total = $this->hasWhere('linkman', $hasWhere)->where($where)->count();
        }else{
            $list = $this->where($where)->with($with)
                ->order($order)
                ->limit(($page-1)*$pageSize, $pageSize)
                ->select();
            $total = $this->where($where)->count();
        }
        foreach($list as $k=> &$l){
            if($l['audit']==0 && $l['comauthid']){
                $l['audit'] = 3;
            }
            $list[$k]['sales_consultant'] = model('admin')->where(['id'=>$l['sales_consultant']])->value('username');

            if(isset($l['visitlog']['result'])){
                $l['visitlog']['result'] = strip_tags(htmlspecialchars_decode($l['visitlog']['result'],ENT_QUOTES));
            }

            $l['com_url'] = $l['comid']>0 ? url('index/company/show',['id'=>$l['comid']]): '';
        }
        return ['list'=>$list, 'total'=>$total,  'page'=>$page, 'page_size'=>$pageSize];
    }

    public function linkman(){
        return $this->hasOne('CrmLinkman', 'id', 'master_linkman');
    }
    public function company(){
        return $this->hasOne(Company::class, 'id', 'comid')->bind('audit');
    }
    public function companyauth(){
        return $this->hasOne(CompanyAuth::class, 'comid', 'comid')->bind(['comauthid'=>'id']);
    }
    public function setmeal(){
        return $this->hasOne(MemberSetmeal::class, 'uid', 'uid')->bind('deadline,setmeal_id');
    }
    public function companybind(){
        return $this->hasOne(MemberBind::class, 'uid', 'uid')->bind('openid');
    }
    public function member(){
        return $this->hasOne(Member::class, 'uid', 'uid')->bind('last_login_time,username');
    }

    public function visitlog(){
        return $this->hasOne('CrmVisitLog', 'id', 'last_visit_id');
    }

    public function previsit(){
        return $this->hasOne('CrmReserveVisit', 'id', 'last_pre_visit_id');
    }

    public function new_company($keyword, $page, $pageSize){
        $where = 'a.id not in (select comid from '.config('database.prefix').'crm_customer)  ';
        if($keyword){
           $where .= " and a.companyname like '%$keyword%' ";
        }
        $total = Db::table(config('database.prefix'). 'company')->alias('a')->where($where)->count();
        $list = Db::table(config('database.prefix'). 'company')->alias('a')
            ->join(config('database.prefix'). 'company_contact b', 'a.id=b.comid')
            ->where($where)
            ->order('id desc')
            ->limit(($page-1)*$pageSize, $pageSize)
            ->field('a.id,a.companyname,a.cs_id,a.addtime,a.audit,b.contact,b.mobile,b.email,b.weixin,b.telephone')->select();
        $csMap = $this->sc_map();

        if(!empty($list)){
            foreach($list as &$v){
                $v['cs_name'] = isset($csMap[$v['cs_id']]) ? $csMap[$v['cs_id']]: '无';
                $v['addtime_fmt'] = date('Y-m-d H:i:s', $v['addtime']);
            }
        }
        return ['list'=>$list, 'total'=>$total,  'page'=>$page, 'page_size'=>$pageSize];
    }

    public function delete_coms($comIds){
        $coms = Db::table(config('database.prefix'). 'company')->where(['id'=>['in', $comIds]])->field('id,companyname,addtime,audit')->select();
        if($coms){
            $data = [];
            foreach($coms as $v){
                $data[] = [
                    'comid' => $v['id'],
                    'title' => $v['companyname'],
                    'realdelete' => time(),
                    'addtime' => time(),
                    'updatetime'=> time()
                ];
            }
            $this->insertAll($data);
        }
    }

    public function com_info($id){
        $com = Db::table(config('database.prefix'). 'company');
        $comInfo = Db::table(config('database.prefix'). 'company_info');
        $comContact = Db::table(config('database.prefix'). 'company_contact');
        $info = $com->where(['id'=>$id])->field('id,companyname,audit,uid')->find();
        $cInfo = $comInfo->where(['comid'=>$id])->find();
        $cContact = $comContact->where(['comid'=>$id])->find();
        $auth = Db::table(config('database.prefix'). 'company_auth')->where(['comid'=>$id])->find();
        if($info['audit'] == 0 && !empty($auth)){
            $info['audit'] = 3;
        }
        $info['address'] = $cInfo?$cInfo['address']:'';
        $info['contact'] = $cContact;
        return $info;
    }

    public function get_com_contact($comid){
        $comContact = Db::table(config('database.prefix'). 'company_contact');
        $cContact = $comContact->where(['comid'=>$comid])->find();
        if($cContact){
            return [
                'name' => $cContact['contact'],
                'appellation' => '',
                'position' => '',
                'gendor' => 0,
                'mobile' => $cContact['mobile'],
                'telephone' => $cContact['telephone'],
                'qq' => $cContact['qq'],
                'email' => $cContact['email'],
                'is_com_contact' => 1 //企业联系方式
            ];
        }else{
            return false;
        }
    }

    public function sc_map(){
        $c = cache('sc_map');
        if(!$c){
            $c = Db::table(config('database.prefix'). 'customer_service')->column('id,name');
            cache('sc_map', $c, 600);
        }
        return $c;
    }

    public function set_sc($cids, $sc, $remark, $op_id){
        if(empty($cids))return ;
        $this->checkOwnNum($sc, count($cids));
        $getLog = new CrmGetLog();
        $ccs = $this->where(['id'=>['in', $cids]])->select();
        $cids2 = [];
        $log = [];
        foreach($ccs as $cc){
            if($cc['sales_consultant'] == $sc)continue;
            $cids2[] = $cc['id'];
            $log[] = [
                'cid' => $cc['id'],
                'sc_id' =>$sc,
                'from_sc_id' => $cc['sales_consultant'],
                'action_type' => $sc == $op_id ? 3: 2,
                'remark' => $remark,
                'addtime' => time(),
                'get_time' => time(),
                'op_id' => $op_id
            ];
        }

        if($cids2){
            $r = $this->where( ['id'=>['in', $cids2]])->update(['sales_consultant'=>$sc, 'bind_change_time'=>time()]);
            if($r) $getLog->insertAll($log);
        }
    }

    public function checkOwnNum($sc, $num){
        if(!$sc)return true;
        $limit = (new CrmConfig())->getCustomerLimit();
        $c = $this->where(['sales_consultant'=>$sc])->count();
        $c += $num;
        if($c>$limit)exception(sprintf('客户数量已达上限:%d', $limit));
        return true;
    }

    public function import_customer($cust, $linkman, $admininfo, $force){
        $lModel = new CrmLinkman();
        if($linkman['mobile']){
            $r = $lModel->checkMobile($linkman['mobile']);
        }
        if($r !== true && !$force){
            if(!is_string($r)){
                exception('手机号重复');
            }else{
                exception($r);
            }
        }

        Db::startTrans();
        if(isset($cust['comid']) && $cust['comid']){
            $has = $this->where(['comid'=>$cust['comid']])->count();
            if($has)exception('该企业已被绑定');
            $uid = Db::table(config('database.prefix').'company')->where('id', $cust['comid'])->value('uid');
            $cust['uid'] = intval($uid);
        }
        //增加realdelete=0 条件，解决CRM系统新增用户删除后无法手动录入问题
        if($this->where(['title'=>$cust['title'],'realdelete'=>0])->count())exception('该企业名称已存在');
        $fr = false;
        $lModel->save($linkman);
        $lkId = $lModel->getLastInsID();
        if($lkId>0){
            $cust['master_linkman'] = $lkId;
            $cust['updatetime']  =  $cust['addtime'] = time();
            $ccId = $this->insert($cust, false, true);
            if($ccId>0){
                if(isset($cust['sales_consultant'])){
                    $getLog = new CrmGetLog();
                    $getLog->insertOne($ccId, $cust['sales_consultant'], 0, CrmGetLog::ADD_NEW, '', $admininfo->id );
                }
                $fr = $lModel->save(['cid'=>$ccId], ['id'=>$lkId]);
            }
        }

        if($fr){
            Db::commit();
        }else{
            Db::rollback();
        }
        return $ccId;
    }

    public function dashboard($scId=0){
        $month = strtotime(date('Y-m'));
        $todayTime = strtotime('today');
        $visitLog = new CrmVisitLog();
        $crmOrder = new CrmOrder();
        $task = new CrmTask();

        $where = $where1 = $where2 =  [];
        if($scId){
            $where = ['sales_consultant'=>$scId];
            $where1 = ['sc_id'=>$scId];
            $where2 = ['resolve_sc_id'=>$scId];
        }

        $tongji = [
            'total_customer' =>  $this->where($where)->count(),
            'total_new_customer' => $this->where($where+['bind_change_time'=>['gt', $todayTime]])->count(),
            'total_visit' => $visitLog->where($where1+['addtime'=>['gt', $todayTime]])->count(),
            'total_unbind_com' => $this->where($where+['comid'=>0])->count(),
            'total_no_job' => $this->total_no_job($scId),//未发布职位
            'total_15days_end' => $this->total_15days($scId),//15天到期

            'total_reserve_visit' => $this->total_reserve_visit($scId),
            'total_releasing_customer' => $this->total_releasing_customer($scId, 1),
            'total_releasing_customer_30' => $this->total_releasing_customer($scId, 30),
            'total_task' => $task->where($where2+['complete_time'=>0])->count(),
            'total_order' => $crmOrder->getTotalOrder($scId),
           // 'total_new_com' => $this->total_new_com(),
            'total_perm' => $crmOrder->totalPermToday($scId),
            'levels_map' => $this->levels_map($scId),
            'perm_top' => $this->perm_top($month),
            'new_customer_top' => $this->new_customer_top($month)
        ];
        return $tongji;
    }

    public function levels_map($scId){
        $where = [];
        $oid = 99999;
        if($scId)  $where = ['sales_consultant'=>$scId];
        $r = $this->where($where)->field('count(*) as levelc,level')->group('level')->select();
        $tmp = [];
        foreach($r as $v){
            $tmp[$v['level']] = $v['levelc'];
        }
        $r = $tmp;

        $levels = (new CrmConfig())->getData2('level');
        $other = ['id'=>$oid, 'name'=> '其它','is_sys'=>0, 'count'=>0];
        $levels[] = $other;
        $res = [];

        foreach($levels as $n){
            $c = isset($r[$n['id']])? $r[$n['id']]: 0;
            if($n['is_sys']){
                $n['count'] = $c;
                $res[$n['id']] = $n;
            }else{
                $other['count'] += $c;
                $res[$oid] = $other;
            }
        }
        return $res;
    }

    public function total_releasing_customer($scId, $days){
        $config = new CrmConfig();
        $remainDays = $config->getRemainDays();
        $where = ['status'=> ['neq', self::STATUS_COMPLETE]];
        if($scId){
            $where['sales_consultant'] = $scId;
        }else{
            $where['sales_consultant'] = ['gt', 0];
        }
        $where['last_visit_time'] = ['lt', strtotime('today')-86400*($remainDays-$days)];
        return $this->where($where)->count();
    }

    public function total_no_job($scId){
        $where = sprintf('id in (select comid from %s where comid>0  ', $this->getTable());
        if($scId){
            $where .= " and sales_consultant=$scId )";
        }else{
            $where .= ')';
        }
        $where1 = sprintf("uid not in (select distinct(uid) from %s where is_display=1)", config('database.prefix'). 'job');
        return  Db::table(config('database.prefix'). 'company')->where($where)->where($where1)->count();
    }
    public function total_15days($scId){
        $where = [];
        $w = sprintf('id in (select comid from %s where comid>0  ', $this->getTable());
        if($scId){
            $w .= " and sales_consultant=$scId )";
        }else{
            $w .= ')';
        }
        $where[] = ['EXP', Db::raw($w)];
        $where[] = ['EXP', Db::raw(sprintf('uid in (select uid from %s where deadline between %d and %d) ', config('database.prefix').'member_setmeal', time()-15*86400, time() ))];
        return  Db::table(config('database.prefix'). 'company')->where($where)->count();
    }
    public function total_reserve_visit($scId){
        $where = [];
        if($scId)$where['sc_id'] = $scId;
        $where['visit_time'] = 0;

        $m = new CrmReserveVisit();
        return $m->where($where)->count();
    }

    public function total_new_com(){
        $where = 'id not in (select comid from '.config('database.prefix').'crm_customer)  ';
        return  Db::table(config('database.prefix'). 'company')->where($where)->count();
    }

}
