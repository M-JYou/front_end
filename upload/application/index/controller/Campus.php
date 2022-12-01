<?php
namespace app\index\controller;
use app\common\lib\Pager;
class Campus extends \app\index\controller\Base{
    public function _initialize(){
        parent::_initialize();
        $this->assign('navSelTag','campus');
    }
    /** [index 校园招聘首页] */
    public function index()
    {
        if(is_mobile_request()===true){
            $this->redirect(config('global_config.mobile_domain').'campus/index');
            exit;
        }
        $banner_list = $this->getBannerList();
        $notice_list = $this->get_notice();
        $election_list = $this->get_election();
        $school_list = $this->get_school_list();
        $job_list = $this->get_job();
        $category_jobs = model('CategoryJob')->getCache('');
        $this->initPageSeo('campus');
        $this->assign('notice_list',$notice_list);
        $this->assign('election_list',$election_list);
        $this->assign('school_list',$school_list);
        $this->assign('job_list',$job_list);
        $this->assign('banner_list',$banner_list);
        $this->assign('category_jobs',$category_jobs);
        $this->assign('pageHeader',$this->pageHeader);
        return $this->fetch('index');
    }

    /** 广告 */
    protected function getBannerList()
    {
        $alias_arr = [
            'QS_campus_top_slide@web',
            'QS_campus_famous@web'
        ];
        $category_arr = model('CampusAdCategory')->whereIn('alias', $alias_arr)->column('id,alias,ad_num', 'id');
        $cid_arr = [];
        foreach ($category_arr as $key => $value) {
            $cid_arr[] = $value['id'];
        }
        $timestamp = time();
        $dataset = model('CampusAd')
            ->where('is_display', 1)
            ->whereIn('cid', $cid_arr)
            ->where('starttime', '<=', $timestamp)
            ->where(function ($query) use ($timestamp) {
                $query->where('deadline', '>=', $timestamp)->whereOr('deadline', 0);
            })
            ->order('sort_id desc,id desc')
            ->column('id,cid,title,imageid,imageurl,target,link_url,inner_link,inner_link_params,company_id');
        $image_id_arr = $image_arr = [];
        foreach ($dataset as $key => $value) {
            $arr = $value;
            $arr['imageid'] > 0 && ($image_id_arr[] = $arr['imageid']);
            $dataset[$key] = $arr;
        }
        if (!empty($image_id_arr)) {
            $image_arr = model('Uploadfile')->getFileUrlBatch($image_id_arr);
        }
        $list = [];
        $allCompanyId = [];
        foreach ($dataset as $key => $value) {
            $value['image_src'] = isset($image_arr[$value['imageid']]) ? $image_arr[$value['imageid']] : $value['imageurl'];
            if (isset($list[$category_arr[$value['cid']]['alias']]) && count($list[$category_arr[$value['cid']]['alias']]) >= $category_arr[$value['cid']]['ad_num']) {
                continue;
            }
            $arr = [];
            $arr['title'] = $value['title'];
            $arr['image_src'] = $value['image_src'];
            $arr['link_url'] = $value['link_url'];
            $arr['inner_link'] = $value['inner_link'];
            $arr['inner_link_params'] = $value['inner_link_params'];
            $arr['company_id'] = $value['company_id'];
            $arr['companyname'] = '';
            $arr['web_link_url'] = model('CampusAd')->handlerWebLink($value);
            $list[$category_arr[$value['cid']]['alias']][] = $arr;
            $value['company_id'] && $allCompanyId[] = $value['company_id'];
        }
        foreach ($category_arr as $key => $value) {
            if (!isset($list[$value['alias']])) {
                $list[$value['alias']] = [];
            }
        }
        $allCompanyId = array_unique($allCompanyId);
        //获取广告位中涉及的所有企业信息
        $all_company_arr = $this->getAllCompany($allCompanyId);
        //处理名企广告
        $list['QS_campus_famous@web'] = $this->handlerFamous($list['QS_campus_famous@web'],$all_company_arr);
        return $list;
    }

    /** 获取广告位中涉及的所有企业信息 */
    protected function getAllCompany($allCompanyId){
        $companyData = [];
        if(!empty($allCompanyId)){
            $companyData = model('Company')->where('is_display',1)->whereIn('id',$allCompanyId)->column('id,companyname');
        }
        return $companyData;
    }

    /** 处理名企广告 */
    protected function handlerFamous($list,$all_company_arr){
        foreach ($list as $key => $value) {
            $list[$key]['companyname'] = isset($all_company_arr[$value['company_id']])?$all_company_arr[$value['company_id']]:$value['title'];
        }
        return $list;
    }

