<?php

namespace app\apiadmin\controller;

use Think\Db;

class RecruitmentToday extends \app\common\controller\Backend {
    public function _initialize() {
        parent::_initialize();
    }
    /** 今日招聘列表 */
    public function index() {
        $where = [];
        $current_page = input('get.page/d', 1, 'intval');
        $pagesize = input('get.pagesize/d', 10, 'intval');

        $total = model('RecruitmentToday');

        $total = $total->where($where)->count();
        $list = model('RecruitmentToday');

        $list = $list->field('id,subject_name,is_display,click,addtime,refreshtime')->where($where)->order('id desc')->page($current_page . ',' . $pagesize)->select();
        $id_arr = [];
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
                ->where(['recruitment_today_id' => ['in', $id_arr]])
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
            $list[$key] = $value;
        }

        $return['items'] = $list;
        $return['total'] = $total;
        $return['current_page'] = $current_page;
        $return['pagesize'] = $pagesize;
        $return['total_page'] = ceil($total / $pagesize);
        $this->ajaxReturn(200, '获取数据成功', $return);
    }
    public function add() {
        $input_data = [
            'subject_name' => input('post.subject_name/s', '', 'trim'),
            'company_id' => input('post.company_id/s', '', 'trim'),
            'template_id' => input('post.is_display/d', 0, 'intval'),
        ];
        $time = time();
        $recruitmentToday = [
            'addtime' => $time,
            'refreshtime' => $time,
            'subject_name' => $input_data['subject_name'],
            'is_display' => 1,
            'template_id' => $input_data['template_id']
        ];

        try {
            Db::startTrans();
            if (isset($recruitmentToday['subject_name']) && mb_strlen($recruitmentToday['subject_name'], 'UTF-8') > 8) {
                $this->ajaxReturn(400, '主题名称最多可输入8个字');
            }
            $id = model('RecruitmentToday')
                ->validate(true)
                ->allowField(true)
                ->insertGetId($recruitmentToday);
            if (!$id) {
                Db::rollback();
                $this->ajaxReturn(500, model('RecruitmentToday')->getError());
            }
            if (!empty($input_data['company_id'])) {
                $company_id = explode(',', $input_data['company_id']);
                $recruitment_today_id = $id;
                foreach ($company_id as $k => $v) {
                    $arr[] = [
                        'addtime' => $time,
                        'refreshtime' => $time,
                        'recruitment_today_id' => $recruitment_today_id,
                        'company_id' => $v,
                        'sort_id' => 0,
                        'data_sources' => 2
                    ];
                }
                model('RecruitmentTodayCompany')
                    ->validate(true)
                    ->allowField(true)
                    ->insertAll($arr);
            }
            $this->todayLogo($id);
            Db::commit();
        } catch (\Exception $e) {
            $this->ajaxReturn(500, $e->getMessage());
        }

        model('AdminLog')->record(
            '添加今日招聘。今日招聘ID【' .
                $id .
                '】;今日招聘主题名称【' .
                $input_data['subject_name'] .
                '】',
            $this->admininfo
        );
        $this->ajaxReturn(200, '保存成功');
    }
    public function edit() {
        $id = input('get.id/d', 0, 'intval');
        if ($id) {
            $info = model('RecruitmentToday')->find($id);
            if (!$info) {
                $this->ajaxReturn(500, '数据获取失败');
            }
            $info = $info->toArray();
            $info['company_num'] = $company_total_list = model('RecruitmentTodayCompany')
                ->where(['recruitment_today_id' => $id])->count();
            $this->ajaxReturn(200, '获取数据成功', $info);
        } else {
            $input_data = [
                'subject_name' => input('post.subject_name/s', '', 'trim'),
                'is_display' => input('post.is_display/d', 0, 'intval'),
                'click' => input('post.click/d', 0, 'intval'),
                'refreshtime' => time()
            ];

            $id = input('post.id/d', 0, 'intval');
            if (
                false ===
                model('RecruitmentToday')
                ->validate(true)
                ->allowField(true)
                ->save($input_data, ['id' => $id])
            ) {
                $this->ajaxReturn(500, model('RecruitmentToday')->getError());
            }
            model('AdminLog')->record(
                '编辑今日招聘。今日招聘ID【' .
                    $id .
                    '】;今日招聘主题名称【' .
                    $input_data['subject_name'] .
                    '】',
                $this->admininfo
            );
            $this->todayLogo($id);
            $this->ajaxReturn(200, '保存成功');
        }
    }
    /** 删除今日招聘 */
    public function delete() {
        $id = input('get.id/a');

        if (empty($id)) {
            $this->ajaxReturn(500, '请选择今日招聘');
        }
        try {
            Db::startTrans();
            model('RecruitmentToday')->whereIn('id', $id)->delete();
            model('RecruitmentTodayCompany')->whereIn('recruitment_today_id', $id)->delete();
            Db::commit();
            model('AdminLog')->record('删除今日招聘。今日招聘ID【' . implode(",", $id) . '】', $this->admininfo);
            $this->ajaxReturn(200, '删除成功');
        } catch (\Exception $e) {
            Db::rollback();
            $this->ajaxReturn(500, $e->getMessage());
        }
    }
    /** 设置状态 */
    public function setDisplay() {
        $id = input('post.id/d', 0, 'intval');
        if ($id == 0) {
            $this->ajaxReturn(500, '请选择数据');
        }
        $is_display = input('post.is_display/d', 1, 'intval');

        model('RecruitmentToday')->where('id', $id)->update(['is_display' => $is_display]);
        model('AdminLog')->record(
            '将今日招聘显示状态变更为【' .
                ($is_display == 1 ? '显示' : '不显示') .
                '】。今日招聘ID【' .
                $id .
                '】',
            $this->admininfo
        );
        $this->ajaxReturn(200, '设置成功');
    }

    /** 场景码 */
    public function sceneCode() {
        $input_data = [
            'paramid' => input('get.paramid/d', 0, 'intval'),
        ];

        $input_data['uuid'] = uuid();
        $mobile_page = 'dailyDetail?id=' . $input_data['paramid'];

        $locationUrl = config('global_config.mobile_domain') . str_replace(":id", $input_data['paramid'], $mobile_page) . '?scene_uuid=' . $input_data['uuid'];
        $locationUrl = urlencode($locationUrl);
        $qrcodeSrc = config('global_config.sitedomain') . config('global_config.sitedir') . 'v1_0/home/qrcode/index?type=normal&url=' . $locationUrl;

        $this->ajaxReturn(200, '获取数据成功', $qrcodeSrc);
    }


    /**
     * 短链接
     * */
    public function shortLink() {
        $paramid = input('get.paramid/d', 0, 'intval');

        $item['url_o'] = url('/dailyDetail/' . $paramid);
        $return[] = $item;
        $shortUrl = new \app\common\model\ShortUrl();
        $shortUrl->genCode4Array($return, 'url_o', 'url', '社群推文营销');

        $this->ajaxReturn(200, '获取数据成功', isset($return[0]) ? $return[0] : []);
    }


    /**
     * 海报
     * */
    public function poster() {
        $paramid = input('get.paramid/d', 0, 'intval');
        $poster = new \app\common\lib\Poster;
        $result = $poster->recruitmentToday(1, $paramid);
        $this->ajaxReturn(200, '获取成功', $result);
    }

    public function companySearch() {
        $keyword = input('get.keyword/s', '', 'trim');
        $list = [];
        $where = [];

        $where['c.companyname'] = ['neq', ''];
        if ($keyword != '') {
            $datalist = model('Company')
                ->alias('c')
                ->join('JobSearchRtime j', 'j.company_id=c.id', 'left')
                ->where($where)
                ->where("c.id='{$keyword}' or c.companyname like '%{$keyword}%'")
                ->field('c.id,c.companyname')
                ->order('c.refreshtime desc')
                ->group('c.id')
                ->select();
            $comid_arr = [];
            foreach ($datalist as $key => $value) {
                $comid_arr[] = $value['id'];
            }
            $jobdata = [];
            if (!empty($comid_arr)) {
                $jobdata = model('JobSearchRtime')->where('company_id', 'in', $comid_arr)->column('company_id,id,uid');
            }
            foreach ($datalist as $key => $value) {
                $arr['id'] = $value['id'];
                $arr['companyname'] = $value['companyname'];
                $arr['has_job'] = isset($jobdata[$value['id']]) ? 1 : 0;
                $list[] = $arr;
            }
        }
        $this->ajaxReturn(200, '获取数据成功', ['items' => $list]);
    }

    public function todayLogo($id) {
        $poster = new \app\common\lib\Poster;
        $pc_result = $poster->recruitmentTodayLogo($id);
        $mobile_result = $poster->recruitmentTodayLogo($id, 'mobile');
        $mobile_result = $poster->recruitmentTodayLogo($id, 'list');
        return true;
    }
}
