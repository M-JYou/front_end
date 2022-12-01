<?php

namespace app\apiadmin\controller;

class LiveApi extends \app\common\controller\Backend {
    /** 企业搜索 */
    public function search() {
        $where  = [];
        $keyword = input('get.keyword/s', '', 'trim');
        $id = input('get.id/d', 0, 'intval');

        if (isset($keyword) && !empty($keyword)) {
            $where['a.companyname'] = ['like', '%' . $keyword . '%'];
        }
        if (isset($id) && !empty($id)) {
            $where['a.id'] = ['eq', $id];
        }
        $where['a.is_display'] = 1;
        $list = model('Company')
            ->alias('a')
            ->field(
                'a.id,a.companyname,a.logo,a.district1,a.district2,a.district3,a.district,a.scale,a.nature,a.trade,a.audit,a.setmeal_id,b.deadline as setmeal_deadline'
            )
            ->join(
                config('database.prefix') . 'member_setmeal b',
                'a.uid=b.uid',
                'LEFT'
            )
            ->where($where)
            ->order('a.id desc')
            ->select();
        $category_data = model('Category')->getCache();
        $category_district_data = model('CategoryDistrict')->getCache();
        if (!empty($list)) {
            $list = $this->companyList($list, $category_data, $category_district_data);
        }
        $this->ajaxReturn('200', '搜索企业', $list);
    }
    /** 获取企业职位 */
    public function getSimilarList() {
        $id = input('get.id/d', 0, 'intval');
        $current_page = input('get.page/d', 1, 'intval');
        $pagesize = input('get.pagesize/d', 10, 'intval');
        $total = model('Job')
            ->where('company_id', 'in', $id)
            ->where('is_display', 1)
            ->where('audit', 1)
            ->count();
        $job_data = model('Job')
            ->where('company_id', 'in', $id)
            ->where('is_display', 1)
            ->where('audit', 1)
            ->field('id,company_id,jobname,minwage,maxwage,negotiable,education,experience,district1,district2,district3')
            ->page($current_page . ',' . $pagesize)
            ->select();
        $job_data = $this->getJobDetails($job_data);
        $return['items'] = $job_data;
        $return['total'] = $total;
        $return['current_page'] = $current_page;
        $return['pagesize'] = $pagesize;
        $return['total_page'] = ceil($total / $pagesize);
        $this->ajaxReturn(200, '获取数据成功', $return);
    }
    /** 操作日志记录 */
    public function journal() {
        $content = input('post.content/s', '', 'trim');
        if (!$content) {
            $this->ajaxReturn('日志内容为空');
        }
        model('AdminLog')->record(
            $content,
            $this->admininfo
        );
    }
    /** 职位详情 */
    public function jobDetails() {
        $id = input('get.id/d', 0, 'intval');
        $field_rule_data = model('FieldRule')->getCache();
        //读取页面缓存配置
        $pageCache = model('Page')->getCache('jobshow');
        if ($pageCache['expire'] > 0) {
            $return = model('Page')->getCacheByAlias('jobshow', $id);
        } else {
            $return = false;
        }
        if (!$return) {
            $return = $this->writeShowCache($id, $pageCache);
            if ($return === false) {
                abort(404, '页面不存在');
            }
        }
        $return['share_url'] = config('global_config.mobile_domain') . 'job/' . $return['base_info']['id'];
        $return['im_url'] = config('global_config.mobile_domain') . 'im/imlist';
        $this->ajaxReturn(200, '获取数据成功', $return);
    }
    public function jobList() {
        $job_id = input('post.job_id/d', 0, 'intval');
        $company_id = input('post.company_id/d', 0, 'intval');
        if (!isset($job_id) && empty($job_id)) {
            $jobinfo = model('Job')
                ->where('id', $job_id)
                ->field('id,company_id,jobname,minwage,maxwage,negotiable,education,experience,district1,district2,district3')
                ->select();
            if ($jobinfo === null) {
                return false;
            }
        } else if (isset($company_id) && !empty($company_id)) {
            $jobinfo = model('Job')
                ->where('company_id', $company_id)
                ->field('id,company_id,jobname,minwage,maxwage,negotiable,education,experience,district1,district2,district3')
                ->select();
            if ($jobinfo === null) {
                return false;
            }
        }
        $list = $this->getJobDetails($jobinfo);
        $this->ajaxReturn(200, '获取数据成功',  $list);
    }