    /** 院校列表 */
    public function school()
    {
        if(is_mobile_request()===true){
            $this->redirect(config('global_config.mobile_domain').'campus/school');
            exit;
        }
        $where = [];
        $keyword = request()->route('keyword/s', '', 'trim');
        $current_page = request()->get('page/d',1,'intval');
        $pagesize = request()->get('pagesize/d',9,'intval');
        $level = request()->route('level/d',0,'intval');
        $type = request()->route('type/d',0,'intval');
        $district1 = request()->route('d1/d',0,'intval');
        $district2 = request()->route('d2/d',0,'intval');
        $district3 = request()->route('d3/d',0,'intval');
        if ($keyword != '') {
            $where['name'] = ['like', '%' . $keyword . '%'];
        }
        if ($level > 0) {
            $where['level'] = ['eq', $level];
        }
        if ($type > 0) {
            $where['type'] = ['eq', $type];
        }
        if ($district3 > 0) {
            $where['district3'] = ['eq', $district3];
        } elseif ($district2 > 0) {
            $where['district2'] = ['eq', $district2];
        } elseif ($district1 > 0) {
            $where['district1'] = ['eq', $district1];
        }
        $where['display'] = ['eq', 1];
        $list = model('CampusSchool')
            ->where($where)
            ->field('id,name,logo,district1,district2,district3,level,type')
            ->order('addtime desc')
            ->page($current_page, $pagesize)
            ->select();
        $total = model('CampusSchool')
            ->where($where)
            ->count();
        $image_id_arr = $image_list = [];
        foreach ($list as $key => $value) {
            $value['logo'] && ($image_id_arr[] = $value['logo']);
        }
        if (!empty($image_id_arr)) {
            $image_list = model('Uploadfile')->getFileUrlBatch($image_id_arr);
        }
        $preach_count_arr = model('CampusPreach')->group('school_id')->column('school_id,count(*) as num');
        $election_count_arr = model('CampusElection')->group('school_id')->column('school_id,count(*) as num');
        $category_district_data = model('CategoryDistrict')->getCache('all');
        foreach ($list as $key => $value) {
            $value['logo_url'] = isset($image_list[$value['logo']])
                ? $image_list[$value['logo']]
                : '';
            $value['level_cn'] = model('CampusSchool')->map_level[$value['level']];
            $value['type_cn'] = model('CampusSchool')->map_type[$value['type']];
            $district_index =
                $value['district3'] != 0
                    ? $value['district3']
                    : ($value['district2'] != 0
                    ? $value['district2']
                    : $value['district1']);
            $value['district_cn'] = isset(
                $category_district_data[$district_index]
            )
                ? $category_district_data[$district_index]
                : '';
            $value['preach_count'] = isset($preach_count_arr[$value['id']]) ? $preach_count_arr[$value['id']] : 0;
            $value['election_count'] = isset($election_count_arr[$value['id']]) ? $election_count_arr[$value['id']] : 0;
            $value['link_url'] = config('global_config.sitedomain').'/campus/school/show/'.$value['id'];
            $list[$key] = $value;
        }
        $level_list_arr = model('CampusSchool')->map_level;
        foreach ($level_list_arr as $key => $value) {
            $arr['id'] = $key;
            $arr['name'] = $value;
            $level_list[] = $arr;
        }
        $type_list_arr = model('CampusSchool')->map_type;
        foreach ($type_list_arr as $key => $value) {
            $arr['id'] = $key;
            $arr['name'] = $value;
            $type_list[] = $arr;
        }
        if($district2>0){
            $district_level = 3;
            $category_district = model('CategoryDistrict')->getCache($district2);
        }else if($district1>0){
            $district_level = 2;
            $category_district = model('CategoryDistrict')->getCache($district1);
        }else {
            $district_level = 1;
            $category_district = model('CategoryDistrict')->getCache('0');
        }
        $options_district = [];
        foreach ($category_district as $key => $value) {
            if($district_level==1){
                $params = ['d1'=>$key,'d2'=>null,'d3'=>null];
            }else if($district_level==2){
                $params = ['d1'=>$district1,'d2'=>$key,'d3'=>null];
            }else if($district_level==3){
                $params = ['d1'=>$district1,'d2'=>$district2,'d3'=>$key];
            }

            $arr['id'] = $key;
            $arr['url'] = P($params);
            $arr['text'] = $value;
            $options_district[] = $arr;
        }
        $pagination = Pager::make($list,$pagesize,$current_page,$total,false,['path'=>'']);
        $this->initPageSeo('schoollist');
        $this->assign('list',$list);
        $this->assign('level_list',$level_list);
        $this->assign('type_list',$type_list);
        $this->assign('district_level',$district_level);
        $this->assign('options_district',$options_district);
        $this->assign('pageHeader',$this->pageHeader);
        $this->assign('pagerHtml',$pagerHtml = $pagination->render());
        $this->assign('navSelTag','school');
        return $this->fetch('campus/school/index');
    }

    /** 院校详情 */
    public function school_show()
    {
        $id = request()->route('id/d',0,'intval');
        if(is_mobile_request()===true){
            $this->redirect(config('global_config.mobile_domain').'campus/school/'.$id);
            exit;
        }
        if(!$id){
            abort(404,'请选择院校！');
        }
        $info = model('CampusSchool')
            ->where('id',$id)
            ->field('id,name,logo,district1,district2,district3,level,type,introduction,address,tel')
            ->find();
        if(null === $info){
            abort(404,'院校不存在或已删除！');
        }
        $info['introduction'] = htmlspecialchars_decode($info['introduction'],ENT_QUOTES);
        $category_district_data = model('CategoryDistrict')->getCache('all');
        $info['level_cn'] = model('CampusSchool')->map_level[$info['level']];
        $info['type_cn'] = model('CampusSchool')->map_type[$info['type']];
        $district_index =
            $info['district3'] != 0
                ? $info['district3']
                : ($info['district2'] != 0
                ? $info['district2']
                : $info['district1']);
        $info['district_cn'] = isset(
            $category_district_data[$district_index]
        )
            ? $category_district_data[$district_index]
            : '';
        $info['logo_url'] = model('Uploadfile')->getFileUrl($info['logo']);
        $info['preach_count'] = model('CampusPreach')->where('school_id', $info['id'])->count();
        $info['election_count'] = model('CampusElection')->where('school_id', $info['id'])->count();
        $info['share_url'] = config('global_config.mobile_domain').'campus/school/'.$info['id'];
        model('CampusSchool')->where('id',$id)->setInc('click',1);
        $this->initPageSeo('schoolshow',['name'=>$info['name']]);
        $this->assign('info',$info);
        $this->assign('pageHeader',$this->pageHeader);
        $this->assign('navSelTag','school');
        return $this->fetch('campus/school/show');
    }

