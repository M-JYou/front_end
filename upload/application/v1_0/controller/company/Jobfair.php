<?php

namespace app\v1_0\controller\company;

class Jobfair extends \app\v1_0\controller\common\Base {
    public function _initialize() {
        parent::_initialize();
        $this->checkLogin(1);
        $this->interceptCompanyProfile();
        $this->interceptCompanyAuth();
    }
    /** [index 已报名的招聘会] */
    public function index() {
        $current_page = input('get.page/d', 1, 'intval');
        $pagesize = input('get.pagesize/d', 10, 'intval');
        $exhibitors = model('JobfairExhibitors')->field('jobfair_id,company_addtime,audit,position,contact,mobile')->where('uid', $this->userinfo->uid)->paginate($pagesize)->toArray();
        if ($exhibitors['total']) {
            $audit_arr = [1 => '已预定', 2 => '审核中', 3 => '未通过'];
            foreach ($exhibitors['data'] as $key => $val) {
                $jids[] = $val['jobfair_id'];
                $exhibitors['data'][$key]['jobfair_url'] = url('index/jobfair/show', ['id' => $val['jobfair_id']]);
                $exhibitors['data'][$key]['audit_text'] = $audit_arr[$val['audit']];
            }
            $jobfair = model('Jobfair')->where(['id' => ['in', $jids]])->column('id,title,address,holddate_start,holddate_end,predetermined_start,predetermined_end');
            foreach ($exhibitors['data'] as $key => $val) {
                $exhibitors['data'][$key] = array_merge($val, $jobfair[$val['jobfair_id']]);
            }
        }
        $this->ajaxReturn(200, '获取数据成功', [
            'items' => $exhibitors['data'],
            'total' => $exhibitors['total'],
            'total_page' => $exhibitors['last_page']
        ]);
    }
    public function reserve() {
        $jobfair_id = input('post.jobfair_id/d', 0, 'intval');
        $position_id = input('post.position_id/d', 0, 'intval');
        $contact = input('post.contact/d', '', 'trim');
        $mobile = input('post.mobile/d', '', 'trim');
        if (!$jobfair_id) $this->ajaxReturn(500, '请选择招聘会');
        if (!$position_id) $this->ajaxReturn(500, '请选择展位');
        $uid = $this->userinfo->uid;
        $company = model('Company')->where('uid', $uid)->find();
        !$company && $this->ajaxReturn(200, '为了达到更好的招聘效果，请先完善您的企业资料！', ['type' => 'company']);
        $jobfair = model('Jobfair')->where('id', $jobfair_id)->find();
        if (!$jobfair) $this->ajaxReturn(500, '招聘会不存在或已删除');
        $time = time();
        if ($jobfair['predetermined_start'] <= $time && $time <= $jobfair['predetermined_end']) {
            $exhibitors = model('JobfairExhibitors')->where(['jobfair_id' => $jobfair_id, 'uid' => $uid, 'audit' => ['in', '1,2']])->find();
            if ($exhibitors) $this->ajaxReturn(500, '你已经预定过此招聘会的展位：' . $exhibitors['position'] . '，不能重复预定');
            $position = model('JobfairPosition')->where('id', $position_id)->find();
            if ($position['company_id']) $this->ajaxReturn(500, '该展位已被预订，请重新选择展位');
            if (!$contact || !$mobile) $company_contact = model('CompanyContact')->field('contact,mobile')->where('uid', $uid)->find();
            $setsqlarr = [
                'audit' => 2,
                'uid' => $uid,
                'contact' => $contact ?: $company_contact['contact'],
                'mobile' => $mobile ?: $company_contact['mobile'],
                'companyname' => $company['companyname'],
                'company_id' => $company['id'],
                'company_addtime' => $company['addtime'],
                'eaddtime' => $time,
                'jobfair_id' => $jobfair_id,
                'jobfair_title' => $jobfair['title'],
                'jobfair_addtime' => $jobfair['addtime'],
                'position_id' => $position_id,
                'position' => $position['position'],
                'note' => "【{$company['companyname']}】 预定了招聘会 《{$jobfair['title']}》 的展位。展位编号：" . $position['position']
            ];
            if (model('JobfairExhibitors')->isUpdate(false)->allowField(true)->save($setsqlarr)) {
                $position_save['id'] = $position_id;
                $position_save['jobfair_id'] = $jobfair_id;
                $position_save['company_id'] = $company['id'];
                $position_save['company_uid'] = $uid;
                $position_save['company_name'] = $company['companyname'];
                $position_save['status'] = 2;
                model('JobfairPosition')->allowField(true)->isUpdate(true)->save($position_save);
                $position_save['position'] = $position['position'];
                $position_save['is_reserve'] = 1;
                $this->writeMemberActionLog($this->userinfo->uid, '预定招聘会【招聘会：' . $jobfair['title'] . '】');
                $this->ajaxReturn(200, '招聘会预定成功！', $position_save);
            }
            $this->ajaxReturn(500, '招聘会预定失败！');
        } else {
            $this->ajaxReturn(500, '招聘会已结束预定！');
        }
    }
}
