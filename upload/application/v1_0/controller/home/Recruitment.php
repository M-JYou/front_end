<?php

namespace app\v1_0\controller\home;

use app\common\model\BaseModel;
use Think\Db;

class Recruitment extends \app\v1_0\controller\common\Base {
    public function _initialize() {
        parent::_initialize();
    }

    /** 今日招聘列表页 */
    public function index() {
        $current_page = input('get.page/d', 1, 'intval');
        $pagesize = input('get.pagesize/d', 10, 'intval');
        $field = 'id,subject_name,is_display,click,addtime,refreshtime';

        $where = ['is_display' => 1];
        $list = model('RecruitmentToday')->where($where)->field($field)->order('refreshtime desc')->page($current_page, $pagesize)->select();

        $jobfair_id_arr = $thumb_arr = $thumb_id_arr = [];
        foreach ($list as $key => $value) {
            $jobfair_id_arr[] = $value['id'];
        }

        foreach ($list as $key => $value) {
            $id_arr[] = $value['id'];
        }
        if (!empty($id_arr)) {
            $company_total_list = model('RecruitmentTodayCompany')
                ->where('recruitment_today_id', 'in', $id_arr)
                ->group('recruitment_today_id')
                ->column('count(*) as num,recruitment_today_id', 'recruitment_today_id');
            $job_total_list = model('RecruitmentTodayCompany')
                ->alias('r')
                ->join('JobSearchRtime j', 'r.company_id=j.company_id')
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
        }

        $total = model('RecruitmentToday')->where($where)->count();
        $return['items'] = $list;
        $return['total'] = $total;
        $return['total_page'] = $total == 0 ? 0 : ceil($total / $pagesize);
        $return['headert_logo'] = config('global_config.sitedomain') . config('global_config.sitedir') . SYS_UPLOAD_DIR_NAME . '/recruitmentToday/mobileTodayHeader.jpg';
        $this->ajaxReturn(200, '获取数据成功', $return);
    }

    /** 今日招聘详情 */
    public function show() {
        $id = input('get.id/d', 1, 'intval');

        if (!$id) {
            $this->ajaxReturn(500, '请选择今日招聘');
        }
        $info = model('RecruitmentToday')->field('id,subject_name,is_display,click,addtime,refreshtime')->where('id', $id)->find();
        if (null === $info) {
            $this->ajaxReturn(500, '请选择今日招聘');
        }
        $info = $info->toArray();


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
        model('RecruitmentToday')->where('id', $id)->setInc('click', 1);

        $info['logo'] = config('global_config.sitedomain') . config('global_config.sitedir') . SYS_UPLOAD_DIR_NAME . '/recruitmentToday/' . ($id % 10) . '/' . $id . '/' . $id . '_mobile_logo.jpg';
        $this->ajaxReturn(200, '获取数据成功', $info);
    }

