<?php

namespace app\index\controller;

use app\common\lib\Pager;

class Jobfair extends \app\index\controller\Base {
    public function _initialize() {
        parent::_initialize();
        $this->assign('navSelTag', 'jobfair');
    }
    /** [index 招聘会列表页] */
    public function index() {
        if (is_mobile_request() === true) {
            $this->redirect(config('global_config.mobile_domain') . 'jobfairlist', 302);
            exit;
        }
        $current_page = input('get.page/d', 1, 'intval');
        $pagesize = input('get.pagesize/d', 10, 'intval');
        $s_result = $e_result = $list = [];
        $s_limit = $e_limit = '';
        $s_order = 'holddate_start asc,ordid desc,id desc';
        $e_order = 'holddate_start desc,ordid desc,id desc';
        $time = time();
        $s_where = ['holddate_start' => ['gt', $time], 'display' => 1];
        $e_where = ['holddate_start' => ['elt', $time], 'display' => 1];
        $s_count = model('Jobfair')->where($s_where)->count();
        $e_count = model('Jobfair')->where($e_where)->count();
        $total = $s_count + $e_count;
        $firstRow = abs($current_page - 1) * $pagesize;

        if ($firstRow > $s_count) {
            $e_count && $e_limit = intval($firstRow) - intval($s_count) . ',' . $pagesize;
        } else {
            $s_count && $s_limit = $firstRow . ',' . $pagesize;
            if ($e_count &&  0 < $e_limit = $firstRow + $pagesize - $s_count) {
                $e_limit = '0,' . $e_limit;
            } else {
                $e_limit = 0;
            }
        }
        $thumbs = [];
        $s_limit && $s_result = model('Jobfair')->where($s_where)->order($s_order)->limit($s_limit)->select();
        $e_limit && $e_result = model('Jobfair')->where($e_where)->order($e_order)->limit($e_limit)->select();
        $result = array_merge($s_result, $e_result);
        foreach ($result as $key => $val) {
            $val['thumb'] > 0 && $thumbs[] = $val['thumb'];
            $jobfairs[] = $val['id'];
        }
        $list = [];
        if (isset($jobfairs)) {
            $company_num = model('JobfairExhibitors')->where(['jobfair_id' => ['in', $jobfairs], 'audit' => 1])->group('jobfair_id')->column('jobfair_id,count(id)');
            $thumb_imgs = model('Uploadfile')->getFileUrlBatch($thumbs);
            foreach ($result as $key => $val) {
                $val['thumb'] = isset($thumb_imgs[$val['thumb']]) ? $thumb_imgs[$val['thumb']] : default_empty('jobfair_thumb');
                $val['company_num'] = isset($company_num[$val['id']]) ? $company_num[$val['id']] : 0;
                if ($val['predetermined_start'] > $time) {
                    $val['predetermined'] = 0;
                    $val['predetermined_text'] = '未开始';
                    $val['friendly_tips_text'] = '本场招聘会报名未开始，将在 ' . date('Y-m-d h:i', $val['predetermined_start']) . ' 开始';
                } elseif ($val['predetermined_end'] < $time) {
                    $val['predetermined'] = 2;
                    $val['predetermined_text'] = '已结束';
                    $val['friendly_tips_text'] = '本场招聘会已停止报名，请及时关注最新招聘会';
                } else {
                    $val['predetermined'] = 1;
                    $val['predetermined_text'] = '预定中';
                    $val['friendly_tips_text'] = '本场招聘会正火热报名中，请从速报名';
                    $jids[] = $val['id'];
                }
                if ($val['holddate_start'] > $time) {
                    $val['holddate'] = 0;
                    $val['holddate_text'] = '未开始';
                } elseif ($val['holddate_end'] < $time) {
                    $val['holddate'] = 2;
                    $val['holddate_text'] = '已结束';
                } else {
                    $val['holddate'] = 1;
                    $val['holddate_text'] = '进行中';
                }
                $val['friendly_tips'] = $val['predetermined'];
                $list[] = $val;
            }
        }
        if ($this->visitor && isset($jids)) {
            $exhibitors = model('JobfairExhibitors')->where(['jobfair_id' => ['in', $jids], 'uid' => $this->visitor['uid']])->column('jobfair_id,position,audit');
            foreach ($list as $key => $val) {
                if ($val['predetermined'] == 1 && isset($exhibitors[$val['id']])) {
                    $list[$key]['is_reserve'] = 1;
                    $list[$key]['position'] = $exhibitors[$val['id']]['position'];
                    $list[$key]['reserve_status'] = $exhibitors[$val['id']]['audit'];
                    switch ($exhibitors[$val['id']]['audit']) {
                        case 1:
                            $friendly_tips = 3;
                            $friendly_tips_text = '您已预定展位号' . $val['position'] . '，请按时参会';
                            break;
                        case 2:
                            $friendly_tips = 4;
                            $friendly_tips_text = '您已预定展位号' . $val['position'] . '，请等待管理员审核';
                            break;
                        case 3:
                            $friendly_tips = 5;
                            $friendly_tips_text = '您已预定展位号' . $val['position'] . '，展位审核未通过，请联系网站客服';
                            break;
                    }
                    $list[$key]['friendly_tips'] = $friendly_tips;
                    $list[$key]['friendly_tips_text'] = $friendly_tips_text;
                } else {
                    $list[$key]['is_reserve'] = 0;
                    $list[$key]['position'] = '';
                    $list[$key]['reserve_status'] = 0;
                }
            }
        }
        $pagination = Pager::make($list, $pagesize, $current_page, $total, false, ['path' => '']);
        $this->initPageSeo('jobfairlist');
        $this->assign('list', $list);
        $this->assign('pageHeader', $this->pageHeader);
        $this->assign('pagerHtml', $pagerHtml = $pagination->render());
        return $this->fetch('index');
    }
    /** [show 招聘会详情页] */
    public function show() {
        $id = request()->route('id/d', 0, 'intval');
        if (is_mobile_request() === true) {
            $this->redirect(config('global_config.mobile_domain') . 'jobfair/' . $id, 302);
            exit;
        }
        if (!$id) abort(404, '请选择招聘会！');
        $jobfair = model('Jobfair')->where('id', $id)->find();
        $jobfair = $jobfair->toarray();
        if ($jobfair === null) abort(404, '招聘会不存在或已删除！');
        $time = time();
        $jobfair['introduction'] = htmlspecialchars_decode($jobfair['introduction'], ENT_QUOTES);
        if ($jobfair['predetermined_start'] > $time) {
            $jobfair['predetermined'] = 0;
            $jobfair['predetermined_text'] = '未开始';
            $jobfair['friendly_tips_text'] = '本场招聘会报名未开始，将在 ' . date('Y-m-d h:i', $jobfair['predetermined_start']) . ' 开始';
        } elseif ($jobfair['predetermined_end'] < $time) {
            $jobfair['predetermined'] = 2;
            $jobfair['predetermined_text'] = '已结束';
            $jobfair['friendly_tips_text'] = '本场招聘会已停止报名，请及时关注最新招聘会';
        } else {
            $jobfair['predetermined'] = 1;
            $jobfair['predetermined_text'] = '预定中';
            $jobfair['friendly_tips_text'] = '本场招聘会正火热报名中，请从速报名';
        }
        if ($jobfair['holddate_start'] > $time) {
            $jobfair['holddate'] = 0;
            $jobfair['holddate_text'] = '未开始';
        } elseif ($jobfair['holddate_end'] < $time) {
            $jobfair['holddate'] = 2;
            $jobfair['holddate_text'] = '已结束';
        } else {
            $jobfair['holddate'] = 1;
            $jobfair['holddate_text'] = '进行中';
        }
        $jobfair['friendly_tips'] = $jobfair['predetermined'];
        $thumbs = [];
        if ($jobfair['position_img']) {
            $jobfair['position_img'] = explode(',', $jobfair['position_img']);
            $thumbs = $jobfair['position_img'];
        }
        $jobfair['thumb'] > 0 && $thumbs[] = $jobfair['thumb'];
        $jobfair['intro_img'] > 0 && $thumbs[] = $jobfair['intro_img'];
        if ($thumbs) $thumb_imgs = model('Uploadfile')->getFileUrlBatch($thumbs);
        $jobfair['thumb'] = isset($thumb_imgs[$jobfair['thumb']]) ? $thumb_imgs[$jobfair['thumb']] : default_empty('jobfair_thumb');
        $jobfair['intro_img'] = isset($thumb_imgs[$jobfair['intro_img']]) ? $thumb_imgs[$jobfair['intro_img']] : default_empty('jobfair_thumb');
        if ($jobfair['position_img']) {
            foreach ($jobfair['position_img'] as $key => $val) {
                isset($thumb_imgs[$val]) && $jobfair['position_img'][$key] = $thumb_imgs[$val];
            }
        }
        $company = model('JobfairExhibitors')->where(['jobfair_id' => $id, 'audit' => 1])->column('uid,company_id,position,audit');
        if ($this->visitor && isset($company[$this->visitor['uid']])) {
            $jobfair['is_reserve'] = 1;
            $jobfair['position'] = $company[$this->visitor['uid']]['position'];
            $jobfair['reserve_status'] = $company[$this->visitor['uid']]['audit'];
            switch ($jobfair['reserve_status']) {
                case 1:
                    $jobfair['friendly_tips'] = 3;
                    $jobfair['friendly_tips_text'] = '您已预定展位号' . $jobfair['position'] . '，请按时参会';
                    break;
                case 2:
                    $jobfair['friendly_tips'] = 4;
                    $jobfair['friendly_tips_text'] = '您已预定展位号' . $jobfair['position'] . '，请等待管理员审核';
                    break;
                case 3:
                    $jobfair['friendly_tips'] = 5;
                    $jobfair['friendly_tips_text'] = '您已预定展位号' . $jobfair['position'] . '，展位审核未通过，请联系网站客服';
                    break;
            }
        } else {
            $jobfair['is_reserve'] = 0;
            $jobfair['position'] = '';
            $jobfair['reserve_status'] = 0;
        }
        $jobfair['company_num'] = count($company);
        $jobfair['jobs_num'] = model('JobSearchRtime')->where(['uid' => ['in', array_keys($company)]])->count('id');
        $retrospect = model('JobfairRetrospect')->where('jobfair_id', $id)->find();
        $jobfair['retrospect'] = $retrospect ? 1 : 0;
        model('Jobfair')->where('id', $id)->setInc('click', 1);
        if ($this->visitor) {
            $company_contact = model('CompanyContact')->field('contact,mobile')->where('uid', $this->visitor['uid'])->find();
            $this->assign('company_contact', $company_contact);
        }
        $this->initPageSeo('jobfairshow', ['title' => $jobfair['title']]);
        $this->assign('pageHeader', $this->pageHeader);
        $this->assign('info', $jobfair);
        return $this->fetch('show');
    }
    public function comlist() {
        $this->assign('tab', 'comlist');
        return $this->show();
    }
    public function reserve() {
        $this->assign('tab', 'reserve');
        return $this->show();
    }
}