    /** 院校详情-院校双选会 */
    public function school_election()
    {
        $id = request()->route('id/d',0,'intval');
        if(is_mobile_request()===true){
            $this->redirect(config('global_config.mobile_domain').'campus/school/'.$id);
            exit;
        }
        if(!$id){
            abort(404,'请选择院校！');
        }
        $info = model('CampusSchool')
            ->where('id',$id)
            ->field('id,name,logo,district1,district2,district3,level,type,introduction,address,tel')
            ->find();
        if(null === $info){
            abort(404,'院校不存在或已删除！');
        }
        $timestamp = time();
        $current_page = input('get.page/d', 1, 'intval');
        $pagesize = 10;
        $list = model('CampusElection')
            ->where('school_id',$id)
            ->field('id,subject,address,starttime,endtime')
            ->where('display', 1)
            ->order('addtime desc')
            ->page($current_page, $pagesize)
            ->select();
        $total = model('CampusElection')
            ->where('school_id',$id)
            ->where('display', 1)
            ->count();
        foreach ($list as $key => $value) {
            if ($value['starttime'] <= $timestamp && $value['endtime'] > $timestamp) {
                $value['score'] = 2; // 进行中
            } elseif ($value['starttime'] > $timestamp) {
                $value['score'] = 1; // 即将开始
            } else {
                $value['score'] = 0; // 已结束
            }
            $value['starttime'] = date('Y-m-d', $value['starttime']);
            $value['endtime'] = date('Y-m-d', $value['endtime']);
            $list[$key] = $value;
        }
        $this->initPageSeo('schoolelection');
        $pagination = Pager::make($list,$pagesize,$current_page,$total,false,['path'=>'']);
        $category_district_data = model('CategoryDistrict')->getCache('all');
        $info['level_cn'] = model('CampusSchool')->map_level[$info['level']];
        $info['type_cn'] = model('CampusSchool')->map_type[$info['type']];
        $district_index =
            $info['district3'] != 0
                ? $info['district3']
                : ($info['district2'] != 0
                ? $info['district2']
                : $info['district1']);
        $info['district_cn'] = isset(
            $category_district_data[$district_index]
        )
            ? $category_district_data[$district_index]
            : '';
        $info['logo_url'] = model('Uploadfile')->getFileUrl($info['logo']);
        $info['preach_count'] = model('CampusPreach')->where('school_id', $info['id'])->count();
        $info['election_count'] = model('CampusElection')->where('school_id', $info['id'])->count();
        $info['share_url'] = config('global_config.mobile_domain').'campus/school/'.$info['id'];
        $info['other_election'] = $this->other_election($id);
        $this->assign('list',$list);
        $this->assign('info',$info);
        $this->assign('pageHeader',$this->pageHeader);
        $this->assign('pagerHtml',$pagerHtml = $pagination->render());
        $this->assign('navSelTag','school');
        return $this->fetch('campus/school/election_list');
    }

    /** 院校详情-院校宣讲会 */
    public function school_preach()
    {
        $id = request()->route('id/d',0,'intval');
        if(is_mobile_request()===true){
            $this->redirect(config('global_config.mobile_domain').'campus/school/'.$id);
            exit;
        }
        if(!$id){
            abort(404,'请选择院校！');
        }
        $info = model('CampusSchool')
            ->where('id',$id)
            ->field('id,name,logo,district1,district2,district3,level,type,introduction,address,tel')
            ->find();
        if(null === $info){
            abort(404,'院校不存在或已删除！');
        }
        $info['introduction'] = htmlspecialchars_decode($info['introduction'],ENT_QUOTES);
        $current_page = input('get.page/d', 1, 'intval');
        $pagesize = 10;
        $list = model('CampusPreach')
            ->where('school_id',$id)
            ->field('id,subject,address,starttime')
            ->where('display', 1)
            ->order('addtime desc')
            ->page($current_page, $pagesize)
            ->select();
        $total = model('CampusPreach')
            ->where('school_id',$id)
            ->where('display', 1)
            ->count();
        foreach ($list as $key => $value) {
            $value['starttime'] = date('Y-m-d', $value['starttime']);
            $list[$key] = $value;
        }
        $this->initPageSeo('schoolpreach');
        $pagination = Pager::make($list,$pagesize,$current_page,$total,false,['path'=>'']);
        $category_district_data = model('CategoryDistrict')->getCache('all');
        $info['level_cn'] = model('CampusSchool')->map_level[$info['level']];
        $info['type_cn'] = model('CampusSchool')->map_type[$info['type']];
        $district_index =
            $info['district3'] != 0
                ? $info['district3']
                : ($info['district2'] != 0
                ? $info['district2']
                : $info['district1']);
        $info['district_cn'] = isset(
            $category_district_data[$district_index]
        )
            ? $category_district_data[$district_index]
            : '';
        $info['logo_url'] = model('Uploadfile')->getFileUrl($info['logo']);
        $info['preach_count'] = model('CampusPreach')->where('school_id', $info['id'])->count();
        $info['election_count'] = model('CampusElection')->where('school_id', $info['id'])->count();
        $info['share_url'] = config('global_config.mobile_domain').'campus/school/'.$info['id'];
        $info['other_preach'] = $this->other_preach($id);
        $this->assign('list',$list);
        $this->assign('info',$info);
        $this->assign('pageHeader',$this->pageHeader);
        $this->assign('pagerHtml',$pagerHtml = $pagination->render());
        $this->assign('navSelTag','school');
        return $this->fetch('campus/school/preach_list');
    }

    /** 院校详情-近期其他院校双选会 */
    protected function other_election($id)
    {
        $where = [];
        $timecase = 7;
        $timecase_map = model('CampusElection')->timecase_map($timecase);
        if ($timecase_map) {
            $where['a.starttime'] = $timecase_map;
        }
        $where['a.school_id'] = ['neq', $id];
        $where['a.display'] = 1;
        $list = model('CampusElection')
            ->alias('a')
            ->join(config('database.prefix') . 'campus_school b', 'a.school_id=b.id', 'left')
            ->field('a.id,a.school_id,b.name school_name,b.logo')
            ->where($where)
            ->order('a.addtime desc')
            ->select();
        $image_id_arr = $image_list = [];
        foreach ($list as $key => $value) {
            $value['logo'] && ($image_id_arr[] = $value['logo']);
        }
        if (!empty($image_id_arr)) {
            $image_list = model('Uploadfile')->getFileUrlBatch($image_id_arr);
        }
        foreach ($list as $key => $value) {
            $value['logo_url'] = isset($image_list[$value['logo']])
                ? $image_list[$value['logo']]
                : '';
            $list[$key] = $value;
        }
        return $list;
    }

    /** 院校详情-近期其他院校宣讲会 */
    protected function other_preach($id)
    {
        $where = [];
        $timecase = 7;
        $timecase_map = model('CampusElection')->timecase_map($timecase);
        if ($timecase_map) {
            $where['a.starttime'] = $timecase_map;
        }
        $where['a.school_id'] = ['neq', $id];
        $where['a.display'] = 1;
        $list = model('CampusPreach')
            ->alias('a')
            ->join(config('database.prefix') . 'campus_school b', 'a.school_id=b.id', 'left')
            ->field('a.id,a.school_id,b.name school_name,b.logo')
            ->where($where)
            ->order('a.addtime desc')
            ->select();
        $image_id_arr = $image_list = [];
        foreach ($list as $key => $value) {
            $value['logo'] && ($image_id_arr[] = $value['logo']);
        }
        if (!empty($image_id_arr)) {
            $image_list = model('Uploadfile')->getFileUrlBatch($image_id_arr);
        }
        foreach ($list as $key => $value) {
            $value['logo_url'] = isset($image_list[$value['logo']])
                ? $image_list[$value['logo']]
                : '';
            $list[$key] = $value;
        }
        return $list;
    }

