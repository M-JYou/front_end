<?php

namespace app\apiadmin\controller;

class FastJob extends \app\common\controller\Backend {
    public function index() {
        $where = [];
        $keyword = input('get.keyword/s', '', 'trim');
        $key_type = input('get.key_type/d', 0, 'intval');
        $current_page = input('get.page/d', 1, 'intval');
        $pagesize = input('get.pagesize/d', 10, 'intval');
        $audit = input('get.audit/d', -1, 'intval');
        $is_end = input('get.is_end/d', -1, 'intval');
        $is_top = input('get.is_top/d', -1, 'intval');
        $is_recommend = input('get.is_recommend/d', -1, 'intval');
        if ($keyword && $key_type) {
            switch ($key_type) {
                case 1:
                    $where['jobname'] = ['like', '%' . $keyword . '%'];
                    break;
                case 2:
                    $where['comname'] = ['like', '%' . $keyword . '%'];
                    break;
                case 3:
                    $where['content'] = ['like', '%' . $keyword . '%'];
                    break;
                case 4:
                    $where['telephone'] = ['like', '%' . $keyword . '%'];
                    break;
                default:
                    break;
            }
        }
        if ($audit > -1) {
            $where['audit'] = ['eq', intval($audit)];
        }
        if ($is_end > -1) {
            if ($is_end == 1) {
                $where['endtime'] = ['lt', time()];
            } else {
                $where['endtime'] = ['gt', time()];
            }
        }
        if ($is_top > -1) {
            $where['is_top'] = ['eq', intval($is_top)];
        }
        if ($is_recommend > -1) {
            $where['is_recommend'] = ['eq', intval($is_recommend)];
        }
        $order = 'addtime desc';
        $list = model('fastJob')
            ->where($where)
            ->order($order)
            ->page($current_page . ',' . $pagesize)
            ->select();
        //dump(model('fastJob')->getLastSql());die;	
        $total = model('fastJob')
            ->where($where)
            ->count();
        foreach ($list as $key => $value) {
            $list[$key]['jobname'] = htmlspecialchars_decode($value['jobname'], ENT_QUOTES);
            $list[$key]['comname'] = htmlspecialchars_decode($value['comname'], ENT_QUOTES);
            $list[$key]['contact'] = htmlspecialchars_decode($value['contact'], ENT_QUOTES);
            $list[$key]['telephone'] = htmlspecialchars_decode($value['telephone'], ENT_QUOTES);
            $list[$key]['address'] = htmlspecialchars_decode($value['address'], ENT_QUOTES);
            $list[$key]['refreshtime_cn'] = date('Y-m-d H:i', $value['refreshtime']);
            $list[$key]['addtime_cn'] = date('Y-m-d H:i', $value['addtime']);
            if ($value['endtime'] == 0) {
                $list[$key]['endtime_cn'] = "长期有效";
            } else {
                $list[$key]['endtime_cn'] = date('Y-m-d H:i', $value['endtime']);
            }
            $list[$key]['audit_text'] = isset(model('FastJob')->map_audit[$value['audit']])
                ? model('FastJob')->map_audit[$value['audit']]
                : '';
            $list[$key]['recommend_text'] = isset(model('FastJob')->map_rec[$value['is_recommend']])
                ? model('FastJob')->map_rec[$value['is_recommend']]
                : '';
            $list[$key]['top_text'] = isset(model('FastJob')->map_top[$value['is_top']])
                ? model('FastJob')->map_top[$value['is_top']]
                : '';
            $list[$key]['content'] = htmlspecialchars_decode($value['content'], ENT_QUOTES);
            $list[$key]['content_'] = strip_tags($list[$key]['content']);
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
            'jobname' => input('post.jobname/s', '', 'trim,badword_filter'),
            'comname' => input('post.comname/s', '', 'trim,badword_filter'),
            'contact' => input('post.contact/s', '', 'trim,badword_filter'),
            'telephone' => input('post.telephone/s', '', 'trim,badword_filter'),
            'address' => input('post.address/s', '', 'trim,badword_filter'),
            'content' => input('post.content/s', '', 'trim,badword_filter'),
            'valid' => input('post.valid/d', 0, 'intval'),
            'adminpwd' => input('post.adminpwd/s', '', 'trim,badword_filter')
        ];
        $input_data['adminpwd'] = md5($input_data['adminpwd']);
        $input_data['addtime'] = $input_data['refreshtime'] = time();
        $input_data['audit'] = 1;
        if ($input_data['valid']) {
            $input_data['endtime'] = $input_data['addtime'] + $input_data['valid'] * 60 * 60 * 24;
        } else {
            $input_data['endtime'] = 0;
        }
        $input_data['is_top'] = 0;
        $input_data['is_recommend'] = 0;
        $validate = new \app\common\validate\FastJob();
        if (!$validate->check($input_data)) {
            $this->ajaxReturn(500, $validate->getError(), $input_data);
        }
        $time1 = strtotime(date('Y-m-d', time()));
        $time2 = strtotime(date('Y-m-d', time())) + 24 * 60 * 60;
        $today_where['telephone'] = $input_data['telephone'];
        $today_where['addtime'] = array('between', $time1 . ',' . $time2);
        $today_pub_num = model('fastJob')->where($today_where)->count();
        if ($today_pub_num >= intval(config('global_config.fast_job_num'))) {
            $this->ajaxReturn(500, '今天快速招聘发布次数已用完，请明天再发！');
        }
        $r = model('fastJob')->save($input_data);
        $input_data['id'] = model('fastJob')->getLastInsID();
        $this->ajaxReturn($r !== false ? 200 : 500, '保存成功', $input_data);
    }