    /** 参会企业列表 */
    public function comlist() {
        $recruitment_today_id = input('get.recruitment_today_id/d', 0, 'intval');
        $keyword = input('get.keyword/s', '', 'trim');
        $type = input('get.type/s', '', 'trim'); //类型 1-职位 2-企业
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
                $where['j.is_display'] = 1;
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

        $job_list = $comid_arr = $logo_arr = $logo_id_arr = [];
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
                ->join(config('database.prefix') . 'category_district c', 'j.district2 =c.id', 'left')
                ->join(config('database.prefix') . 'category_district d', 'j.district3 =d.id', 'left')
                ->where(['j.company_id' => ['in', $comid_arr], 'j.is_display' => 1, 'audit' => 1])
                ->where($job_where)
                ->order('updatetime', 'desc')
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
                    $job_tmp_arr['district_name'] .= ',' . $value['district2_name'];
                }
                if (!empty($value['district3_name'])) {
                    $job_tmp_arr['district_name'] .= ',' . $value['district3_name'];
                }
                $district_name = explode(',', $job_tmp_arr['district_name']);
                if (count($district_name) == 3) {
                    unset($district_name[0]);
                    $job_tmp_arr['district_name'] = implode(' ', $district_name);
                }
                $basemodel = new BaseModel();
                $job_tmp_arr['education'] = isset($basemodel->map_education[$value['education']]) ? $basemodel->map_education[$value['education']] : '不限学历';
                $job_tmp_arr['experience'] = isset($basemodel->map_experience[$value['experience']]) ? $basemodel->map_experience[$value['experience']] : '无经验';
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
            ->group('a.id')->select();
        $total = count($total);
        $return['total'] = $total;
        $return['total_page'] = $total == 0 ? 0 : ceil($total / $pagesize);
        $this->ajaxReturn(200, '获取数据成功', $return);
    }

    /** 职位列表 */
    public function joblist() {
        $recruitment_today_id = input('get.recruitment_today_id/d', 0, 'intval');
        $keyword = input('get.keyword/s', '', 'trim');
        $current_page = input('get.page/d', 1, 'intval');
        $pagesize = input('get.pagesize/d', 10, 'intval');
        if (!$recruitment_today_id) {
            $this->ajaxReturn(500, '请选择今日招聘');
        }
        $where = [
            'a.recruitment_today_id' => $recruitment_today_id,
            'j.id' => ['not null', '']
        ];
        $job_where = [];
        if ($keyword != '') {
            $job_where['j.jobname'] = ['like', "%{$keyword}%"];
            $where['j.jobname'] = ['like', "%{$keyword}%"];
        }
        $list = model('RecruitmentTodayCompany')
            ->alias('a')
            ->field('a.addtime,a.refreshtime,a.sort_id,b.refreshtime,b.id as company_id,b.district,j.id,j.jobname,j.minwage,j.maxwage,j.negotiable,j.education,j.experience')
            ->join(config('database.prefix') . 'company b', 'a.company_id=b.id', 'left')
            ->join(config('database.prefix') . 'JobSearchRtime j', 'j.company_id=b.id', 'left')
            ->where($where);
        if ($keyword != '') {
            $list = $list->where('j.jobname', 'like', '%' . $keyword . '%');
        }
        $list = $list->order('b.refreshtime desc,b.id desc')->page($current_page, $pagesize)->select();
        $comid_arr = $cominfo_arr = $logo_arr = $logo_id_arr = $icon_id_arr = $icon_arr = $cs_id_arr = $cs_arr = [];

        foreach ($list as $key => $value) {
            $comid_arr[] = $value['company_id'];
        }
        if (!empty($comid_arr)) {
            $cominfo_arr = model('Company')
                ->alias('a')
                ->join(
                    config('database.prefix') . 'setmeal b',
                    'a.setmeal_id=b.id',
                    'LEFT'
                )
                ->join(config('database.prefix') . 'customer_service c', 'a.cs_id=c.id', 'LEFT')
                ->where('a.id', 'in', $comid_arr)
                ->column(
                    'a.id,a.companyname,a.audit,a.logo,a.setmeal_id,b.icon,c.wx_qrcode',
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
            if (!empty($cs_id_arr)) {
                $cs_arr = model('Uploadfile')->getFileUrlBatch($cs_id_arr);
            }
        }

        $category_district_data = model('CategoryDistrict')->getCache();
        $returnlist = [];
        foreach ($list as $key => $value) {
            $tmp_arr = [];
            $tmp_arr['id'] = $value['id'];
            $tmp_arr['jobname'] = $value['jobname'];
            $tmp_arr['job_url'] = url('index/job/show', ['id' => $value['company_id']]);
            if (isset($cominfo_arr[$value['company_id']])) {
                $tmp_arr['id'] = $value['id'];
                $tmp_arr['companyname'] =
                    $cominfo_arr[$value['company_id']]['companyname'];
                $tmp_arr['company_audit'] =
                    $cominfo_arr[$value['company_id']]['audit'];
                $tmp_arr['company_logo'] = isset(
                    $logo_arr[$cominfo_arr[$value['company_id']]['logo']]
                )
                    ? $logo_arr[$cominfo_arr[$value['company_id']]['logo']]
                    : default_empty('logo');
            } else {
                $tmp_arr['companyname'] = '';
                $tmp_arr['company_audit'] = 0;
                $tmp_arr['company_logo'] = '';
                $tmp_arr['setmeal_icon'] = '';
                $tmp_arr['qrcode_src'] = '';
            }

            if ($value['district']) {
                $tmp_arr['district_text'] = isset(
                    $category_district_data[$value['district']]
                )
                    ? $category_district_data[$value['district']]
                    : '';
            } else {
                $tmp_arr['district_text'] = '';
            }
            $tmp_arr['wage_text'] = model('BaseModel')->handle_wage(
                $value['minwage'],
                $value['maxwage'],
                $value['negotiable']
            );

            $tmp_arr['education_text'] = isset(
                model('BaseModel')->map_education[$value['education']]
            )
                ? model('BaseModel')->map_education[$value['education']]
                : '学历不限';
            $tmp_arr['experience_text'] = isset(
                model('BaseModel')->map_experience[$value['experience']]
            )
                ? model('BaseModel')->map_experience[$value['experience']]
                : '经验不限';


            $returnlist[] = $tmp_arr;
        }

        $return['items'] = $returnlist;
        $total = model('RecruitmentTodayCompany')
            ->alias('a')
            ->field('a.id')
            ->join(config('database.prefix') . 'company b', 'a.company_id=b.id', 'left')
            ->join(config('database.prefix') . 'job j', 'j.company_id=b.id', 'left')
            ->where($where);
        if ($keyword != '') {
            $total = $total->where('j.jobname', 'like', '%' . $keyword . '%');
        }
        $total = $total->count();

        $return['total'] = $total;
        $return['total_page'] = $total == 0 ? 0 : ceil($total / $pagesize);
        $this->ajaxReturn(200, '获取数据成功', $return);
    }
}