    /** ajax获取院校分类 */
    public function get_school()
    {
        $school_list = model('CampusSchool')->field('id,name')->where('display', 1)->select();
        $this->ajaxReturn(200,'获取数据成功',$school_list);
    }

    /** 双选会列表 */
    public function election()
    {
        if(is_mobile_request()===true){
            $this->redirect(config('global_config.mobile_domain').'campus/election');
            exit;
        }
        $where = [];
        $keyword = request()->route('keyword/s', '', 'trim');
        $current_page = request()->get('page/d',1,'intval');
        $pagesize = request()->get('pagesize/d',12,'intval');
        $school_id = request()->route('school_id/d',0,'intval');
        $district1 = request()->route('d1/d',0,'intval');
        $district2 = request()->route('d2/d',0,'intval');
        $district3 = request()->route('d3/d',0,'intval');
        $timecase = request()->route('timecase/d',0,'intval');

        if ($keyword != '') {
            $where['a.subject'] = ['like', '%' . $keyword . '%'];
        }
        if ($district3 > 0) {
            $where['b.district3'] = ['eq', $district3];
        } elseif ($district2 > 0) {
            $where['b.district2'] = ['eq', $district2];
        } elseif ($district1 > 0) {
            $where['b.district1'] = ['eq', $district1];
        }
        if ($timecase != '') {
            $timecase_map = model('CampusElection')->timecase_map($timecase);
            if ($timecase_map) {
                $where['a.starttime'] = $timecase_map;
            }
        }
        if ($school_id != '') {
            $where['a.school_id'] = ['eq', $school_id];
        }

        $list = model('CampusElection')
            ->alias('a')
            ->join(config('database.prefix') . 'campus_school b', 'a.school_id=b.id', 'left')
            ->field('a.id,a.school_id,a.subject,a.address,a.starttime,a.endtime,a.company_count,a.graduate_count,b.name school_name')
            ->where($where)
            ->where('a.display', 1)
            ->order('a.addtime desc')
            ->page($current_page, $pagesize)
            ->select();
        $total = model('CampusElection')
            ->alias('a')
            ->join(config('database.prefix') . 'campus_school b', 'a.school_id=b.id', 'left')
            ->where($where)
            ->where('a.display', 1)
            ->count();
        $timestamp = time();
        foreach ($list as $key => $value) {
            if ($value['starttime'] <= $timestamp && $value['endtime'] > $timestamp) {
                $value['score'] = 2; // 进行中
            } elseif ($value['starttime'] > $timestamp) {
                $value['score'] = 1; // 即将开始
            } else {
                $value['score'] = 0; // 已结束
            }
            $value['starttime'] = date('Y-m-d', $value['starttime']);
            $value['endtime'] = date('Y-m-d', $value['endtime']);
            $list[$key] = $value;
        }
        if($district2>0){
            $district_level = 3;
            $category_district = model('CategoryDistrict')->getCache($district2);
        }else if($district1>0){
            $district_level = 2;
            $category_district = model('CategoryDistrict')->getCache($district1);
        }else {
            $district_level = 1;
            $category_district = model('CategoryDistrict')->getCache('0');
        }
        $options_district = [];
        foreach ($category_district as $key => $value) {
            if($district_level==1){
                $params = ['d1'=>$key,'d2'=>null,'d3'=>null];
            }else if($district_level==2){
                $params = ['d1'=>$district1,'d2'=>$key,'d3'=>null];
            }else if($district_level==3){
                $params = ['d1'=>$district1,'d2'=>$district2,'d3'=>$key];
            }

            $arr['id'] = $key;
            $arr['url'] = P($params);
            $arr['text'] = $value;
            $options_district[] = $arr;
        }
        $school_list = model('CampusSchool')->field('id,name')->where('display', 1)->select();
        $pagination = Pager::make($list,$pagesize,$current_page,$total,false,['path'=>'']);
        $category_district_data = model('CategoryDistrict')->getCache();
        $seoData['keyword'] = $keyword;
        if($district3>0){
            $seoData['citycategory'] = isset($category_district_data[$district3]) ? $category_district_data[$district3] : '';
        }else if($district2>0){
            $seoData['citycategory'] = isset($category_district_data[$district2]) ? $category_district_data[$district2] : '';
        }else if($district1>0){
            $seoData['citycategory'] = isset($category_district_data[$district1]) ? $category_district_data[$district1] : '';
        }else{
            $seoData['citycategory'] = '';
        }
        $seoData['schoolname'] = model('CampusSchool')->where('id',$school_id)->value('name');
        $this->initPageSeo('electionlist',$seoData);
        $this->assign('list',$list);
        $this->assign('school_list',$school_list);
        $this->assign('district_level',$district_level);
        $this->assign('options_district',$options_district);
        $this->assign('pageHeader',$this->pageHeader);
        $this->assign('pagerHtml',$pagerHtml = $pagination->render());
        $this->assign('navSelTag','election');
        return $this->fetch('campus/election/index');
    }

