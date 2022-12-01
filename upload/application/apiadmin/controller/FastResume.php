<?php

namespace app\apiadmin\controller;

class FastResume extends \app\common\controller\Backend {
    public function get_experience() {
        $map_experience = [
            1 => array('id' => 1, 'name' => '应届生'),
            2 => array('id' => 2, 'name' => '1年'),
            3 => array('id' => 3, 'name' => '2年'),
            4 => array('id' => 4, 'name' => '3年'),
            5 => array('id' => 5, 'name' => '3-5年'),
            6 => array('id' => 6, 'name' => '5-10年'),
            7 => array('id' => 7, 'name' => '10年以上'),
        ];
        $this->ajaxReturn(200, '获取数据成功', $map_experience);
    }
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
                    $where['fullname'] = ['like', '%' . $keyword . '%'];
                    break;
                case 2:
                    $where['wantjob'] = ['like', '%' . $keyword . '%'];
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
        $list = model('fastResume')
            ->where($where)
            ->order($order)
            ->page($current_page . ',' . $pagesize)
            ->select();
        //dump(model('fastResume')->getLastSql());die;	
        $total = model('fastResume')
            ->where($where)
            ->count();
        foreach ($list as $key => $value) {
            $list[$key]['refreshtime_cn'] = date('Y-m-d H:i', $value['refreshtime']);
            $list[$key]['addtime_cn'] = date('Y-m-d H:i', $value['addtime']);
            if ($value['endtime'] == 0) {
                $list[$key]['endtime_cn'] = "长期有效";
            } else {
                $list[$key]['endtime_cn'] = date('Y-m-d H:i', $value['endtime']);
            }
            $list[$key]['sex_text'] = isset(model('FastResume')->map_sex[$value['sex']])
                ? model('FastResume')->map_sex[$value['sex']]
                : '';
            $list[$key]['audit_text'] = isset(model('FastResume')->map_audit[$value['audit']])
                ? model('FastResume')->map_audit[$value['audit']]
                : '';
            $list[$key]['recommend_text'] = isset(model('FastResume')->map_rec[$value['is_recommend']])
                ? model('FastResume')->map_rec[$value['is_recommend']]
                : '';
            $list[$key]['top_text'] = isset(model('FastResume')->map_top[$value['is_top']])
                ? model('FastResume')->map_top[$value['is_top']]
                : '';
            $list[$key]['experience_text'] = isset(
                model('BaseModel')->map_experience[$value['experience']]
            )
                ? model('BaseModel')->map_experience[$value['experience']]
                : '';
            $list[$key]['content_'] = htmlspecialchars_decode(strip_tags($value['content']), ENT_QUOTES);
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
            'fullname' => input('post.fullname/s', '', 'trim,badword_filter'),
            'sex' => input('post.sex/d', 0, 'intval'),
            'experience' => input('post.experience/d', 0, 'intval'),
            'wantjob' => input('post.wantjob/s', '', 'trim,badword_filter'),
            'telephone' => input('post.telephone/s', '', 'trim,badword_filter'),
            'content' => input('post.content/s', '', 'trim,badword_filter'),
            'valid' => input('post.valid/d', 0, 'intval'),
            'adminpwd' => input('post.adminpwd/s', '', 'trim,badword_filter')
        ];
        $input_data['adminpwd'] = md5($input_data['adminpwd']);
        $input_data['addtime'] = $input_data['refreshtime'] = time();
        $input_data['click'] = 0;
        $input_data['audit'] = 1;
        if ($input_data['valid']) {
            $input_data['endtime'] = $input_data['addtime'] + $input_data['valid'] * 60 * 60 * 24;
        } else {
            $input_data['endtime'] = 0;
        }
        $input_data['is_top'] = 0;
        $input_data['is_recommend'] = 0;
        $result = model('FastResume')->save($input_data);
        if ($result === false) {
            $this->ajaxReturn(500, model('FastResume')->getError());
        }
        model('AdminLog')->record(
            '添加快速求职。简历ID【' .
                model('FastResume')->id .
                '】',
            $this->admininfo
        );
        $this->ajaxReturn(200, '保存成功');
    }

    public function edit() {
        $id = input('get.id/d', 0, 'intval');
        if ($id) {
            $info = $this->getResumeDetail($id);
            if (null === $info) {
                $this->ajaxReturn(500, '数据获取失败');
            }
            $info = $info->toArray();
            $this->ajaxReturn(200, '获取数据成功', [
                'info' => $info
            ]);
        } else {
            $input_data = [
                'id' => input('post.id/d', 0, 'intval'),
                'fullname' => input('post.fullname/s', '', 'trim,badword_filter'),
                'sex' => input('post.sex/d', 0, 'intval'),
                'experience' => input('post.experience/d', 0, 'intval'),
                'wantjob' => input('post.wantjob/s', '', 'trim,badword_filter'),
                'telephone' => input('post.telephone/s', '', 'trim,badword_filter'),
                'content' => input('post.content/s', '', 'trim,badword_filter'),
                'valid' => input('post.valid/d', 0, 'intval'),
                'adminpwd' => input('post.adminpwd/s', '', 'trim,badword_filter')
            ];
            if (!$input_data['id']) {
                $this->ajaxReturn(500, '请选择数据');
            }
            $info = $this->getResumeDetail($input_data['id']);
            $input_data['refreshtime'] = time();
            if (intval(config('global_config.fast_resume_edit_audit') > 0)) {
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
            $result = model('FastResume')->save($input_data, ['id' => $id]);
            if (false === $result) {
                $this->ajaxReturn(500, model('FastResume')->getError());
            }
            model('AdminLog')->record(
                '编辑快速求职。简历ID【' .
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
        $info = $this->getResumeDetail($id);
        if ($info['endtime']) {
            $input_data['endtime'] = $info['endtime'] + $valid * 60 * 60 * 24;
        } else {
            $input_data['endtime'] = $info['refreshtime'] + $valid * 60 * 60 * 24;
        }
        $result = model('FastResume')->save($input_data, ['id' => $id]);
        model('AdminLog')->record(
            '将快速求职有效期延长【' .
                $valid .
                '天】。快速求职记录ID【' .
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
        model('fastResume')->destroy($id);
        model('AdminLog')->record(
            '删除快速求职。求职记录ID【' .
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
        $info = $this->getResumeDetail($id);
        $input_data['adminpwd'] = md5($adminpwd);
        $result = model('FastResume')->save($input_data, ['id' => $id]);
        model('AdminLog')->record(
            '将快速求职密码改为【' .
                $adminpwd .
                '】。快速求职记录ID【' .
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
        model('fastResume')->setAudit($id, $audit, $reason);
        model('AdminLog')->record(
            '将快速求职审核状态变更为【' .
                model('fastResume')->map_audit[$audit] .
                '】。快速求职记录ID【' .
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
        model('fastResume')->setRecommend($id, $recommend);
        model('AdminLog')->record(
            '将快速求职推荐状态变更为【' .
                model('fastResume')->map_rec[$recommend] .
                '】。快速求职记录ID【' .
                implode(',', $id) .
                '】',
            $this->admininfo
        );
        $this->ajaxReturn(200, '设置成功');
    }
    public function top() {
        $id = input('post.id/a');
        $top = input('post.top/d', 0, 'intval');
        if (empty($id)) {
            $this->ajaxReturn(500, '请选择数据');
        }
        model('fastResume')->setTop($id, $top);
        model('AdminLog')->record(
            '将快速求职置顶状态变更为【' .
                model('fastResume')->map_top[$top] .
                '】。快速求职记录ID【' .
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
        model('fastResume')->setRefresh($id);
        model('AdminLog')->record(
            '将快速求职刷新时间变更为【' .
                date('Y-m-d H:i:s', time()) .
                '】。快速求职记录ID【' .
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
        model('fastResume')->delAll($id);
        model('AdminLog')->record(
            '删除快速求职。求职记录ID【' .
                implode(',', $id) .
                '】',
            $this->admininfo
        );
        $this->ajaxReturn(200, '删除成功');
    }
    public function getResumeDetail($id) {
        $id = intval($id);
        $where['id'] = $id;
        $basic = model('FastResume')
            ->where($where)
            ->find();
        if ($basic === null) {
            return false;
        }
        $basic['sex_text'] = isset(model('FastResume')->map_sex[$basic['sex']])
            ? model('FastResume')->map_sex[$basic['sex']]
            : '';
        $basic['experience_text'] = isset(
            model('BaseModel')->map_experience[$basic['experience']]
        )
            ? model('BaseModel')->map_experience[$basic['experience']]
            : '';
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
