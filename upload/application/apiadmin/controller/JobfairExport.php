<?php

namespace app\apiadmin\controller;

class JobfairExport extends \app\common\controller\Backend {
    public function _initialize() {
        parent::_initialize();
    }
    public function isExhibitors() {
        $jobfair_id = input('get.jobfair_id/d', 0, 'intval');
        if (!$jobfair_id) $this->ajaxReturn(500, '请选择招聘会');
        if (!$jobfair = model('Jobfair')->find($jobfair_id)) $this->ajaxReturn(500, '招聘会不存在');
        $company = model('JobfairExhibitors')->where('jobfair_id', $jobfair['id'])->field('company_id,companyname,position')->select();
        if (!$company) $this->ajaxReturn(500, '暂无报名企业');
        $this->ajaxReturn(200, '获取成功', url('jobfair_export/docxdownload'));
    }
    public function docxdownload() {
        $reg = $this->getExhibitors();
        if (!$reg['state']) $this->ajaxReturn(500, $reg['msg']);
        $company_str = input('get.company/s', '', 'trim');
        $jobs_str = input('get.jobs/s', '', 'trim');
        foreach (explode(',', $company_str) as $val) {
            $company[$val] = 1;
        }
        foreach (explode(',', $jobs_str) as $val) {
            $jobs[$val] = 1;
        }
        $qrcode = input('get.qrcode/s', 'mobile', 'trim');
        $this->assign('list', $reg['data']['items']);
        $this->assign('jobfair', $reg['data']['jobfair']);
        $this->assign('company', $company);
        $this->assign('jobs', $jobs);
        $this->assign('qrcode', $qrcode);
        $this->assign('sitename', config('global_config.sitename'));
        $html = $this->fetch('docxdownload');
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-Type: application/doc");
        $ua = $_SERVER["HTTP_USER_AGENT"];
        $filename = $reg['data']['jobfair']['title'] . '.doc';
        $filename = urlencode($filename);
        $filename = str_replace("+", "%20", $filename);
        if (preg_match("/MSIE/", $ua)) {
            header('Content-Disposition: attachment; filename="' . $filename . '"');
        } else if (preg_match("/Firefox/", $ua)) {
            header('Content-Disposition: attachment; filename*="utf8\'\'' . $filename . '"');
        } else {
            header('Content-Disposition: attachment; filename="' . $filename . '"');
        }
        echo $html;
    }
    public function exhibitors() {
        $reg = $this->getExhibitors();
        if (!$reg['state']) $this->ajaxReturn(500, $reg['msg']);
        $this->ajaxReturn(200, $reg['msg'], $reg['data']);
    }
    protected function getExhibitors() {
        $jobfair_id = input('get.jobfair_id/d', 0, 'intval');
        if (!$jobfair_id) return ['state' => false, 'msg' => '请选择招聘会'];
        if (!$jobfair = model('Jobfair')->find($jobfair_id)) return ['state' => false, 'msg' => '招聘会不存在'];
        $company = model('JobfairExhibitors')->where('jobfair_id', $jobfair['id'])->select();
        if (!$company) return ['state' => false, 'msg' => '暂无报名企业！'];
        foreach ($company as $val) {
            $cids[] = $val['company_id'];
        }
        if (isset($cids)) {
            $jobs = model('Job')->where(['company_id' => ['in', $cids], 'audit' => 1, 'is_display' => 1])->select();
            $category_data = model('Category')->getCache();
            $category_district_data = model('CategoryDistrict')->getCache();
            foreach ($jobs as $val) {
                $val['experience_text'] = $val['experience'] ? model('BaseModel')->map_experience[$val['experience']] : '不限';
                $val['education_text'] = $val['education'] ? model('BaseModel')->map_education[$val['education']] : '不限';
                $val['age_text'] = $val['age_na'] ? '不限' : $val['minage'] . '-' . $val['maxage'] . '岁';
                $val['wage_text'] =  model('BaseModel')->handle_wage($val['minwage'], $val['maxwage'], $val['negotiable']);
                $val['district_text'] = $category_district_data[$val['district']];
                $temp[$val['company_id']][] = $val;
            }
            $class = new \app\common\lib\Wechat;
            foreach ($company as $key => $val) {
                $company[$key]['jobs'] = isset($temp[$val['company_id']]) ? $temp[$val['company_id']] : [];
                $company[$key]['jobs_count'] = count($company[$key]['jobs']);
                $company[$key]['wx_qrcode'] = $class->makeQrcode(['alias' => 'subscribe_company', 'comid' => $val['company_id']]);
                $url = urlencode(config('global_config.mobile_domain') . 'company/' . $val['company_id']);
                $company[$key]['mobile_qrcode'] = url('qrcode/normal') . '?url=' . $url;
            }
        }
        return ['state' => true, 'msg' => '数据获取成功', 'data' => ['items' => $company, 'jobfair' => $jobfair]];
    }
}