    /** 双选会详情 */
    public function election_show()
    {
        $id = request()->route('id/d',0,'intval');
        if(is_mobile_request()===true){
            $this->redirect(config('global_config.mobile_domain').'campus/election/'.$id);
            exit;
        }
        if(!$id){
            abort(404,'请选择双选会！');
        }
        $info = model('CampusElection')
            ->where('id',$id)
            ->field('id,school_id,subject,address,starttime,endtime,introduction,company_count,graduate_count')
            ->find();
        if(null === $info){
            abort(404, '双选会不存在或已删除！');
        }
        $info['introduction'] = htmlspecialchars_decode($info['introduction'],ENT_QUOTES);
        $info['school_name'] = model('CampusSchool')->where('id',$info['school_id'])->value('name');
        $timestamp = time();
        if ($info['starttime'] <= $timestamp && $info['endtime'] > $timestamp) {
            $info['score'] = 2; // 进行中
        } elseif ($info['starttime'] > $timestamp) {
            $info['score'] = 1; // 即将开始
        } else {
            $info['score'] = 0; // 已结束
        }
        $info['starttime'] = date('Y-m-d', $info['starttime']);
        $info['endtime'] = date('Y-m-d', $info['endtime']);
        $info['share_url'] = config('global_config.mobile_domain').'campus/election/'.$info['id'];
        $seoData['subject'] = $info['subject'];
        $seoData['schoolname'] = $info['school_name'];
        model('CampusElection')->where('id',$id)->setInc('click',1);
        $this->initPageSeo('electionshow',$seoData);
        $this->assign('pageHeader',$this->pageHeader);
        $this->assign('info',$info);
        $this->assign('navSelTag','election');
        return $this->fetch('campus/election/show');
    }

    /** 宣讲会列表 */
    public function preach()
    {
        if(is_mobile_request()===true){
            $this->redirect(config('global_config.mobile_domain').'campus/preach');
            exit;
        }
        $where = [];
        $keyword = request()->route('keyword/s', '', 'trim');
        $current_page = request()->get('page/d',1,'intval');
        $pagesize = request()->get('pagesize/d',9,'intval');
        $school_id = request()->route('school_id/d',0,'intval');
        $district1 = request()->route('d1/d',0,'intval');
        $district2 = request()->route('d2/d',0,'intval');
        $district3 = request()->route('d3/d',0,'intval');
        $timecase = request()->route('timecase/d',0,'intval');
        if ($keyword != '') {
            $where['a.subject'] = ['like', '%' . $keyword . '%'];
        }
        if ($district3 > 0) {
            $where['b.district3'] = ['eq', $district3];
        } elseif ($district2 > 0) {
            $where['b.district2'] = ['eq', $district2];
        } elseif ($district1 > 0) {
            $where['b.district1'] = ['eq', $district1];
        }
        if ($timecase != '') {
            $timecase_map = model('CampusElection')->timecase_map($timecase);
            if ($timecase_map) {
                $where['a.starttime'] = $timecase_map;
            }
        }
        if ($school_id != '') {
            $where['a.school_id'] = ['eq', $school_id];
        }

        $list = model('CampusPreach')
            ->alias('a')
            ->join(config('database.prefix') . 'campus_school b', 'a.school_id=b.id', 'left')
            ->field('a.id,a.school_id,a.subject,a.address,a.starttime,b.name school_name,b.logo,b.district1,b.district2,b.district3')
            ->where($where)
            ->where('a.display', 1)
            ->order('a.addtime desc')
            ->page($current_page, $pagesize)
            ->select();
        $total = model('CampusPreach')
            ->alias('a')
            ->join(config('database.prefix') . 'campus_school b', 'a.school_id=b.id', 'left')
            ->where($where)
            ->where('a.display', 1)
            ->count();
        $image_id_arr = $image_list = [];
        foreach ($list as $key => $value) {
            $value['logo'] && ($image_id_arr[] = $value['logo']);
        }
        if (!empty($image_id_arr)) {
            $image_list = model('Uploadfile')->getFileUrlBatch($image_id_arr);
        }
        $category_district_data = model('CategoryDistrict')->getCache('all');
        foreach ($list as $key => $value) {
            $district_index =
                $value['district3'] != 0
                    ? $value['district3']
                    : ($value['district2'] != 0
                    ? $value['district2']
                    : $value['district1']);
            $value['district_cn'] = isset(
                $category_district_data[$district_index]
            )
                ? $category_district_data[$district_index]
                : '';
            $value['logo_url'] = isset($image_list[$value['logo']])
                ? $image_list[$value['logo']]
                : '';
            $value['link_url'] = config('global_config.sitedomain').'/campus/preach/show/'.$value['id'];
            $list[$key] = $value;
        }
        $school_list = model('CampusSchool')->field('id,name')->where('display', 1)->select();
        if($district2>0){
            $district_level = 3;
            $category_district = model('CategoryDistrict')->getCache($district2);
        }else if($district1>0){
            $district_level = 2;
            $category_district = model('CategoryDistrict')->getCache($district1);
        }else {
            $district_level = 1;
            $category_district = model('CategoryDistrict')->getCache('0');
        }
        $options_district = [];
        foreach ($category_district as $key => $value) {
            if($district_level==1){
                $params = ['d1'=>$key,'d2'=>null,'d3'=>null];
            }else if($district_level==2){
                $params = ['d1'=>$district1,'d2'=>$key,'d3'=>null];
            }else if($district_level==3){
                $params = ['d1'=>$district1,'d2'=>$district2,'d3'=>$key];
            }

            $arr['id'] = $key;
            $arr['url'] = P($params);
            $arr['text'] = $value;
            $options_district[] = $arr;
        }
        $pagination = Pager::make($list,$pagesize,$current_page,$total,false,['path'=>'']);
        $seoData['keyword'] = $keyword;
        if($district3>0){
            $seoData['citycategory'] = isset($category_district_data[$district3]) ? $category_district_data[$district3] : '';
        }else if($district2>0){
            $seoData['citycategory'] = isset($category_district_data[$district2]) ? $category_district_data[$district2] : '';
        }else if($district1>0){
            $seoData['citycategory'] = isset($category_district_data[$district1]) ? $category_district_data[$district1] : '';
        }else{
            $seoData['citycategory'] = '';
        }
        $seoData['schoolname'] = model('CampusSchool')->where('id',$school_id)->value('name');
        $this->initPageSeo('preachlist',$seoData);
        $this->assign('list',$list);
        $this->assign('school_list',$school_list);
        $this->assign('district_level',$district_level);
        $this->assign('options_district',$options_district);
        $this->assign('pageHeader',$this->pageHeader);
        $this->assign('pagerHtml',$pagerHtml = $pagination->render());
        $this->assign('navSelTag','preach');
        return $this->fetch('campus/preach/index');
    }

