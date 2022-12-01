<?php

namespace app\index\controller;

use app\common\lib\Pager;

class Recruitment extends \app\index\controller\Base {
    public function _initialize() {
        parent::_initialize();
        $this->assign('navSelTag', 'recruitment');
    }
    /** [index 今日招聘列表页] */
    public function index() {
        if (is_mobile_request() === true) {
            $this->redirect(config('global_config.mobile_domain') . 'dailyList', 302);
            exit;
        }
        $field = 'id,subject_name,is_display,click,addtime,refreshtime';

        $where = ['is_display' => 1];
        $list = model('RecruitmentToday')->where($where)->field($field)->order('refreshtime desc')->select();
        foreach ($list as $key => $value) {
            $id_arr[] = $value['id'];
        }
        if (!empty($id_arr)) {
            $company_total_list = model('RecruitmentTodayCompany')
                ->where('recruitment_today_id', 'in', $id_arr)
                ->group('recruitment_today_id')
                ->column('count(*) as num,recruitment_today_id', 'recruitment_today_id');

            $job_total_list = model('JobSearchRtime')
                ->alias('j')
                ->join('RecruitmentTodayCompany r', 'r.company_id=j.company_id', 'left')
                ->where(['r.recruitment_today_id' => ['in', $id_arr]])
                ->group('recruitment_today_id')
                ->column('count(*) as num,recruitment_today_id', 'recruitment_today_id');
        } else {
            $company_total_list = [];
            $job_total_list = [];
        }
        foreach ($list as $key => $value) {
            $value['company_num'] = isset($company_total_list[$value['id']])
                ? $company_total_list[$value['id']]
                : 0;
            $value['job_num'] = isset($job_total_list[$value['id']])
                ? $job_total_list[$value['id']]
                : 0;
            $value['logo_src'] = config('global_config.sitedomain') . config('global_config.sitedir') . SYS_UPLOAD_DIR_NAME . '/recruitmentToday/' . ($value['id'] % 10) . '/' . $value['id'] . '/' . $value['id'] . '_list_logo.jpg';

            $list[$key] = $value;
        }

        $this->initPageSeo('jobfairollist');
        $pageHeader = $this->pageHeader;
        $pageHeader['title'] =  '今日招聘 - ' . $pageHeader['og']['title'];
        $this->assign('pageHeader', $pageHeader);
        $this->assign('list', $list);
        $this->assign('global_config', config('global_config'));
        return $this->fetch('list');
    }
    /** 今日招聘详情 */
    public function show() {
        $id = request()->route('id/d', 0, 'intval');
        if (is_mobile_request() === true) {
            $this->redirect(config('global_config.mobile_domain') . 'dailyDetail?id=' . $id, 302);
            exit;
        }
        if (!$id) {
            $this->ajaxReturn(500, '请选择今日招聘');
        }

        $info = model('RecruitmentToday')->field('id,subject_name,is_display,click,addtime,refreshtime')->where('id', $id)->find();
        if (null === $info) {
            $this->ajaxReturn(500, '请选择今日招聘');
        }
        $info = $info->toArray();
        model('RecruitmentToday')->where('id', $id)->setInc('click', 1);

        $info['total_company'] = model('RecruitmentTodayCompany')
            ->where('recruitment_today_id', $id)
            ->count();
        $info['total_job'] = model('JobSearchRtime')
            ->alias('a')
            ->join(config('database.prefix') . 'recruitment_today_company b', 'a.company_id=b.company_id', 'left')
            ->where('b.recruitment_today_id', $id)
            ->count();
        $info['total_amount'] = model('Job')
            ->alias('a')
            ->join(config('database.prefix') . 'recruitment_today_company b', 'a.company_id=b.company_id', 'left')
            ->where('b.recruitment_today_id', $id)
            ->where('a.is_display', 1)
            ->where('a.audit', 1)
            ->sum('amount');
        $pageHeader = $this->pageHeader;
        $pageHeader['title'] =  $info['subject_name'] . ' - ' . $pageHeader['title'];
        $this->assign('pageHeader', $pageHeader);
        $this->assign('info', $info);
        $this->assign('global_config', config('global_config'));
        $this->assign('todayLogo', config('global_config.sitedomain') . config('global_config.sitedir') . SYS_UPLOAD_DIR_NAME . '/recruitmentToday/' . ($id % 10) . '/' . $id . '/' . $id . '_logo.jpg');
        return $this->fetch('show');
    }
    /** 参会企业列表 */
    public function comlist() {
        $recruitment_today_id = input('get.recruitment_today_id/d', 0, 'intval');
        $keyword = input('get.keyword/s', '', 'trim');
        $type = input('get.type/d', 0, 'intval'); //类型 1-职位 2-企业
        $current_page = input('get.page/d', 1, 'intval');
        $pagesize = input('get.pagesize/d', 25, 'intval');

        if (!$recruitment_today_id) {
            $this->ajaxReturn(500, '请选择今日招聘');
        }
        $where = ['a.recruitment_today_id' => $recruitment_today_id];
        $job_where = [];
        if ($keyword != '') {
            if ($type == 1) {
                $job_where['j.jobname'] = ['like', "%{$keyword}%"];
                $where['j.jobname'] = ['like', "%{$keyword}%"];
            } elseif ($type == 2) {
                $where['b.companyname'] = ['like', "%{$keyword}%"];
            }
        }
        $list = model('RecruitmentTodayCompany')
            ->alias('a')
            ->field('a.addtime,a.refreshtime,a.sort_id,b.*,c.name as trade_name,d.name as scale_name')
            ->join(config('database.prefix') . 'company b', 'a.company_id=b.id', 'left')
            ->join(config('database.prefix') . 'category c', 'b.trade=c.id', 'left')
            ->join(config('database.prefix') . 'category d', 'b.scale=d.id', 'left')
            ->join(config('database.prefix') . 'job j', 'j.company_id=b.id', 'left')
            ->where($where)
            ->group('a.id');


        $list = $list->order('a.sort_id desc,a.refreshtime desc,a.id desc')->page($current_page, $pagesize)->select();
        $job_list = $comid_arr   = $logo_arr = $logo_id_arr  = [];
        foreach ($list as $key => $value) {
            $comid_arr[] = $value['id'];
            $value['logo'] > 0 && ($logo_id_arr[] = $value['logo']);
        }
        if (!empty($logo_id_arr)) {
            $logo_arr = model('Uploadfile')->getFileUrlBatch($logo_id_arr);
        }
        if (!empty($comid_arr)) {
            $job_data = model('Job')
                ->alias('j')
                ->join(config('database.prefix') . 'category_district b', 'j.district1 =b.id', 'left')
                ->join(config('database.prefix') . 'category_district c', 'j.district2 =b.id', 'left')
                ->join(config('database.prefix') . 'category_district d', 'j.district3 =b.id', 'left')
                ->where(['j.audit' => 1, 'j.is_display' => 1, 'j.company_id' => ['in', $comid_arr]])
                ->where($job_where)
                ->column('j.id,j.company_id,j.jobname,j.minwage,j.maxwage,j.negotiable,b.name as district1_name,c.name as district2_name,d.name as district3_name,j.experience,j.education', 'j.id');
            foreach ($job_data as $key => $value) {
                if (isset($job_list[$value['company_id']]) && count($job_list[$value['company_id']]) >= 3) {
                    continue;
                }
                $job_tmp_arr = [];
                $job_tmp_arr['id'] = $value['id'];
                $job_tmp_arr['jobname'] = $value['jobname'];
                $job_tmp_arr['district_name'] = $value['district1_name'];
                if (!empty($value['district2_name'])) {
                    $job_tmp_arr['district_name'] .= ' ' . $job_tmp_arr['district2_name'];
                }
                if (!empty($value['district3_name'])) {
                    $job_tmp_arr['district_name'] .= ' ' . $job_tmp_arr['district3_name'];
                }
                $basemodel = new BaseModel();
                $job_tmp_arr['education'] = empty($value['education']) ? '不限学历' : $basemodel->map_education[$value['education']];
                $job_tmp_arr['experience'] = empty($value['experience']) ? '无经验' : $basemodel->map_experience[$value['experience']];
                $job_tmp_arr['wage_text'] = model('BaseModel')->handle_wage(
                    $value['minwage'],
                    $value['maxwage'],
                    $value['negotiable']
                );
                $job_list[$value['company_id']][] = $job_tmp_arr;
            }
        }
        $returnlist = [];
        foreach ($list as $key => $value) {
            $tmp_arr = [];
            $tmp_arr['id'] = $value['id'];
            $tmp_arr['companyname'] = $value['companyname'];
            $tmp_arr['company_url'] = url('index/company/show', ['id' => $value['id']]);
            $tmp_arr['scale_name'] = $value['scale_name'];
            $tmp_arr['trade_name'] = $value['trade_name'];
            $tmp_arr['job_num'] = model('Job')
                ->where(['company_id' => $value['id'], 'is_display' => 1, 'audit' => 1])
                ->count();
            $tmp_arr['logo_src'] = isset($logo_arr[$value['logo']])
                ? $logo_arr[$value['logo']]
                : default_empty('logo');

            $tmp_arr['joblist'] = isset($job_list[$value['id']])
                ? $job_list[$value['id']]
                : [];


            $returnlist[] = $tmp_arr;
        }
        $return['items'] = $returnlist;
        $not_company_id = [];
        $total = model('RecruitmentTodayCompany')
            ->alias('a')
            ->field('a.addtime,a.refreshtime,a.sort_id,b.*')
            ->join(config('database.prefix') . 'company b', 'a.company_id=b.id', 'left')
            ->join(config('database.prefix') . 'job j', 'j.company_id=b.id', 'left')
            ->where($where)
            ->count();
        $return['total'] = $total;
        $return['total_page'] = $total == 0 ? 0 : ceil($total / $pagesize);;
        $this->ajaxReturn(200, '获取数据成功', $return);
    }
}