    public function sceneQrcode() {
        $input_data = [
            'live_id' => input('post.live_id/s', '', 'trim'),
            'share_title' => input('post.share_title/s', '', 'trim'),
            'share_describe' => input('post.share_describe/s', '', 'trim'),
            'share_picture' => input('post.share_picture/s', '', 'trim'),
        ];
        $input_data['type'] = 'live';
        $input_data['paramid'] = 1;
        $expire = 2592001;
        $input_data['uuid'] = uuid();
        $typeinfo = model('SceneQrcode')->type_arr[$input_data['type']];
        if ($expire < 0) {
            $expire = 60;
        }
        if ($expire > 2592000) {
            $expire = 2592000;
        }
        $class = new \app\common\lib\Wechat;
        $qrcodeData = $class->makeQrcode(
            [
                'alias' => 'subscribe_' . $typeinfo['alias'],
                $typeinfo['offiaccount_param_name'] => $input_data['paramid'],
                'scene_uuid' => $input_data['uuid'],
                'appkey' => config('global_config.live_app_key'),
                'appsecret' => config('global_config.live_app_secret'),
                'live_id' => $input_data['live_id'],
                'share_title' => $input_data['share_title'],
                'share_describe' => $input_data['share_describe'],
                'share_picture' => $input_data['share_picture'],
            ],
            $expire
        );
        $result = file_get_contents($qrcodeData);
        $filename = 'scene_qrcode_wechat_' . time() . '.jpg';
        $file_dir_name = 'files/' . date('Ymd/');
        $file_dir = SYS_UPLOAD_PATH . $file_dir_name;
        $file_path = $file_dir . $filename;
        if (!is_dir($file_dir)) {
            mkdir($file_dir, 0755, true);
        }
        file_put_contents($file_path, $result);
        $qrcodeSrc = $file_dir_name . $filename;
        $this->ajaxReturn(200, '保存成功', config('global_config.sitedomain') . config('global_config.sitedir') . 'upload/' . $qrcodeSrc);
    }
    /**
     * 获取投递简历信息
     * @return void
     */
    public function getDeliveryInformation() {
        $uid = input('get.uid/d', 0, 'intval');
        $data = [];
        $resume = model('Resume')->where('uid', $uid)->find();
        if (empty($resume)) {
            $this->ajaxReturn(500, '未查询到简历');
        } else {
            $data['id'] = $resume['id'];
            $data['fullname'] = $resume['fullname'];
            $data['sex'] = $resume['sex'];
            $data['birthday'] = $resume['birthday'];
            $data['education'] = $resume['education'];
            $data['enter_job_time'] = $resume['enter_job_time'];
            $data['integrity'] = model('Resume')->countCompletePercent(0, $uid);
            $this->ajaxReturn(200, '简历信息', $data);
        }
    }