    /** 宣讲会详情 */
    public function preach_show()
    {
        $id = request()->route('id/d',0,'intval');
        if(is_mobile_request()===true){
            $this->redirect(config('global_config.mobile_domain').'campus/preach/'.$id);
            exit;
        }
        if(!$id){
            abort(404,'请选择宣讲会！');
        }
        $info = model('CampusPreach')
            ->where('id',$id)
            ->field('id,school_id,subject,address,starttime,introduction')
            ->find();
        if(null === $info){
            abort(404, '宣讲会不存在或已删除！');
        }
        $info['introduction'] = htmlspecialchars_decode($info['introduction'],ENT_QUOTES);
        $school = model('CampusSchool')->where('id',$info['school_id'])->field('logo,name,tel')->find();
        $info['school_name'] = $school['name'];
        $info['school_tel'] = $school['tel'];
        $info['logo_url'] = model('Uploadfile')->getFileUrl($school['logo']);
        $info['starttime'] = daterange(time(), $info['starttime']);
        $info['share_url'] = config('global_config.mobile_domain').'campus/preach/'.$info['id'];
        $seoData['subject'] = $info['subject'];
        $seoData['schoolname'] = $info['school_name'];
        model('CampusPreach')->where('id',$id)->setInc('click',1);
        $this->initPageSeo('preachshow',$seoData);
        $this->assign('pageHeader',$this->pageHeader);
        $this->assign('info',$info);
        $this->assign('navSelTag','preach');
        return $this->fetch('campus/preach/show');
    }

    /** 校招职位 */
    public function job()
    {
        if(is_mobile_request()===true){
            $this->redirect(config('global_config.mobile_domain').'campus/job');
            exit;
        }
        $keyword = request()->route('keyword/s', '', 'trim');
        $listtype = request()->route('listtype/s','','trim');
        $category1 = request()->route('c1/d',0,'intval');
        $category2 = request()->route('c2/d',0,'intval');
        $category3 = request()->route('c3/d',0,'intval');
        $district1 = request()->route('d1/d',0,'intval');
        $district2 = request()->route('d2/d',0,'intval');
        $district3 = request()->route('d3/d',0,'intval');
        $minwage = request()->route('w1/d',0,'intval');
        $maxwage = request()->route('w2/d',0,'intval');
        $trade = request()->route('trade/d',0,'intval');
        $scale = request()->route('scale/d',0,'intval');
        $nature = request()->route('nat/d',0,'intval');
        $education = request()->route('edu/d',0,'intval');
        $experience = request()->route('exp/d',0,'intval');
        $tag = request()->route('tag/s', '', 'trim');
        $settr = request()->route('settr/d',0,'intval');
        $sort = request()->route('sort/s', '', 'trim');
        $famous = request()->route('famous/d',0,'intval');
        $license = request()->route('license/d',0,'intval');
        $filter_apply = request()->route('filter_apply/d',0,'intval');
        $current_page = request()->get('page/d',1,'intval');
        $pagesize = request()->get('pagesize/d',10,'intval');
        $selectedTagArr = [];

        if ($keyword != '') {
            $params['keyword'] = $keyword;
        }
        if ($district1 > 0) {
            $params['district1'] = $district1;
        }
        if ($district2 > 0) {
            $params['district2'] = $district2;
        }
        if ($district3 > 0) {
            $params['district3'] = $district3;
        }
        if ($category1 > 0) {
            $params['category1'] = $category1;
        }
        if ($category2 > 0) {
            $params['category2'] = $category2;
        }
        if ($category3 > 0) {
            $params['category3'] = $category3;
        }
        if($listtype=='emergency'){
            $params['emergency'] = 1;
        }
        if ($minwage > 0) {
            $params['minwage'] = $minwage;
        }
        if ($maxwage > 0) {
            $params['maxwage'] = $maxwage;
        }
        if ($trade > 0) {
            $params['trade'] = $trade;
        }
        if ($scale > 0) {
            $params['scale'] = $scale;
        }
        if ($nature > 0) {
            $params['nature'] = $nature;
        }
        $params['education'] = -1;
        $params['experience'] = -1;
        if ($tag != '') {
            $params['tag'] = $tag;
            $selectedTagArr = explode("_",$tag);
        }
        if ($settr > 0) {
            $params['settr'] = $settr;
        }
        if ($sort != '') {
            $params['sort'] = $sort;
        }
        if ($famous > 0) {
            $params['famous'] = $famous;
        }
        if ($license > 0) {
            $params['license'] = $license;
        }
        if ($filter_apply > 0 && $this->visitor!==null && $this->visitor['utype']==2) {
            $params['filter_apply_uid'] = $this->visitor['uid'];
        }
        $show_mask = 0;
        $params['count_total'] = 1;
        $params['current_page'] = $current_page;
        $params['pagesize'] = $pagesize;
        $instance = new \app\common\lib\JobSearchEngine($params);
        $searchResult = $instance->run();
        $pagerHtml = $searchResult['items']->render();
        $return['items'] = $this->get_datalist($searchResult['items']);
        $return['total'] = $searchResult['total'];
        $return['total_page'] = $searchResult['total_page'];

        if($district2>0){
            $district_level = 3;
            $category_district = model('CategoryDistrict')->getCache($district2);
        }else if($district1>0){
            $district_level = 2;
            $category_district = model('CategoryDistrict')->getCache($district1);
        }else {
            $district_level = 1;
            $category_district = model('CategoryDistrict')->getCache('0');
        }
        $options_district = [];
        foreach ($category_district as $key => $value) {
            if($district_level==1){
                $params = ['d1'=>$key,'d2'=>null,'d3'=>null];
            }else if($district_level==2){
                $params = ['d1'=>$district1,'d2'=>$key,'d3'=>null];
            }else if($district_level==3){
                $params = ['d1'=>$district1,'d2'=>$district2,'d3'=>$key];
            }

            $arr['id'] = $key;
            $arr['url'] = P($params);
            $arr['text'] = $value;
            $options_district[] = $arr;
        }


        if($category2>0){
            $category_level = 3;
            $category_category = model('CategoryJob')->getCache($category2);
        }else if($category1>0){
            $category_level = 2;
            $category_category = model('CategoryJob')->getCache($category1);
        }else {
            $category_level = 1;
            $category_category = model('CategoryJob')->getCache('0');
        }
        $options_categoryjob = [];
        foreach ($category_category as $key => $value) {
            if($category_level==1){
                $params = ['c1'=>$key,'c2'=>null,'c3'=>null];
            }else if($category_level==2){
                $params = ['c1'=>$category1,'c2'=>$key,'c3'=>null];
            }else if($category_level==3){
                $params = ['c1'=>$category1,'c2'=>$category2,'c3'=>$key];
            }

            $arr['id'] = $key;
            $arr['url'] = P($params);
            $arr['text'] = $value;
            $options_categoryjob[] = $arr;
        }

        $category_all = model('Category')->getCache('');
        $options_exp = model('BaseModel')->map_experience;
        $options_tag = $category_all['QS_jobtag'];
        $options_sex = model('Job')->map_sex;
        $options_trade = $category_all['QS_trade'];
        $options_edu = model('BaseModel')->map_education;
        $options_scale = $category_all['QS_scale'];
        $options_nature = model('Job')->map_nature;

        $hotjob_list = $this->getHotjob();

        $category_district_data = model('CategoryDistrict')->getCache();
        $category_job_data = model('CategoryJob')->getCache();
        $seoData['keyword'] = $keyword;
        if($district3>0){
            $seoData['citycategory'] = isset($category_district_data[$district3]) ? $category_district_data[$district3] : '';
        }else if($district2>0){
            $seoData['citycategory'] = isset($category_district_data[$district2]) ? $category_district_data[$district2] : '';
        }else if($district1>0){
            $seoData['citycategory'] = isset($category_district_data[$district1]) ? $category_district_data[$district1] : '';
        }else{
            $seoData['citycategory'] = '';
        }
        if($category3>0){
            $seoData['jobcategory'] = isset($category_job_data[$category3]) ? $category_job_data[$category3] : '';
        }else if($category2>0){
            $seoData['jobcategory'] = isset($category_job_data[$category2]) ? $category_job_data[$category2] : '';
        }else if($category1>0){
            $seoData['jobcategory'] = isset($category_job_data[$category1]) ? $category_job_data[$category1] : '';
        }else{
            $seoData['jobcategory'] = '';
        }
        $this->initPageSeo('campusjoblist',$seoData);
        $this->assign('selectedTagArr',$selectedTagArr);
        $this->assign('hotjob_list',$hotjob_list);
        $this->assign('currentPage',$current_page);
        $this->assign('prevPage',$current_page-1);
        $this->assign('nextPage',$current_page+1);
        $this->assign('pagerHtml',$pagerHtml);
        $this->assign('list',$return);
        $this->assign('district_level',$district_level);
        $this->assign('options_district',$options_district);
        $this->assign('category_level',$category_level);
        $this->assign('options_categoryjob',$options_categoryjob);
        $this->assign('options_exp',$options_exp);
        $this->assign('options_tag',$options_tag);
        $this->assign('options_sex',$options_sex);
        $this->assign('options_trade',$options_trade);
        $this->assign('options_edu',$options_edu);
        $this->assign('options_scale',$options_scale);
        $this->assign('options_nature',$options_nature);
        $this->assign('show_mask',$show_mask);
        $this->assign('pageHeader',$this->pageHeader);
        $this->assign('navSelTag','job');
        return $this->fetch('campus/job/index');
    }

