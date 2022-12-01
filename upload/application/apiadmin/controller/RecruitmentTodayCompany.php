<?php

namespace app\apiadmin\controller;

use Think\Db;

class RecruitmentTodayCompany extends \app\common\controller\Backend {
    public function _initialize() {
        parent::_initialize();
    }
    /** 今日招聘企业列表 */
    public function index() {
        $current_page = input('get.page/d', 1, 'intval');
        $pagesize = input('get.pagesize/d', 10, 'intval');
        $id = input('get.recruitment_today_id/d', 0, 'intval');

        $total = model('RecruitmentTodayCompany');
        $where = ['t.recruitment_today_id' => $id];

        $total = $total->alias('t')->where($where)->count();
        $list = model('RecruitmentTodayCompany');

        $list = $list->field('t.id,t.recruitment_today_id,t.company_id,c.companyname as company_name,t.addtime,t.refreshtime,t.data_sources,t.sort_id,c.audit,c.is_display,m.setmeal_id,s.name as setmeal_name')
            ->alias('t')
            ->join('Company c', 'c.id=t.company_id', 'left')
            ->join('MemberSetmeal m', 'm.uid=c.uid', 'left')
            ->join('Setmeal s', 's.id=m.setmeal_id', 'left')
            ->where($where)
            ->order('t.sort_id desc,t.refreshtime desc')
            ->page($current_page . ',' . $pagesize)->select();
        $id_arr = [];
        foreach ($list as $key => $value) {
            $id_arr[] = $value['company_id'];
        }
        if (!empty($id_arr)) {
            $job_total_list = model('JobSearchRtime')
                ->where('company_id', 'in', $id_arr)
                ->group('company_id')
                ->column('count(*) as num,company_id', 'company_id');
        } else {
            $job_total_list = [];
        }
        foreach ($list as $key => $value) {
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
            'recruitment_today_id' => input('post.recruitment_today_id/d', 0, 'intval'),
            'company_id' => input('post.company_id/d', 0, 'intval'),
            'sort_id' => input('post.sort_id/d', 0, 'intval'),
            'data_sources' => 2
        ];
        $count = model('RecruitmentTodayCompany')->where(['company_id' => $input_data['company_id'], 'recruitment_today_id' => $input_data['recruitment_today_id']])->count();
        if ($count > 0) {
            $this->ajaxReturn(500, '该企业已添加，请勿重复添加');
        }
        $input_data['addtime'] = time();
        $input_data['refreshtime'] = time();
        if (
            false ===
            model('RecruitmentTodayCompany')
            ->validate(true)
            ->allowField(true)
            ->save($input_data)
        ) {
            $this->ajaxReturn(500, model('RecruitmentTodayCompany')->getError());
        }
        model('AdminLog')->record(
            '添加今日招聘企业。今日招聘企业ID【' .
                model('RecruitmentTodayCompany')->id .
                '】;',
            $this->admininfo
        );
        $this->ajaxReturn(200, '保存成功');
    }
    public function edit() {
        $id = input('get.id/d', 0, 'intval');
        if ($id) {
            $info = model('RecruitmentTodayCompany')->find($id);
            if (!$info) {
                $this->ajaxReturn(500, '数据获取失败');
            }
            $info = $info->toArray();
            $this->ajaxReturn(200, '获取数据成功', $info);
        } else {
            $input_data = [
                'recruitment_today_id' => input('post.recruitment_today_id/d', 0, 'intval'),
                'company_id' => input('post.company_id/d', 0, 'intval'),
                'sort_id' => input('post.sort_id/d', 0, 'intval'),
                'data_sources' => 2
            ];
            $id = input('post.id/d', 0, 'intval');
            if (
                false ===
                model('RecruitmentTodayCompany')
                ->validate(true)
                ->allowField(true)
                ->save($input_data, ['id' => $id])
            ) {
                $this->ajaxReturn(500, model('RecruitmentTodayCompany')->getError());
            }
            model('AdminLog')->record(
                '编辑今日招聘企业。今日招聘企业ID【' .
                    $id .
                    '】',
                $this->admininfo
            );
            $this->ajaxReturn(200, '保存成功');
        }
    }
    /** 删除今日招聘企业 */
    public function delete() {
        $id = input('get.id/s', '', 'trim');
        if (empty($id)) {
            $this->ajaxReturn(500, '请选择今日招聘');
        }
        if (false == model('RecruitmentTodayCompany')->whereIn('id', $id)->delete()) {
            $this->ajaxReturn(500, model('RecruitmentTodayCompany')->getError());
        }
        model('AdminLog')->record('删除今日招聘企业。今日招聘企业ID【' . $id . '】', $this->admininfo);
        $this->ajaxReturn(200, '删除成功');
    }

    /** 设置排序 */
    public function setSort() {
        $id = input('post.id/d', 0, 'intval');
        if ($id == 0) {
            $this->ajaxReturn(500, '请选择数据');
        }
        $sort_id = input('post.sort_id/d', 1, 'intval');

        model('RecruitmentTodayCompany')->where('id', $id)->update(['sort_id' => $sort_id]);
        model('AdminLog')->record(
            '将今日招聘企业排序变更为【' .
                $sort_id .
                '】。今日招聘企业ID【' .
                $id .
                '】',
            $this->admininfo
        );
        $this->ajaxReturn(200, '设置成功');
    }
}
