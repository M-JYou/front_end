<?php

namespace app\index\controller;

class Mobile extends \think\Controller {
    protected function convertUrlQuery($query) {
        $params = array();
        if ($query != '') {
            $queryParts = explode('&', $query);
            foreach ($queryParts as $param) {
                $item = explode('=', $param);
                $params[$item[0]] = $item[1];
            }
        }
        return $params;
    }
    public function index() {
        if (!is_mobile_request()) {
            $site_domain = config('global_config.sitedomain');
            // $site_domain = trim($site_domain,"https://");
            // $site_domain = trim($site_domain,"/");
            $request_url_full = request()->url(true);
            $request_url = request()->url();
            if (strpos($request_url_full, config('global_config.mobile_domain') . 'job/') === 0) { //职位详情页
                preg_match('/\d+/', $request_url, $arr);
                $id = isset($arr[0]) ? $arr[0] : null;
                $location_href = url('index/job/show', ['id' => $id], '', $site_domain);
            } else if (strpos($request_url_full, config('global_config.mobile_domain') . 'joblist') === 0) { //职位列表页
                $parse_url = parse_url($request_url);
                $query_str = isset($parse_url['query']) ? $parse_url['query'] : '';
                $query_arr = $this->convertUrlQuery($query_str);

                $params = [];
                $replace_arr = [
                    'keyword' => 'keyword',
                    'district1' => 'd1',
                    'district2' => 'd2',
                    'district3' => 'd3',
                    'category1' => 'c1',
                    'category2' => 'c2',
                    'category3' => 'c3',
                    'minwage' => 'w1',
                    'maxwage' => 'w2',
                    'education' => 'edu',
                    'experience' => 'exp',
                    'settr' => 'settr',
                    'tag' => 'tag'
                ];
                foreach ($query_arr as $key => $value) {
                    if (isset($replace_arr[$key])) {
                        if ($key == 'keyword') {
                            $value = urldecode($value);
                        } elseif ($key == 'tag') {
                            $value = str_replace(",", "_", $value);
                        }
                        $params[$replace_arr[$key]] = $value;
                    }
                }
                $location_href = url('index/job/index', $params, '', $site_domain);
            } else if (strpos($request_url_full, config('global_config.mobile_domain') . 'resume/') === 0) { //简历详情页
                preg_match('/\d+/', $request_url, $arr);
                $id = isset($arr[0]) ? $arr[0] : null;
                $location_href = url('index/resume/show', ['id' => $id], '', $site_domain);
            } else if (strpos($request_url_full, config('global_config.mobile_domain') . 'resumelist') === 0) { //简历列表页
                $parse_url = parse_url($request_url);
                $query_str = isset($parse_url['query']) ? $parse_url['query'] : '';
                $query_arr = $this->convertUrlQuery($query_str);

                $params = [];
                $replace_arr = [
                    'keyword' => 'keyword',
                    'district1' => 'd1',
                    'district2' => 'd2',
                    'district3' => 'd3',
                    'sex' => 'sex',
                    'minage' => 'a1',
                    'maxage' => 'a2',
                    'minwage' => 'w1',
                    'maxwage' => 'w2',
                    'education' => 'edu',
                    'experience' => 'exp',
                    'settr' => 'settr',
                    'tag' => 'tag'
                ];
                foreach ($query_arr as $key => $value) {
                    if (isset($replace_arr[$key])) {
                        if ($key == 'keyword') {
                            $value = urldecode($value);
                        } elseif ($key == 'tag') {
                            $value = str_replace(",", "_", $value);
                        }
                        $params[$replace_arr[$key]] = $value;
                    }
                }
                $location_href = url('index/resume/index', $params, '', $site_domain);
            } else if (strpos($request_url_full, config('global_config.mobile_domain') . 'company/') === 0) { //企业详情页
                preg_match('/\d+/', $request_url, $arr);
                $id = isset($arr[0]) ? $arr[0] : null;
                $location_href = url('index/company/show', ['id' => $id], '', $site_domain);
            } else if (strpos($request_url_full, config('global_config.mobile_domain') . 'companylist') === 0) { //企业列表页
                $parse_url = parse_url($request_url);
                $query_str = isset($parse_url['query']) ? $parse_url['query'] : '';
                $query_arr = $this->convertUrlQuery($query_str);

                $params = [];
                $replace_arr = [
                    'keyword' => 'keyword',
                    'district1' => 'd1',
                    'district2' => 'd2',
                    'district3' => 'd3',
                    'trade' => 'trade',
                    'nature' => 'nature'
                ];
                foreach ($query_arr as $key => $value) {
                    if (isset($replace_arr[$key])) {
                        if ($key == 'keyword') {
                            $value = urldecode($value);
                        } elseif ($key == 'tag') {
                            $value = str_replace(",", "_", $value);
                        }
                        $params[$replace_arr[$key]] = $value;
                    }
                }
                $location_href = url('index/company/index', $params, '', $site_domain);
            } else if (strpos($request_url_full, config('global_config.mobile_domain') . 'jobfair/') === 0) { //招聘会详情页
                preg_match('/\d+/', $request_url, $arr);
                $id = isset($arr[0]) ? $arr[0] : null;
                $location_href = url('index/jobfair/show', ['id' => $id], '', $site_domain);
            } else if (strpos($request_url_full, config('global_config.mobile_domain') . 'jobfairlist') === 0) { //招聘会列表页
                $location_href = url('index/jobfair/index', '', '', $site_domain);
            } else if (strpos($request_url_full, config('global_config.mobile_domain') . 'jobfairol/') === 0) { //网络招聘会详情页
                preg_match('/\d+/', $request_url, $arr);
                $id = isset($arr[0]) ? $arr[0] : null;
                $location_href = url('index/jobfairol/show', ['id' => $id], '', $site_domain);
            } else if (strpos($request_url_full, config('global_config.mobile_domain') . 'jobfairol') === 0) { //网络招聘会列表页
                $location_href = url('index/jobfairol/index', '', '', $site_domain);
            } else if (strpos($request_url_full, config('global_config.mobile_domain') . 'news/') === 0) { //资讯详情页
                preg_match('/\d+/', $request_url, $arr);
                $id = isset($arr[0]) ? $arr[0] : null;
                $location_href = url('index/article/show', ['id' => $id], '', $site_domain);
            } else if (strpos($request_url_full, config('global_config.mobile_domain') . 'newslist') === 0) { //资讯列表页
                preg_match('/\d+/', $request_url, $arr);
                $cid = isset($arr[0]) ? $arr[0] : null;
                $location_href = url('index/article/index', ['cid' => $cid], '', $site_domain);
            } else if (strpos($request_url_full, config('global_config.mobile_domain') . 'noticelist') === 0) { //公告列表
                $location_href = url('index/notice/index', '', '', $site_domain);
            } else if (strpos($request_url_full, config('global_config.mobile_domain') . 'notice') === 0) { //公告详情
                preg_match('/\d+/', $request_url, $arr);
                $id = isset($arr[0]) ? $arr[0] : null;
                $location_href = url('index/notice/show', ['id' => $id], '', $site_domain);
            } else if (strpos($request_url_full, config('global_config.mobile_domain') . 'fast/joblist') === 0) { //快速招聘
                $location_href = url('index/fast/job', '', '', $site_domain);
            } else if (strpos($request_url_full, config('global_config.mobile_domain') . 'fast/job') === 0) { //快速招聘详情
                $location_href = url('index/fast/job', '', '', $site_domain);
            } else if (strpos($request_url_full, config('global_config.mobile_domain') . 'fast/resumelist') === 0) { //快速求职
                $location_href = url('index/fast/resume', '', '', $site_domain);
            } else if (strpos($request_url_full, config('global_config.mobile_domain') . 'fast/resume') === 0) { //快速求职详情
                $location_href = url('index/fast/resume', '', '', $site_domain);
            } else if (strpos($request_url_full, config('global_config.mobile_domain') . 'campus/index') === 0) { //校招
                $location_href = url('index/campus/index', '', '', $site_domain);
            } else if (strpos($request_url_full, config('global_config.mobile_domain') . 'freelance/index') === 0) { //自由职业
                $location_href = url('index/freelance/index', '', '', $site_domain);
            } else if (strpos($request_url_full, config('global_config.mobile_domain') . 'campus/job') === 0) { //校招职位
                $location_href = url('index/campus/job', '', '', $site_domain);
            } else if (strpos($request_url_full, config('global_config.mobile_domain') . 'campus/school/') === 0) { //院校详情
                preg_match('/\d+/', $request_url, $arr);
                $id = isset($arr[0]) ? $arr[0] : null;
                $location_href = url('index/campus/school_show', ['id' => $id], '', $site_domain);
            } else if (strpos($request_url_full, config('global_config.mobile_domain') . 'campus/school') === 0) { //院校
                $location_href = url('index/campus/school', '', '', $site_domain);
            } else if (strpos($request_url_full, config('global_config.mobile_domain') . 'campus/election/') === 0) { //双选会详情
                preg_match('/\d+/', $request_url, $arr);
                $id = isset($arr[0]) ? $arr[0] : null;
                $location_href = url('index/campus/election_show', ['id' => $id], '',  $site_domain);
            } else if (strpos($request_url_full, config('global_config.mobile_domain') . 'campus/election') === 0) { //双选会
                $location_href = url('index/campus/election', '', '', $site_domain);
            } else if (strpos($request_url_full, config('global_config.mobile_domain') . 'campus/preach/') === 0) { //宣讲会详情
                preg_match('/\d+/', $request_url, $arr);
                $id = isset($arr[0]) ? $arr[0] : null;
                $location_href = url('index/campus/preach_show', ['id' => $id], '', $site_domain);
            } else if (strpos($request_url_full, config('global_config.mobile_domain') . 'campus/preach') === 0) { //宣讲会
                $location_href = url('index/campus/preach', '', '', $site_domain);
            } else if (strpos($request_url_full, config('global_config.mobile_domain') . 'dailyDetail') === 0) { //今日招聘详情
                preg_match('/\d+/', $request_url, $arr);
                $cid = isset($arr[0]) ? $arr[0] : null;
                $location_href = url('index/recruitment/show', ['id' => $cid], '', $site_domain);
            } else if (strpos($request_url_full, config('global_config.mobile_domain') . 'dailyList') === 0) { //今日招聘列表
                preg_match('/\d+/', $request_url, $arr);
                $cid = isset($arr[0]) ? $arr[0] : null;
                $location_href = url('index/Recruitment/index', '', '', $site_domain);
            } else if (strpos($request_url_full, config('global_config.mobile_domain') . 'jobRegister') === 0) { //求职登记
                $location_href = url('index/job/register', '', '', $site_domain);
            } else if (strpos($request_url_full, config('global_config.mobile_domain') . 'shortvideo/videoplay') === 0 || strpos($request_url_full, config('global_config.mobile_domain') . 'shortvideo/personalList') === 0 || strpos($request_url_full, config('global_config.mobile_domain') . 'shortvideo/companylist') === 0) { //视频详情
                $location_href = url('index/video_recruitment/index', '', '', $site_domain);
            } else {
                $location_href = url('index/index/index', '', '', $site_domain);
            }
            $this->redirect($location_href, 302);
            exit;
        }
        return $this->fetch('./tpl/mobile/index.html');
    }
}