    protected function get_datalist($list)
    {
        $result_data_list = $jobid_arr = $comid_arr = $cominfo_arr = $logo_id_arr = $logo_arr = $icon_id_arr = $icon_arr = [];
        foreach ($list as $key => $value) {
            $jobid_arr[] = $value['id'];
            $comid_arr[] = $value['company_id'];
        }
        if ($jobid_arr) {
            if (!empty($comid_arr)) {
                $cominfo_arr = model('Company')
                    ->alias('a')
                    ->join(
                        config('database.prefix') . 'setmeal b',
                        'a.setmeal_id=b.id',
                        'LEFT'
                    )
                    ->where('a.is_display',1)
                    ->where('a.id', 'in', $comid_arr)
                    ->column(
                        'a.id,a.companyname,a.audit,a.logo,a.nature,a.scale,a.trade,a.setmeal_id,b.icon',
                        'a.id'
                    );
                foreach ($cominfo_arr as $key => $value) {
                    $value['logo'] > 0 && ($logo_id_arr[] = $value['logo']);
                    $value['icon'] > 0 && ($icon_id_arr[] = $value['icon']);
                }
                if (!empty($logo_id_arr)) {
                    $logo_arr = model('Uploadfile')->getFileUrlBatch(
                        $logo_id_arr
                    );
                }
                if (!empty($icon_id_arr)) {
                    $icon_arr = model('Uploadfile')->getFileUrlBatch(
                        $icon_id_arr
                    );
                }
            }
            $jids = implode(',', $jobid_arr);
            $field =
                'id,company_id,jobname,minwage,maxwage,negotiable,education,experience,district,addtime,refreshtime';
            $joblist = model('Job')
                ->where('id', 'in', $jids)
                ->orderRaw('field(id,' . $jids . ')')
                ->field($field)
                ->select();
            $category_district_data = model('CategoryDistrict')->getCache();
            foreach ($joblist as $key => $val) {
                $tmp_arr = [];
                $tmp_arr['id'] = $val['id'];
                $tmp_arr['jobname'] = htmlspecialchars_decode($val['jobname'],ENT_QUOTES);
                $tmp_arr['company_id'] = $val['company_id'];
                if (isset($cominfo_arr[$val['company_id']])) {
                    $tmp_arr['companyname'] =
                        htmlspecialchars_decode($cominfo_arr[$val['company_id']]['companyname'],ENT_QUOTES);
                    $tmp_arr['company_logo'] = isset(
                        $logo_arr[$cominfo_arr[$val['company_id']]['logo']]
                    )
                        ? $logo_arr[$cominfo_arr[$val['company_id']]['logo']]
                        : default_empty('logo');
                } else {
                    $tmp_arr['companyname'] = '';
                    $tmp_arr['company_logo'] = '';
                }

                if ($val['district']) {
                    $tmp_arr['district_text'] = isset(
                        $category_district_data[$val['district']]
                    )
                        ? $category_district_data[$val['district']]
                        : '';
                } else {
                    $tmp_arr['district_text'] = '';
                }
                $tmp_arr['wage_text'] = model('BaseModel')->handle_wage(
                    $val['minwage'],
                    $val['maxwage'],
                    $val['negotiable']
                );
                $tmp_arr['refreshtime'] = daterange_format(
                    $val['addtime'],
                    $val['refreshtime']
                );
                $tmp_arr['job_link_url_web'] = url('campus/job/show',['id'=>$tmp_arr['id']]);
                $result_data_list[] = $tmp_arr;
            }
        }
        return $result_data_list;
    }

