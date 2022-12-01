<?php

namespace app\apiadmin\controller;

class JobfairExhibitors extends \app\common\controller\Backend {
    public function _initialize() {
        parent::_initialize();
    }
    public function index() {
        $where = [];
        $audit = input('get.audit/d', 0, 'intval');
        $recommend = input('get.recommend/d', 0, 'intval');
        $jobfair_id = input('get.jobfair_id/d', 0, 'intval');
        $settr = input('get.settr/d', 0, 'intval');
        $key_type = input('get.key_type/d', 0, 'intval');
        $keyword = input('get.keyword/s', '', 'trim');
        $current_page = input('get.page/d', 1, 'intval');
        $pagesize = input('get.pagesize/d', 10, 'intval');
        $time = time();
        if ($keyword && $key_type) {
            switch ($key_type) {
                case 1:
                    $where['companyname'] = array('like', '%' . $keyword . '%');
                    break;
                case 2:
                    $where['jobfair_title'] = array('like', '%' . $keyword . '%');
                    break;
            }
        }
        $recommend > 0 && $where['recommend'] = array('eq', $recommend - 1);
        $jobfair_id > 0 && $where['jobfair_id'] = array('eq', $jobfair_id);
        $audit > 0 && $where['audit'] = array('eq', $audit);
        if ($settr) $where['eaddtime'] = ['gt', strtotime("-" . $settr . " day")];
        $JobfairExhibitorsMod = model('JobfairExhibitors');
        $total = $JobfairExhibitorsMod->where($where)->count();
        $list = $JobfairExhibitorsMod->where($where)->orderRaw('field(audit,2,3,1) desc, id desc')->page($current_page . ',' . $pagesize)->select();
        $count = $JobfairExhibitorsMod->where(array('audit' => 2))->count('id');
        foreach ($list as $key => $val) {
            if (!$val['contact'] || !$val['mobile']) $cid[] = $val['company_id'];
            $list[$key]['company_link'] = url('index/company/show', ['id' => $val['company_id']]);
            $list[$key]['jobfair_link'] = url('index/jobfair/show', ['id' => $val['jobfair_id']]);
            $list[$key]['audit_text'] = model('JobfairExhibitors')->audit_text[$val['audit']];
        }
        if (isset($cid)) {
            $company = model('CompanyContact')->where('comid', 'in', $cid)->column('comid,contact,mobile,telephone');
            foreach ($list as $key => $val) {
                if (isset($company[$val['company_id']])) {
                    $list[$key]['contact'] = $company[$val['company_id']]['contact'];
                    $list[$key]['mobile'] = $company[$val['company_id']]['mobile'] ?: $company[$val['company_id']]['telephone'];
                }
            }
        }
        $return['items'] = $list;
        $return['total'] = $total;
        $return['auditWait'] = $count;
        $return['current_page'] = $current_page;
        $return['pagesize'] = $pagesize;
        $return['total_page'] = ceil($total / $pagesize);
        $this->ajaxReturn(200, '获取数据成功', $return);
    }
    public function add() {
        $data['jobfair_id'] = input('post.jobfair_id/d', 0, 'intval');
        $data['position_id'] = input('post.position_id/d', 0, 'intval');
        $data['comid'] = input('post.comid/d', 0, 'intval');
        $data['recommend'] = input('post.recommend/d', 0, 'intval');
        $data['audit'] = input('post.audit/d', 1, 'intval');
        $data['etype'] = input('post.etype/d', 1, 'intval');
        $data['note'] = input('post.note/s', '', 'trim');
        $reg = model('JobfairExhibitors')->exhibitorsAdd($data, $this->admininfo);
        $this->ajaxReturn($reg['state'] ? 200 : 500, $reg['msg']);
    }
    public function edit() {
        if (request()->isPost()) {
            $input_data = [
                'id' => input('post.id/d', 0, 'intval'),
                'etype' => input('post.etype/d', 0, 'intval'),
                'audit' => input('post.audit/d', 0, 'intval'),
                'recommend' => input('post.recommend/d', 0, 'intval'),
                'note' => input('post.note/s', '', 'trim')
            ];
            $reg = model('JobfairExhibitors')->exhibitorsEdit($input_data, $this->admininfo);
            $this->ajaxReturn($reg['state'] ? 200 : 500, $reg['msg']);
        } else {
            $id = input('get.id/d', 0, 'intval');
            $info = model('JobfairExhibitors')->find($id);
            if (!$info) $this->ajaxReturn(500, '数据获取失败');
            $info = $info->toArray();
            $info['jobfair_link'] = url('index/jobfair/show', ['id' => $info['jobfair_id']]);
            $info['company_link'] = url('index/company/show', ['id' => $info['company_id']]);
            $this->ajaxReturn(200, '获取数据成功', [
                'info' => $info
            ]);
        }
    }
    public function delete() {
        $id = input('post.id/a');
        if (!$id) $this->ajaxReturn(500, '请选择参会企业');
        $reg = model('JobfairExhibitors')->exhibitorsDelete($id, $this->admininfo);
        $this->ajaxReturn($reg['state'] ? 200 : 500, $reg['msg']);
    }
    public function audit() {
        $id = input('post.id/a');
        if (!$id) $this->ajaxReturn(500, '请选择参会企业');
        $audit = input('post.audit/d', 0, 'intval');
        $note = input('post.note/s', '', 'trim');
        $reg = model('JobfairExhibitors')->exhibitorsAudit($id, $audit, $note, $this->admininfo);
        $this->ajaxReturn($reg['state'] ? 200 : 500, $reg['msg']);
    }
    public function recommend() {
        $id = input('post.id/a');
        if (!$id) $this->ajaxReturn(500, '请选择参会企业');
        $recommend = input('post.recommend/d', 0, 'intval');
        $reg = model('JobfairExhibitors')->exhibitorsRecommend($id, $recommend, $this->admininfo);
        $this->ajaxReturn($reg['state'] ? 200 : 500, $reg['msg']);
    }
    public function getCompany() {
        $where = [];
        $key = input('get.key/s', '', 'trim');
        $type = input('get.type/s', '', 'trim');
        switch ($type) {
            case 'companyname':
                $where['companyname'] = ['like', '%' . $key . '%'];
                break;
            case 'uid':
                $where['uid'] = intval($key);
                break;
        }
        $list = model('Company')->field('id,companyname,addtime,refreshtime')->where($where)->limit(30)->select();
        foreach ($list as $key => $val) {
            $list[$key]['company_link'] = url('index/company/show', ['id' => $val['id']]);
        }
        $this->ajaxReturn(200, '获取成功', ['items' => $list]);
    }
    public function getPosition() {
        $jobfair_id = input('get.jobfair_id/d', 0, 'intval');
        if (!$jobfair_id) $this->ajaxReturn(500, '请选择招聘会');
        $list = model('JobfairPosition')->field('id,position')->where(['jobfair_id' => $jobfair_id, 'status' => 0])->select();
        $this->ajaxReturn(200, '获取成功', ['items' => $list]);
    }
}
