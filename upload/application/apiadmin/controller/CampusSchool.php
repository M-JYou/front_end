<?php

namespace app\apiadmin\controller;

class CampusSchool extends \app\common\controller\Backend {
    public function index() {
        $where = [];
        $is_display = input('get.is_display/s', '', 'trim');
        $key_type = input('get.key_type/d', 0, 'intval');
        $keyword = input('get.keyword/s', '', 'trim');
        $current_page = input('get.page/d', 1, 'intval');
        $pagesize = input('get.pagesize/d', 10, 'intval');
        if ($keyword && $key_type) {
            switch ($key_type) {
                case 1:
                    $where['name'] = ['like', '%' . $keyword . '%'];
                    break;
                case 2:
                    $where['id'] = ['eq', intval($keyword)];
                    break;
                default:
                    break;
            }
        }
        if ($is_display != '') {
            $where['display'] = ['eq', intval($is_display)];
        }

        $total = model('CampusSchool')
            ->where($where)
            ->count();
        $list = model('CampusSchool')
            ->where($where)
            ->order('id desc')
            ->page($current_page . ',' . $pagesize)
            ->select();
        $image_id_arr = $image_list = [];
        foreach ($list as $key => $value) {
            $value['logo'] && ($image_id_arr[] = $value['logo']);
        }
        if (!empty($image_id_arr)) {
            $image_list = model('Uploadfile')->getFileUrlBatch($image_id_arr);
        }
        foreach ($list as $key => $value) {
            $value['logo_url'] = isset($image_list[$value['logo']])
                ? $image_list[$value['logo']]
                : '';
            $value['preach_count'] = model('CampusPreach')->where('school_id', $value['id'])->count();
            $value['election_count'] = model('CampusElection')->where('school_id', $value['id'])->count();
            $value['school_link'] = url('campus/school/' . $value['id']);
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
            'name' => input('post.name/s', '', 'trim'),
            'logo' => input('post.logo/d', 0, 'intval'),
            'display' => input('post.display/d', 1, 'intval'),
            'district1' => input('post.district1/d', 0, 'intval'),
            'district2' => input('post.district2/d', 0, 'intval'),
            'district3' => input('post.district3/d', 0, 'intval'),
            'level' => input('post.level/d', 0, 'intval'),
            'type' => input('post.type/d', 0, 'intval'),
            'introduction' => input('post.introduction/s', '', 'trim'),
            'address' => input('post.address/s', '', 'trim'),
            'tel' => input('post.tel/s', '', 'trim')
        ];
        $input_data['addtime'] = time();
        $input_data['click'] = 0;
        $input_data['district'] =
            $input_data['district3'] != 0
            ? $input_data['district3']
            : ($input_data['district2'] != 0
                ? $input_data['district2']
                : $input_data['district1']);

        $result = model('CampusSchool')
            ->validate(true)
            ->allowField(true)
            ->save($input_data);
        if ($result === false) {
            $this->ajaxReturn(500, model('CampusSchool')->getError());
        }
        model('AdminLog')->record(
            '添加院校。院校ID【' .
                model('CampusSchool')->id .
                '】;院校名称【' .
                $input_data['name'] .
                '】',
            $this->admininfo
        );
        $this->ajaxReturn(200, '保存成功');
    }
    public function edit() {
        $id = input('get.id/d', 0, 'intval');
        if ($id) {
            $info = model('CampusSchool')->find($id);
            if (null === $info) {
                $this->ajaxReturn(500, '数据获取失败');
            }
            $info = $info->toArray();
            $info['introduction'] = htmlspecialchars_decode($info['introduction'], ENT_QUOTES);
            $imageSrc = model('Uploadfile')->getFileUrl($info['logo']);
            $this->ajaxReturn(200, '获取数据成功', [
                'info' => $info,
                'imageSrc' => $imageSrc
            ]);
        } else {
            $input_data = [
                'id' => input('post.id/d', 0, 'intval'),
                'name' => input('post.name/s', '', 'trim'),
                'logo' => input('post.logo/d', 0, 'intval'),
                'display' => input('post.display/d', 1, 'intval'),
                'district1' => input('post.district1/d', 0, 'intval'),
                'district2' => input('post.district2/d', 0, 'intval'),
                'district3' => input('post.district3/d', 0, 'intval'),
                'level' => input('post.level/d', 0, 'intval'),
                'type' => input('post.type/d', 0, 'intval'),
                'introduction' => input('post.introduction/s', '', 'trim'),
                'address' => input('post.address/s', '', 'trim'),
                'tel' => input('post.tel/s', '', 'trim')
            ];
            $id = intval($input_data['id']);
            if (!$id) {
                $this->ajaxReturn(500, '请选择数据');
            }
            $input_data['district'] =
                $input_data['district3'] != 0
                ? $input_data['district3']
                : ($input_data['district2'] != 0
                    ? $input_data['district2']
                    : $input_data['district1']);
            $result = model('CampusSchool')
                ->validate(true)
                ->allowField(true)
                ->save($input_data, ['id' => $id]);
            if (false === $result) {
                $this->ajaxReturn(500, model('CampusSchool')->getError());
            }
            model('AdminLog')->record(
                '编辑院校。院校ID【' .
                    $id .
                    '】;院校名称【' .
                    $input_data['name'] .
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
        $list = model('CampusSchool')
            ->where('id', 'in', $id)
            ->column('name');
        model('CampusSchool')->destroy($id);
        model('AdminLog')->record(
            '删除院校。院校ID【' .
                implode(',', $id) .
                '】;院校名称【' .
                implode(',', $list) .
                '】',
            $this->admininfo
        );
        $this->ajaxReturn(200, '删除成功');
    }
    public function level_category() {
        $list = model('CampusSchool')->map_level;
        $return = [];
        foreach ($list as $key => $value) {
            $arr['id'] = $key;
            $arr['name'] = $value;
            $return[] = $arr;
        }
        $this->ajaxReturn(200, '获取数据成功', $return);
    }
    public function type_category() {
        $list = model('CampusSchool')->map_type;
        $return = [];
        foreach ($list as $key => $value) {
            $arr['id'] = $key;
            $arr['name'] = $value;
            $return[] = $arr;
        }
        $this->ajaxReturn(200, '获取数据成功', $return);
    }
}