    /** 校招资讯 */
    public function notice()
    {
        if(is_mobile_request()===true){
            $this->redirect(config('global_config.mobile_domain').'campus/notice');
            exit;
        }
        $keyword = request()->route('keyword/s', '', 'trim');
        $current_page = request()->get('page/d',1,'intval');
        $pagesize = request()->get('pagesize/d',10,'intval');
        $where = ['is_display' => 1];
        if ($keyword != '') {
            $where['title'] = ['like', '%' . $keyword . '%'];
        }

        $list = model('CampusNotice')
            ->order('sort_id desc,id desc')
            ->where($where)
            ->paginate(['list_rows'=>$pagesize,'page'=>$current_page,'type'=>'\\app\\common\\lib\\Pager']);
        $pagerHtml = $list->render();
        foreach ($list as $key => $value) {
            $list[$key]['content'] = strip_tags(htmlspecialchars_decode($value['content'],ENT_QUOTES));
            $list[$key]['content'] = cut_str($list[$key]['content'],200,0,'...');
            $list[$key]['link_url'] = $value['link_url']==''?url('campus/notice/show',['id'=>$value['id']]):$value['link_url'];
        }
        $this->initPageSeo('camnoticelist');
        $this->assign('list',$list);
        $this->assign('pagerHtml',$pagerHtml);
        $this->assign('pageHeader',$this->pageHeader);
        return $this->fetch('campus/notice/index');
    }

    /** 校招资讯详情 */
    public function notice_show()
    {
        $id = request()->route('id/d',0,'intval');
        if (!$id) {
            abort(404,'请选择资讯！');
        }
        if(is_mobile_request()===true){
            $this->redirect(config('global_config.mobile_domain').'campus/notice/'.$id);
            exit;
        }
        $info = model('CampusNotice')
            ->field('id,title,content,addtime,holddate_start,holddate_end,seo_keywords,seo_description')
            ->where('id', $id)
            ->find();
        if(null === $info){
            abort(404, '资讯不存在或已被删除！');
        }
        $info['content'] = htmlspecialchars_decode($info['content'],ENT_QUOTES);
        $info['share_url'] = config('global_config.mobile_domain').'campus/notice/'.$info['id'];
        $seoData['title'] = $info['title'];
        if($info['seo_keywords']!=''){
            $seoData['seo_keywords'] = $info['seo_keywords'];
        }else{
            $seoData['seo_keywords'] = $info['title'];
        }
        if($info['seo_description']!=''){
            $seoData['seo_description'] = $info['seo_description'];
        }else{
            $seoData['seo_description'] = cut_str(strip_tags($info['content']),100);
        }
        model('CampusNotice')->where('id',$id)->setInc('click',1);
        $this->initPageSeo('camnoticeshow',$seoData);
        $this->assign('pageHeader',$this->pageHeader);
        $this->assign('info',$info);
        return $this->fetch('campus/notice/show');
    }

    /** 首页公告列表 */
    protected function get_notice()
    {
        $list = model('CampusNotice')
            ->field('id,title')
            ->order('sort_id desc,id desc')
            ->limit(6)
            ->select();
        foreach ($list as $key => $value) {
            $list[$key] = $value;
        }
        return $list;
    }

    /** 首页院校列表 */
    protected function get_school_list()
    {
        $list = model('CampusSchool')
            ->field('id,name as school_name,logo')
            ->where('display', 1)
            ->order('addtime desc')
            ->select();
        $image_id_arr = $image_list = [];
        foreach ($list as $key => $value) {
            $value['logo'] && ($image_id_arr[] = $value['logo']);
        }
        if (!empty($image_id_arr)) {
            $image_list = model('Uploadfile')->getFileUrlBatch($image_id_arr);
        }
        $preach_count_arr = model('CampusPreach')->group('school_id')->column('school_id,count(*) as num');
        foreach ($list as $key => $value) {
            $value['logo_url'] = isset($image_list[$value['logo']])
                ? $image_list[$value['logo']]
                : '';
            $value['total'] = isset($preach_count_arr[$value['id']]) ? $preach_count_arr[$value['id']] : 0;
            $list[$key] = $value;
        }
        return $list;
    }

    /** 首页双选会列表 */
    protected function get_election()
    {
        $timestamp = time();
        $field =
            'a.id,a.school_id,a.subject,a.address,CASE 
        WHEN a.starttime<=' .
            $timestamp .
            ' AND a.endtime>'.$timestamp.' THEN 2
        WHEN a.starttime>' .
            $timestamp .
            ' THEN 1
        ELSE 0
        END AS score,a.starttime,a.endtime,a.company_count,a.graduate_count,b.name school_name';
        $list = model('CampusElection')
            ->alias('a')
            ->join(config('database.prefix') . 'campus_school b', 'a.school_id=b.id', 'left')
            ->field($field)
            ->where('a.display', 1)
            ->order('score desc,a.starttime desc')
            ->limit(8)
            ->select();
        foreach ($list as $key => $value) {
            $list[$key] = $value;
        }
        return $list;
    }

    /** 职位列表 */
    protected function get_job()
    {
        // 教育经历和工作经历为不限
        $params['education'] = -1;
        $params['experience'] = -1;
        $params['pagesize'] = 18;
        $params['sort'] = 'refreshtime';
        $instance = new \app\common\lib\JobSearchEngine($params);

        $searchResult = $instance->run();
        $list = $this->get_datalist($searchResult['items']);
        return $list;
    }

    /** 热门职位 */
    protected function getHotjob($id=0){
        $params['count_total'] = 0;
        $params['pagesize'] = 5;
        $params['sort'] = 'emergency';
        $instance = new \app\common\lib\JobSearchEngine($params);
        $runMap = '';
        if($id>0){
            $runMap = 'id!='.$id;
        }
        $searchResult = $instance->run($runMap);
        $list = $this->get_datalist($searchResult['items']);
        return $list;
    }
}