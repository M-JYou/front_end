<?php

namespace app\v1_0\controller\home;

class Jobfair extends \app\v1_0\controller\common\Base {
    public function _initialize() {
        parent::_initialize();
    }
    /** [index 招聘会列表页] */
    public function index() {
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
        $s_limit && $s_result = model('Jobfair')->where($s_where)->order($s_order)->limit($s_limit)->select();
        $e_limit && $e_result = model('Jobfair')->where($e_where)->order($e_order)->limit($e_limit)->select();
        $result = array_merge($s_result, $e_result);
        foreach ($result as $key => $val) {
            $val['thumb'] > 0 && $thumbs[] = $val['thumb'];
            $jobfairs[] = $val['id'];
        }
        if (isset($jobfairs)) {
            $company_num = model('JobfairExhibitors')->where(['jobfair_id' => ['in', $jobfairs], 'audit' => 1])->group('jobfair_id')->column('jobfair_id,count(id)');
            isset($thumbs) && $thumb_imgs = model('Uploadfile')->getFileUrlBatch($thumbs);
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
                $val['jobfair_url'] = url('index/jobfair/show', ['id' => $val['id']]);
                $list[] = $val;
            }
            if ($this->userinfo && isset($jids)) {
                $exhibitors = model('JobfairExhibitors')->where(['jobfair_id' => ['in', $jids], 'uid' => $this->userinfo->uid])->column('jobfair_id,position,audit');
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
        }
        $this->ajaxReturn(200, '获取数据成功', [
            'items' => isset($list) ? $list : [],
            'total' => $total,
            'total_page' => $total == 0 ? 0 : ceil($total / $pagesize)
        ]);
    }
    protected function getShow($id = 0) {
        $id = $id ?: input('get.id/d', 0, 'intval');
        if (!$id) return ['state' => 0, 'msg' => '请选择招聘会！'];
        $jobfair = model('Jobfair')->where('id', $id)->find();
        if ($jobfair === null) {
            return ['state' => 0, 'msg' => '招聘会不存在或已删除！'];
        }
        $jobfair = $jobfair->toarray();
        return ['state' => 1, 'msg' => '招聘会获取成功！', 'data' => $jobfair];
    }
    /** [show 招聘会详情页] */
    public function show() {
        $reg = $this->getShow();
        if (!$reg['state']) $this->ajaxReturn(500, $reg['msg']);
        $jobfair = $reg['data'];
        $time = time();
        if ($jobfair['predetermined_start'] > $time) {
            $jobfair['predetermined'] = 0;
            $jobfair['predetermined_text'] = '未开始';
        } elseif ($jobfair['predetermined_end'] < $time) {
            $jobfair['predetermined'] = 2;
            $jobfair['predetermined_text'] = '已结束';
        } else {
            $jobfair['predetermined'] = 1;
            $jobfair['predetermined_text'] = '预定中';
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
        $thumbs = [];
        if ($jobfair['position_img']) {
            $jobfair['position_img'] = explode(',', $jobfair['position_img']);
            $thumbs = $jobfair['position_img'];
        }
        $jobfair['thumb'] > 0 && $thumbs[] = $jobfair['thumb'];
        $jobfair['intro_img'] > 0 && $thumbs[] = $jobfair['intro_img'];
        if ($thumbs) $thumb_imgs = model('Uploadfile')->getFileUrlBatch($thumbs);
        $jobfair['thumb'] = isset($thumb_imgs[$jobfair['thumb']]) ? $thumb_imgs[$jobfair['thumb']] : default_empty('jobfair_thumb');
        $jobfair['intro_img'] = isset($thumb_imgs[$jobfair['intro_img']]) ? $thumb_imgs[$jobfair['intro_img']] : '';
        if ($jobfair['position_img']) {
            foreach ($jobfair['position_img'] as $key => $val) {
                isset($thumb_imgs[$val]) && $jobfair['position_img'][$key] = $thumb_imgs[$val];
            }
        }
        $company = model('JobfairExhibitors')->where(['jobfair_id' => $jobfair['id']])->column('uid,company_id,position,audit');
        if ($this->userinfo && isset($company[$this->userinfo->uid])) {
            $jobfair['is_reserve'] = 1;
            $jobfair['position'] = $company[$this->userinfo->uid]['position'];
            $jobfair['reserve_status'] = $company[$this->userinfo->uid]['audit'];
        } else {
            $jobfair['is_reserve'] = 0;
            $jobfair['position'] = '';
        }
        $jobfair['company_num'] = 0;
        foreach ($company as $val) {
            if ($val['audit'] == 1) {
                $jobfair['company_num']++;
                $cids[] = $val['uid'];
            }
        }
        $jobfair['jobs_num'] = $jobfair['company_num'] > 0 ? model('JobSearchRtime')->where(['uid' => ['in', $cids]])->count('id') : 0;
        model('Jobfair')->where('id', $jobfair['id'])->setInc('click', 1);
        $jobfair['introduction'] = htmlspecialchars_decode($jobfair['introduction'], ENT_QUOTES);
        $return['info'] = $jobfair;
        if ($this->userinfo) {
            $company_contact = model('CompanyContact')->field('contact,mobile')->where('uid', $this->userinfo->uid)->find();
            $return['company_contact'] = $company_contact;
        }
        $this->ajaxReturn(200, '获取数据成功', $return);
    }
    /** [comlist 参会企业] */
    public function comlist() {
        $reg = $this->getShow();
        if (!$reg['state']) $this->ajaxReturn(500, $reg['msg']);
        $current_page = input('get.page/d', 1, 'intval');
        $pagesize = input('get.pagesize/d', 10, 'intval');
        $company = [];
        if ($total = model('JobfairExhibitors')->where(['jobfair_id' => $reg['data']['id'], 'audit' => 1])->count()) {
            $firstRow = abs($current_page - 1) * $pagesize;
            $company_ids = model('JobfairExhibitors')->where(['jobfair_id' => $reg['data']['id'], 'audit' => 1])->limit($firstRow, $pagesize)->column('company_id,uid,position');
            $company = model('Company')->where('is_display', 1)->where(['id' => ['in', array_keys($company_ids)]])->column('id,companyname,nature,trade,district,logo,refreshtime');
            if ($company) {
                $category_data = model('Category')->getCache();
                $category_district_data = model('CategoryDistrict')->getCache();
                foreach ($company as $key => $val) {
                    $cids[] = $key;
                    $logos[] = $val['logo'];
                    $company[$key]['position'] = $company_ids[$val['id']]['position'];
                    $company[$key]['nature_text'] = $category_data['QS_company_type'][$val['nature']];
                    $company[$key]['trade_text'] = $category_data['QS_trade'][$val['trade']];
                    $company[$key]['district_text'] = $category_district_data[$val['district']];
                    $company[$key]['jobs_list'] = [];
                    $company[$key]['jobs_num'] = 0;
                }
                $jobs = model('Job')->field('id,company_id,jobname')->where(['company_id' => ['in', $cids]])->select();
                foreach ($jobs as $val) {
                    $company[$val['company_id']]['jobs_num']++;
                    $company[$val['company_id']]['jobs_list'][] = ['id' => $val['id'], 'jobname' => $val['jobname']];
                }
                if ($logos) {
                    $thumb_imgs = model('Uploadfile')->getFileUrlBatch($logos);
                    foreach ($company as $key => $val) {
                        $company[$key]['logo'] = isset($thumb_imgs[$val['logo']]) ? $thumb_imgs[$val['logo']] : default_empty('logo');
                    }
                }
            }
        }
        $this->ajaxReturn(200, '获取数据成功', [
            'items' => array_values($company),
            'total' => $total,
            'total_page' => $total == 0 ? 0 : ceil($total / $pagesize)
        ]);
    }
    /** [recommend 推荐企业] */
    public function recommend() {
        $reg = $this->getShow();
        if (!$reg['state']) $this->ajaxReturn(500, $reg['msg']);
        $where = [
            'jobfair_id' => $reg['data']['id'],
            'audit'      => 1,
            'recommend'  => 1,
        ];
        $cids = model('JobfairExhibitors')->where($where)->column('company_id');
        $company = [];
        if ($cids) {
            $company = model('Company')->where('is_display', 1)->field('id,companyname,nature,trade,district,logo,refreshtime')->where(['id' => ['in', $cids]])->select();
            $category_data = model('Category')->getCache();
            $category_district_data = model('CategoryDistrict')->getCache();
            foreach ($company as $key => $val) {
                $logos[] = $val['logo'];
                $company[$key]['nature_text'] = $category_data['QS_company_type'][$val['nature']];
                $company[$key]['trade_text'] = $category_data['QS_trade'][$val['trade']];
                $company[$key]['district_text'] = $category_district_data[$val['district']];
            }
            if ($logos) {
                $thumb_imgs = model('Uploadfile')->getFileUrlBatch($logos);
                foreach ($company as $key => $val) {
                    $company[$key]['logo'] = isset($thumb_imgs[$val['logo']]) && !empty($thumb_imgs[$val['logo']]) ? $thumb_imgs[$val['logo']] : default_empty('logo');
                }
            }
        }
        $this->ajaxReturn(200, '获取数据成功', [
            'items' => $company
        ]);
    }
    /** [reserve 在线预定,展位信息][展位图，展位信息] */
    public function position() {
        $reg = $this->getShow();
        if (!$reg['state']) $this->ajaxReturn(500, $reg['msg']);
        $area = model('JobfairArea')->field('id,area')->where(array('jobfair_id' => $reg['data']['id']))->order('area asc')->select();
        foreach ($area as $key => $val) {
            $area[$key]['area'] .= '区';
            $sids[] = $val['id'];
        }
        $position_arr = model('JobfairPosition')->field('id,area_id,position,company_id,company_name,status')->where(array('jobfair_id' => $reg['data']['id'], 'area_id' => ['in', $sids]))->order('area_id asc,orderid asc')->select();
        if ($position_arr) {
            $position = array();
            foreach ($position_arr as $key => $val) {
                $position[$val['area_id']][] = ['id' => $val['id'], 'position' => $val['position'], 'company_id' => $val['company_id'], 'company_name' => $val['company_name'], 'status' => $val['status']];
            }
            foreach ($area as $key => $val) {
                $area[$key]['positions'] = isset($position[$val['id']]) ? $position[$val['id']] : [];
            }
        }
        $this->ajaxReturn(200, '获取数据成功', [
            'items' => $area
        ]);
    }
    /** [retrospect 精彩回顾] */
    public function retrospect() {
        $reg = $this->getShow();
        if (!$reg['state']) $this->ajaxReturn(500, $reg['msg']);
        $imgs = model('JobfairRetrospect')->where('jobfair_id', $reg['data']['id'])->column('img');
        if ($imgs) $imgs = model('Uploadfile')->getFileUrlBatch($imgs);
        $this->ajaxReturn(200, '获取数据成功', [
            'items' => $imgs
        ]);
    }
}
