<?php

namespace app\apiadmin\controller;

class CampusElection extends \app\common\controller\Backend {
    public function index() {
        $where = [];
        $is_display = input('get.is_display/s', '', 'trim');
        $timecase = input('get.timecase', '', 'intval');
        $orderby_str = input('get.orderby', 'addtime', 'trim');
        $key_type = input('get.key_type/d', 0, 'intval');
        $keyword = input('get.keyword/s', '', 'trim');
        $current_page = input('get.page/d', 1, 'intval');
        $pagesize = input('get.pagesize/d', 10, 'intval');
        if ($keyword && $key_type) {
            switch ($key_type) {
                case 1:
                    $where['subject'] = ['like', '%' . $keyword . '%'];
                    break;
                case 2:
                    $c_where['name'] = ['like', '%' . $keyword . '%'];
                    $cids = model('CampusSchool')->where($c_where)->column('id');
                    $where['school_id'] = ['in', implode(',', $cids)];
                    break;
                case 3:
                    $where['id'] = array('eq', $keyword);
                    break;
                case 4:
                    $where['school_id'] = array('eq', $keyword);
                    break;
                default:
                    break;
            }
        }
        if ($timecase != '') {
            $timecase_map = model('CampusElection')->timecase_map($timecase);
            if ($timecase_map) {
                $where['starttime'] = $timecase_map;
            }
        }
        if ($is_display != '') {
            $where['display'] = ['eq', intval($is_display)];
        }
        $order = 'addtime desc';
        if (in_array($orderby_str, array('addtime', 'starttime'))) {
            if ($orderby_str == 'starttime') {
                $order = $orderby_str . ' asc';
            } else {
                $order = $orderby_str . ' desc';
            }
        }
        $list = model('CampusElection')
            ->where($where)
            ->order($order)
            ->page($current_page . ',' . $pagesize)
            ->select();
        $total = model('CampusElection')
            ->where($where)
            ->count();
        $school_ids = [];
        foreach ($list as $key => $value) {
            $school_ids[] = $value['school_id'];
        }
        $school_list = model('CampusSchool')->where('id', 'in', $school_ids)->column('name', 'id');
        foreach ($list as $key => $value) {
            $value['school_name'] = $school_list[$value['school_id']];
            $value['election_link'] = url('campus/election/' . $value['id']);
            $value['school_link'] = url('campus/school/' . $value['school_id']);
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
            'school_id' => input('post.school_id/d', 0, 'intval'),
            'subject' => input('post.subject/s', '', 'trim'),
            'display' => input('post.display/d', 1, 'intval'),
            'address' => input('post.address/s', '', 'trim'),
            'starttime' => input('post.starttime/s', '', 'trim'),
            'endtime' => input('post.endtime/s', '', 'trim'),
            'introduction' => input('post.introduction/s', '', 'trim'),
            'company_count' => input('post.company_count/d', 0, 'intval'),
            'graduate_count' => input('post.graduate_count/d', 0, 'intval')
        ];
        $input_data['addtime'] = time();
        $input_data['click'] = 0;
        if ($input_data['starttime']) {
            $input_data['starttime'] = strtotime($input_data['starttime']);
        }
        if ($input_data['endtime']) {
            $input_data['endtime'] = strtotime($input_data['endtime']);
        }
        $result = model('CampusElection')
            ->validate(true)
            ->allowField(true)
            ->save($input_data);
        if ($result === false) {
            $this->ajaxReturn(500, model('CampusElection')->getError());
        }
        model('AdminLog')->record(
            '添加双选会。双选会ID【' .
                model('CampusElection')->id .
                '】',
            $this->admininfo
        );
        $this->ajaxReturn(200, '保存成功');
    }

    public function edit() {
        $id = input('get.id/d', 0, 'intval');
        if ($id) {
            $info = model('CampusElection')->find($id);
            if (null === $info) {
                $this->ajaxReturn(500, '数据获取失败');
            }
            $info = $info->toArray();
            $info['introduction'] = htmlspecialchars_decode($info['introduction'], ENT_QUOTES);
            $this->ajaxReturn(200, '获取数据成功', [
                'info' => $info
            ]);
        } else {
            $input_data = [
                'id' => input('post.id/d', 0, 'intval'),
                'school_id' => input('post.school_id/d', 0, 'intval'),
                'subject' => input('post.subject/s', '', 'trim'),
                'display' => input('post.display/d', 1, 'intval'),
                'address' => input('post.address/s', '', 'trim'),
                'starttime' => input('post.starttime/s', '', 'trim'),
                'endtime' => input('post.endtime/s', '', 'trim'),
                'introduction' => input('post.introduction/s', '', 'trim'),
                'company_count' => input('post.company_count/d', 0, 'intval'),
                'graduate_count' => input('post.graduate_count/d', 0, 'intval')
            ];
            $id = intval($input_data['id']);
            if (!$id) {
                $this->ajaxReturn(500, '请选择数据');
            }
            if ($input_data['starttime']) {
                $input_data['starttime'] = strtotime($input_data['starttime']);
            }
            if ($input_data['endtime']) {
                $input_data['endtime'] = strtotime($input_data['endtime']);
            }
            $result = model('CampusElection')
                ->validate(true)
                ->allowField(true)
                ->save($input_data, ['id' => $id]);
            if (false === $result) {
                $this->ajaxReturn(500, model('CampusElection')->getError());
            }
            model('AdminLog')->record(
                '编辑双选会。双选会ID【' .
                    $id .
                    '】',
                $this->admininfo
            );
            $this->ajaxReturn(200, '保存成功');
        }
    }

    public function delete() {
        $id = input('post.id/a');
        if (!$id) {
            $this->ajaxReturn(500, '请选择数据');
        }
        model('CampusElection')->destroy($id);
        model('AdminLog')->record(
            '删除双选会。双选会ID【' .
                implode(',', $id) .
                '】',
            $this->admininfo
        );
        $this->ajaxReturn(200, '删除成功');
    }

    public function get_school_list() {
        $school_list = model('CampusSchool')->field('id,name')->select();
        $this->ajaxReturn(200, '获取数据成功', $school_list);
    }
}