    public function edit() {
        $id = input('get.id/d', 0, 'intval');
        if ($id) {
            $info = $this->getJobDetail($id);
            if (null === $info) {
                $this->ajaxReturn(500, '数据获取失败');
            }
            $info = $info->toArray();
            $info['jobname'] = htmlspecialchars_decode($info['jobname'], ENT_QUOTES);
            $info['comname'] = htmlspecialchars_decode($info['comname'], ENT_QUOTES);
            $info['contact'] = htmlspecialchars_decode($info['contact'], ENT_QUOTES);
            $info['telephone'] = htmlspecialchars_decode($info['telephone'], ENT_QUOTES);
            $info['address'] = htmlspecialchars_decode($info['address'], ENT_QUOTES);
            $info['content'] = htmlspecialchars_decode($info['content'], ENT_QUOTES);
            $this->ajaxReturn(200, '获取数据成功', [
                'info' => $info
            ]);
        } else {
            $input_data = [
                'id' => input('post.id/d', 0, 'intval'),
                'jobname' => input('post.jobname/s', '', 'trim,badword_filter'),
                'comname' => input('post.comname/s', '', 'trim,badword_filter'),
                'contact' => input('post.contact/s', '', 'trim,badword_filter'),
                'telephone' => input('post.telephone/s', '', 'trim,badword_filter'),
                'address' => input('post.address/s', '', 'trim,badword_filter'),
                'content' => input('post.content/s', '', 'trim,badword_filter'),
                'valid' => input('post.valid/d', 0, 'intval'),
                'adminpwd' => input('post.adminpwd/s', '', 'trim,badword_filter')
            ];
            $id = intval($input_data['id']);
            if (!$id) {
                $this->ajaxReturn(500, '请选择数据');
            }
            $info = $this->getJobDetail($input_data['id']);
            $input_data['refreshtime'] = time();
            if (intval(config('global_config.fast_job_edit_audit') > 0)) {
                $input_data['audit'] = 0;
            }
            if ($input_data['valid']) {
                if ($info['endtime']) {
                    $input_data['endtime'] = $info['endtime'] + $input_data['valid'] * 60 * 60 * 24;
                } else {
                    $input_data['endtime'] = $info['refreshtime'] + $input_data['valid'] * 60 * 60 * 24;
                }
            } else {
                $input_data['endtime'] = 0;
            }
            $result = model('FastJob')->save($input_data, ['id' => $id]);
            if (false === $result) {
                $this->ajaxReturn(500, model('FastJob')->getError());
            }
            model('AdminLog')->record(
                '编辑快速招聘。职位ID【' .
                    $id .
                    '】',
                $this->admininfo
            );
            $this->ajaxReturn(200, '保存成功');
        }
    }
    public function valid() {
        $id = input('post.id/d');
        $valid = input('post.valid/d', 0, 'intval');
        if (empty($id)) {
            $this->ajaxReturn(500, '请选择数据');
        }
        $info = $this->getJobDetail($id);
        if ($info['endtime']) {
            $input_data['endtime'] = $info['endtime'] + $valid * 60 * 60 * 24;
        } else {
            $input_data['endtime'] = $info['refreshtime'] + $valid * 60 * 60 * 24;
        }
        model('fastJob')->save($input_data, ['id' => $id]);
        model('AdminLog')->record(
            '将快速招聘有效期延长【' .
                $valid .
                '天】。快速招聘记录ID【' .
                $id .
                '】',
            $this->admininfo
        );
        $this->ajaxReturn(200, '设置成功');
    }