    protected function getJobDetails($job_data) {
        foreach ($job_data as $k => $v) {
            $job_data[$k]['job_name'] = $v['jobname'];
            $category_district_data = model('CategoryDistrict')->getCache();
            $job_data[$k]['wage_text'] = model('BaseModel')->handle_wage(
                $v['minwage'],
                $v['maxwage'],
                $v['negotiable']
            );
            $job_data[$k]['education_text'] = isset(
                model('BaseModel')->map_education[$v['education']]
            )
                ? model('BaseModel')->map_education[$v['education']]
                : '学历不限';
            $job_data[$k]['experience_text'] = isset(
                model('BaseModel')->map_experience[$v['experience']]
            )
                ? model('BaseModel')->map_experience[$v['experience']]
                : '经验不限';
            $job_data[$k]['district_text'] = isset(
                $category_district_data[$v['district3']]
            )
                ? $category_district_data[$v['district3']]
                : '';
        }
        return $job_data;
    }
    protected function writeShowCache($id, $pageCache) {
        $jobinfo = model('Job')
            ->where('id', $id)
            ->field(true)
            ->find();
        if ($jobinfo === null) {
            return false;
        }
        $category_data = model('Category')->getCache();
        $category_district_data = model('CategoryDistrict')->getCache();
        $category_job_data = model('CategoryJob')->getCache();
        $base_info['id'] = $jobinfo['id'];
        $company = model('Company')->where('id', $jobinfo['company_id'])->find();
        $base_info['company_id'] = $jobinfo['company_id'];
        $base_info['company_name'] = $company['companyname'];
        $base_info['jobname'] = $jobinfo['jobname'];
        $base_info['emergency'] = $jobinfo['emergency'];
        $base_info['stick'] = $jobinfo['stick'];
        $base_info['content'] = $jobinfo['content'];
        $base_info['department'] = $jobinfo['department'];
        $base_info['nature_text'] = isset(
            model('Job')->map_nature[$jobinfo['nature']]
        )
            ? model('Job')->map_nature[$jobinfo['nature']]
            : '全职';
        $base_info['sex_text'] = isset(model('Job')->map_sex[$jobinfo['sex']])
            ? model('Job')->map_sex[$jobinfo['sex']]
            : '年龄不限';
        $base_info['district_text'] = isset(
            $category_district_data[$jobinfo['district1']]
        )
            ? $category_district_data[$jobinfo['district1']]
            : '';
        if ($base_info['district_text'] != '' && $jobinfo['district2'] > 0) {
            $base_info['district_text'] .= isset(
                $category_district_data[$jobinfo['district2']]
            )
                ? ' / ' . $category_district_data[$jobinfo['district2']]
                : '';
        }
        if ($base_info['district_text'] != '' && $jobinfo['district3'] > 0) {
            $base_info['district_text'] .= isset(
                $category_district_data[$jobinfo['district3']]
            )
                ? ' / ' . $category_district_data[$jobinfo['district3']]
                : '';
        }
        $base_info['category_text'] = isset(
            $category_job_data[$jobinfo['category']]
        )
            ? $category_job_data[$jobinfo['category']]
            : '';
        $base_info['negotiable'] = $jobinfo['negotiable'];
        $base_info['wage_text'] = model('BaseModel')->handle_wage(
            $jobinfo['minwage'],
            $jobinfo['maxwage'],
            $jobinfo['negotiable']
        );
        $base_info['education_text'] = isset(
            model('BaseModel')->map_education[$jobinfo['education']]
        )
            ? model('BaseModel')->map_education[$jobinfo['education']]
            : '学历不限';
        $base_info['experience_text'] = isset(
            model('BaseModel')->map_experience[$jobinfo['experience']]
        )
            ? model('BaseModel')->map_experience[$jobinfo['experience']]
            : '经验不限';

        $base_info['tag_text_arr'] = [];
        if ($jobinfo['tag'] != '') {
            $tag_arr = explode(',', $jobinfo['tag']);
            foreach ($tag_arr as $k => $v) {
                isset($category_data['QS_jobtag'][$v]) &&
                    ($base_info['tag_text_arr'][] =
                        $category_data['QS_jobtag'][$v]);
            }
        }

        $base_info['amount_text'] = $jobinfo['amount'] == 0 ? '若干' : $jobinfo['amount'];
        if ($jobinfo['age_na'] == 1) {
            $base_info['age_text'] = '年龄不限';
        } else if ($jobinfo['minage'] > 0 || $jobinfo['maxage'] > 0) {
            $base_info['age_text'] =
                $jobinfo['minage'] . '-' . $jobinfo['maxage'];
        } else {
            $base_info['age_text'] = '';
        }
        $base_info['click'] = $jobinfo['click'];
        $base_info['map_lat'] = $jobinfo['map_lat'];
        $base_info['map_lng'] = $jobinfo['map_lng'];
        $base_info['map_zoom'] = $jobinfo['map_zoom'];
        $base_info['address'] = $jobinfo['address'];
        $base_info['custom_field_1'] = $jobinfo['custom_field_1'];
        $base_info['custom_field_2'] = $jobinfo['custom_field_2'];
        $base_info['custom_field_3'] = $jobinfo['custom_field_3'];
        $base_info['refreshtime'] = daterange_format(
            $jobinfo['addtime'],
            $jobinfo['refreshtime']
        );
        $return['base_info'] = $base_info;
        if ($pageCache['expire'] > 0) {
            model('Page')->writeCacheByAlias('jobshow', $return, $pageCache['expire'], $id);
        }
        return $return;
    }
    public function companyList($list, $category_data, $category_district_data) {
        foreach ($list as $key => $value) {
            $job_list = $comid_arr = $logo_arr = $logo_id_arr = $setmeal_id_arr = $setmeal_list = [];
            foreach ($list as $a => $b) {
                $comid_arr[] = $b['id'];
                $value['logo'] > 0 && ($logo_id_arr[] = $b['logo']);
                $setmeal_id_arr[] = $b['setmeal_id'];
            }
            if (!empty($logo_id_arr)) {
                $logo_arr = model('Uploadfile')->getFileUrlBatch($logo_id_arr);
            }
            $list[$key]['id'] = $value['id'];
            $list[$key]['companyname'] = $value['companyname'];
            $list[$key]['company_audit'] = $value['audit'];
            $list[$key]['district_text'] = isset(
                $category_district_data[$value['district1']]
            )
                ? $category_district_data[$value['district1']]
                : '';
            if ($list[$key]['district_text'] != '' && $list[$key]['district2'] > 0) {
                $list[$key]['district_text'] .= isset(
                    $category_district_data[$value['district2']]
                )
                    ? ' · ' . $category_district_data[$value['district2']]
                    : '';
            }
            if ($list[$key]['district_text'] != '' && $value['district3'] > 0) {
                $list[$key]['district_text'] .= isset(
                    $category_district_data[$value['district3']]
                )
                    ? ' · ' . $category_district_data[$value['district3']]
                    : '';
            }
            $list[$key]['trade_text'] = isset(
                $category_data['QS_trade'][$value['trade']]
            )
                ? $category_data['QS_trade'][$value['trade']]
                : '';
            $list[$key]['scale_text'] = isset(
                $category_data['QS_scale'][$value['scale']]
            )
                ? $category_data['QS_scale'][$value['scale']]
                : '';
            $list[$key]['nature_text'] = isset(
                $category_data['QS_company_type'][$value['nature']]
            )
                ? $category_data['QS_company_type'][$value['nature']]
                : '';
            $list[$key]['first_jobname'] = isset($job_list[$value['id']])
                ? $job_list[$value['id']][0]
                : '';
            $list[$key]['logo_src'] = isset($logo_arr[$value['logo']])
                ? $logo_arr[$value['logo']]
                : default_empty('logo');
            if (isset($setmeal_list[$value['setmeal_id']]) && ($value['setmeal_deadline'] > time() || $value['setmeal_deadline'] == 0)) {
                $list[$key]['setmeal_icon'] =
                    $setmeal_list[$value['setmeal_id']]['icon'] > 0
                    ? model('Uploadfile')->getFileUrl(
                        $setmeal_list[$value['setmeal_id']]['icon']
                    )
                    : model('Setmeal')->getSysIcon($value['setmeal_id']);
            } else {
                $list[$key]['setmeal_icon'] = '';
            }
            $job_list = model('Job')
                ->field('id,jobname')
                ->where('company_id', 'eq', $value['id'])
                ->where('is_display', 1)
                ->where('audit', 1)
                ->select();
            $list[$key]['job_num'] = count($job_list);
            $company_info = model('CompanyInfo')->where('comid', $value['id'])->find();
            $list[$key]['details'] = $company_info['content'];
        }
        return $list;
    }
}