    public function delete() {
        $id = input('post.id/d');
        if (!$id) {
            $this->ajaxReturn(500, '请选择数据');
        }
        model('fastJob')->destroy($id);
        model('AdminLog')->record(
            '删除快速招聘。快速招聘记录ID【' .
                $id .
                '】',
            $this->admininfo
        );
        $this->ajaxReturn(200, '删除成功');
    }
    public function edit_pwd() {
        $id = input('post.id/d');
        $adminpwd = input('post.adminpwd/s', '', 'trim');
        if (empty($id)) {
            $this->ajaxReturn(500, '请选择数据');
        }
        $info = $this->getJobDetail($id);
        $input_data['adminpwd'] = md5($adminpwd);
        $result = model('FastJob')->save($input_data, ['id' => $id]);
        model('AdminLog')->record(
            '将快速招聘密码改为【' .
                $adminpwd .
                '】。快速招聘记录ID【' .
                $id .
                '】',
            $this->admininfo
        );
        $this->ajaxReturn(200, '设置成功');
    }
    public function audit() {
        $id = input('post.id/a');
        $audit = input('post.audit/d', 0, 'intval');
        $reason = input('post.reason/s', '', 'trim');
        if (empty($id)) {
            $this->ajaxReturn(500, '请选择数据');
        }
        model('FastJob')->setAudit($id, $audit, $reason);
        model('AdminLog')->record(
            '将快速招聘审核状态变更为【' .
                model('fastResume')->map_audit[$audit] .
                '】。快速招聘记录ID【' .
                implode(',', $id) .
                '】',
            $this->admininfo
        );
        $this->ajaxReturn(200, '审核成功');
    }
    public function rec() {
        $id = input('post.id/a');
        $recommend = input('post.recommend/d', 0, 'intval');
        if (empty($id)) {
            $this->ajaxReturn(500, '请选择数据');
        }
        model('FastJob')->setRecommend($id, $recommend);
        model('AdminLog')->record(
            '将快速招聘推荐状态变更为【' .
                model('fastResume')->map_rec[$recommend] .
                '】。快速招聘记录ID【' .
                implode(',', $id) .
                '】',
            $this->admininfo
        );
        $this->ajaxReturn(200, '设置成功', model('FastJob')->getLastSql());
    }
    public function top() {
        $id = input('post.id/a');
        $top = input('post.top/d', 0, 'intval');
        if (empty($id)) {
            $this->ajaxReturn(500, '请选择数据');
        }
        model('FastJob')->setTop($id, $top);
        model('AdminLog')->record(
            '将快速招聘置顶状态变更为【' .
                model('fastResume')->map_top[$top] .
                '】。快速招聘记录ID【' .
                implode(',', $id) .
                '】',
            $this->admininfo
        );
        $this->ajaxReturn(200, '设置成功');
    }
    public function refresh() {
        $id = input('post.id/a');
        $valid = input('post.valid/d', 0, 'intval');
        if (empty($id)) {
            $this->ajaxReturn(500, '请选择数据');
        }
        model('FastJob')->setRefresh($id);
        model('AdminLog')->record(
            '将快速招聘刷新时间变更为【' .
                date('Y-m-d H:i:s', time()) .
                '】。快速招聘记录ID【' .
                implode(',', $id) .
                '】',
            $this->admininfo
        );
        $this->ajaxReturn(200, '设置成功');
    }
    public function delete_all() {
        $id = input('post.id/a');
        if (!$id) {
            $this->ajaxReturn(500, '请选择数据');
        }
        model('FastJob')->delAll($id);
        model('AdminLog')->record(
            '删除快速招聘。招聘记录ID【' .
                implode(',', $id) .
                '】',
            $this->admininfo
        );
        $this->ajaxReturn(200, '删除成功');
    }

    public function getJobDetail($id) {
        $id = intval($id);
        $where['id'] = $id;
        $basic = model('FastJob')
            ->where($where)
            ->find();
        if ($basic === null) {
            return false;
        }
        $basic['refreshtime_cn'] = daterange(time(), $basic['refreshtime']);
        $basic['addtime_cn'] = daterange(time(), $basic['addtime']);
        if ($basic['endtime'] == 0) {
            $basic['endtime_cn'] = "长期有效";
        } else {
            $basic['endtime_cn'] = date('Y-m-d H:i', $basic['endtime']);
        }
        return $basic;
    }
}
