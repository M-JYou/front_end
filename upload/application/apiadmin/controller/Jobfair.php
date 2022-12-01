<?php

namespace app\apiadmin\controller;

class Jobfair extends \app\common\controller\Backend {
    public function _initialize() {
        parent::_initialize();
    }
    public function index() {
        $where = [];
        $status = input('get.status/s', '', 'trim');
        $settr = input('get.settr/d', 0, 'intval');
        $key_type = input('get.key_type/d', 0, 'intval');
        $keyword = input('get.keyword/s', '', 'trim');
        $current_page = input('get.page/d', 1, 'intval');
        $pagesize = input('get.pagesize/d', 10, 'intval');
        $time = time();

        if ($keyword && $key_type) {
            switch ($key_type) {
                case 1:
                    $where['title'] = ['like', '%' . $keyword . '%'];
                    break;
            }
        }
        if ($settr) $where['addtime'] = ['gt', strtotime("-" . $settr . " day")];
        if ($status != '') {
            switch (intval($status)) {
                case 0:
                    $where['predetermined_start'] = array('gt', $time);
                    break;
                case 1:
                    $where['predetermined_start'] = array('lt', $time);
                    $where['predetermined_end'] = array('gt', $time);
                    break;
                case 2:
                    $where['predetermined_end'] = array('lt', $time);
                    break;
            }
        }
        $total = model('Jobfair')->where($where)->count();
        $list = model('Jobfair')->where($where)->order('ordid desc,id desc')->page($current_page . ',' . $pagesize)->select();
        foreach ($list as $key => $val) {
            if ($val['predetermined_start'] > $time) {
                $val['predetermined'] = 0;
                $val['predetermined_text'] = '未开始';
            } elseif ($val['predetermined_end'] < $time) {
                $val['predetermined'] = 2;
                $val['predetermined_text'] = '已结束';
            } else {
                $val['predetermined'] = 1;
                $val['predetermined_text'] = '预定中';
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
            $val['jobfair_link'] = url('index/jobfair/show', ['id' => $val['id']]);
            $list[$key] = $val;
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
            'title' => input('post.title/s', '', 'trim'),
            'holddate_start' => input('post.holddate_start/s', '', 'trim'),
            'holddate_end' => input('post.holddate_end/s', '', 'trim'),
            'predetermined_start' => input('post.predetermined_start/s', '', 'trim'),
            'predetermined_end' => input('post.predetermined_end/s', '', 'trim'),
            'sponsor' => input('post.sponsor/s', '', 'trim'),
            'contact' => input('post.contact/s', '', 'trim'),
            'phone' => input('post.phone/s', '', 'trim'),
            'thumb' => input('post.thumb/d', 0, 'intval'),
            'address' => input('post.address/s', '', 'trim'),
            'number' => input('post.number/s', '', 'trim'),
            'bus' => input('post.bus/s', '', 'trim'),
            'map_lat' => input('post.map_lat/s', '', 'trim'),
            'map_lng' => input('post.map_lng/s', '', 'trim'),
            'map_zoom' => input('post.map_zoom/s', '', 'trim'),
            'display' => input('post.display/d', 0, 'intval'),
            'intro_img' => input('post.intro_img/d', 0, 'intval'),
            'introduction' => input('post.introduction/s', '', 'trim'),
            'ordid' => input('post.ordid/d', 0, 'intval'),
            'tpl_id' => input('post.tpl_id/d', 0, 'intval'),
            'participants_object' => input('post.participants_object/s', '', 'trim'),
            'price' => input('post.price/s', '', 'trim'),
            'registration_method' => input('post.registration_method/s', '', 'trim')
        ];
        $reg = model('Jobfair')->jobfairAdd($input_data, $this->admininfo);
        if (!$reg['state']) $this->ajaxReturn(500, $reg['msg']);
        model('AdminLog')->record(
            '发布招聘会。招聘会ID【' .
                $reg['data']['id'] .
                '】;招聘会标题【' .
                $input_data['title'] .
                '】',
            $this->admininfo
        );
        $this->ajaxReturn(200, '保存成功');
    }
    public function edit() {
        if (request()->isPost()) {
            $input_data = [
                'id' => input('post.id/d', 0, 'intval'),
                'title' => input('post.title/s', '', 'trim'),
                'holddate_start' => input('post.holddate_start/s', '', 'trim'),
                'holddate_end' => input('post.holddate_end/s', '', 'trim'),
                'predetermined_start' => input('post.predetermined_start/s', '', 'trim'),
                'predetermined_end' => input('post.predetermined_end/s', '', 'trim'),
                'sponsor' => input('post.sponsor/s', '', 'trim'),
                'contact' => input('post.contact/s', '', 'trim'),
                'phone' => input('post.phone/s', '', 'trim'),
                'thumb' => input('post.thumb/d', 0, 'intval'),
                'address' => input('post.address/s', '', 'trim'),
                'number' => input('post.number/s', '', 'trim'),
                'bus' => input('post.bus/s', '', 'trim'),
                'map_lat' => input('post.map_lat/s', '', 'trim'),
                'map_lng' => input('post.map_lng/s', '', 'trim'),
                'map_zoom' => input('post.map_zoom/s', '', 'trim'),
                'display' => input('post.display/d', 0, 'intval'),
                'intro_img' => input('post.intro_img/d', 0, 'intval'),
                'introduction' => input('post.introduction/s', '', 'trim'),
                'ordid' => input('post.ordid/d', 0, 'intval'),
                'participants_object' => input('post.participants_object/s', '', 'trim'),
                'price' => input('post.price/s', '', 'trim'),
                'registration_method' => input('post.registration_method/s', '', 'trim')
            ];
            $reg = model('Jobfair')->jobfairEdit($input_data, $this->admininfo);
            if (!$reg['state']) $this->ajaxReturn(500, $reg['msg']);
            model('AdminLog')->record(
                '编辑招聘会。招聘会ID【' . $input_data['id'] . '】;招聘会标题【' . $input_data['title'] . '】',
                $this->admininfo
            );
            $this->ajaxReturn(200, $reg['msg']);
        } else {
            $id = input('get.id/d', 0, 'intval');
            $info = model('Jobfair')->find($id);
            if (!$info) $this->ajaxReturn(500, '数据获取失败');
            $info = $info->toArray();
            $info['introduction'] = htmlspecialchars_decode($info['introduction'], ENT_QUOTES);
            $imgs = $imageUrl = [];
            if ($info['thumb']) $imgs[] = $info['thumb'];
            if ($info['intro_img']) $imgs[] = $info['intro_img'];
            if (!empty($imgs)) {
                $imageUrl = model('Uploadfile')->getFileUrlBatch($imgs);
            }
            $this->ajaxReturn(200, '获取数据成功', [
                'info' => $info,
                'thumbUrl' => isset($imageUrl[$info['thumb']]) ? $imageUrl[$info['thumb']] : '',
                'introImgUrl' => isset($imageUrl[$info['intro_img']]) ? $imageUrl[$info['intro_img']] : ''
            ]);
        }
    }
    public function delete() {
        $id = input('post.id/a');
        if (!$id) $this->ajaxReturn(500, '请选择招聘会');
        $reg = model('Jobfair')->jobfairDelete($id, $this->admininfo);
        $this->ajaxReturn($reg['state'] ? 200 : 500, $reg['msg']);
    }
    public function getPositionTpl() {
        $tpl_arr = model('JobfairPositionTpl')->field('id,title')->where('status', 1)->select();
        $this->ajaxReturn(200, '获取数据成功', ['items' => $tpl_arr]);
    }
    public function getJobfairAll() {
        $jobfair = model('Jobfair')->field('id,title')->order(['addtime' => 'desc'])->select();
        $this->ajaxReturn(200, '获取数据成功', ['items' => $jobfair]);
    }
    public function retrospect() {
        $id = input('get.jobfair_id/d', 0, 'intval');
        if (!$id) $this->ajaxReturn(500, '请选择招聘会');
        if ($list = model('JobfairRetrospect')->field('id,img')->where('jobfair_id', $id)->select()) {
            foreach ($list as $val) {
                $imgs[] = $val['img'];
            }
            $imageUrl = model('Uploadfile')->getFileUrlBatch($imgs);
            foreach ($list as $key => $val) {
                $list[$key]['img'] = isset($imageUrl[$val['img']]) ? $imageUrl[$val['img']] : '';
            }
        }
        $this->ajaxReturn(200, '获取数据成功', ['items' => $list]);
    }
    public function retrospectAdd() {
        $jobfair_id = input('post.jobfair_id/d', 0, 'intval');
        if (!$jobfair_id) $this->ajaxReturn(500, '请选择招聘会');
        $img = input('post.img/d', 0, 'intval');
        if (!$img) $this->ajaxReturn(500, '请上传图片');
        if (!$jobfair = model('Jobfair')->find($jobfair_id)) $this->ajaxReturn(500, '招聘会不存在');
        $reg = model('JobfairRetrospect')->allowField(true)->isUpdate(false)->save(['jobfair_id' => $jobfair['id'], 'img' => $img]);
        if ($reg && $id = model('JobfairRetrospect')->id) {
            model('AdminLog')->record(
                '招聘会新增精彩回顾。招聘会ID【' . $jobfair['id'] . '】;招聘会标题【' . $jobfair['title'] . '】,图片ID【' . $id . '】',
                $this->admininfo
            );
            $this->ajaxReturn(200, '上传成功', $id);
        }
        $this->ajaxReturn(500, '上传图片失败');
    }
    public function retrospectDelete() {
        $jobfair_id = input('post.jobfair_id/d', 0, 'intval');
        if (!$jobfair_id) $this->ajaxReturn(500, '请选择招聘会');
        $id = input('post.id/d', 0, 'intval');
        if (!$id) $this->ajaxReturn(500, '请选择图片');
        if (!$jobfair = model('Jobfair')->find($jobfair_id)) $this->ajaxReturn(500, '招聘会不存在');
        $reg = model('JobfairRetrospect')->where(['id' => $id, 'jobfair_id' => $jobfair['id']])->delete();
        if (false === $reg) $this->ajaxReturn(500, '删除失败');
        if (!$reg) $this->ajaxReturn(500, '删除失败,图片不存在或已经删除');
        model('AdminLog')->record(
            '删除招聘会精彩回顾。招聘会ID【' . $jobfair['id'] . '】;招聘会标题【' . $jobfair['title'] . '】,图片ID【' . $id . '】',
            $this->admininfo
        );
        $this->ajaxReturn(200, '删除成功');
    }
    public function qrcode() {
        $jobfair_id = input('get.jobfair_id/d', 0, 'intval');
        if (!$jobfair_id) $this->ajaxReturn(500, '请选择招聘会');
        $url = urlencode(config('global_config.mobile_domain') . 'jobfair/' . $jobfair_id);
        $list = [
            [
                'side' => '8cm',
                'distance' => '0.5m',
                'link' => url('qrcode/index') . '?url=' . $url . '&download=1&px=258'
            ],
            [
                'side' => '12cm',
                'distance' => '0.8m',
                'link' => url('qrcode/index') . '?url=' . $url . '&download=1&px=344'
            ],
            [
                'side' => '15cm',
                'distance' => '1m',
                'link' => url('qrcode/index') . '?url=' . $url . '&download=1&px=430'
            ],
            [
                'side' => '30cm',
                'distance' => '1.5m',
                'link' => url('qrcode/index') . '?url=' . $url . '&download=1&px=860'
            ],
            [
                'side' => '50cm',
                'distance' => '2.5m',
                'link' => url('qrcode/index') . '?url=' . $url . '&download=1&px=1280'
            ]
        ];
        $this->ajaxReturn(200, '删除成功', ['items' => $list]);
    }

    public function positionInfo() {
        $id = input('get.id/d', 0, 'intval');
        if (!$id) $this->ajaxReturn(500, '请选择展位模板');
        $jobfair = model('Jobfair')->find($id);
        if (!$jobfair) $this->ajaxReturn(500, '招聘会不存在');
        $area = model('JobfairArea')->field('id,area')->where(array('jobfair_id' => $jobfair['id']))->order('area asc')->select();
        foreach ($area as $key => $val) {
            $sids[] = $val['id'];
        }
        $position_arr = model('JobfairPosition')->field('id,area_id,position,company_id,company_name,status')->where(array('jobfair_id' => $jobfair['id']))->order('area_id asc,orderid asc')->select();
        if ($position_arr) {
            $position = array();
            foreach ($position_arr as $key => $val) {
                $position[$val['area_id']][] = [
                    'id' => $val['id'],
                    'position' => $val['position'],
                    'company_id' => $val['company_id'],
                    'company_name' => $val['company_name'],
                    'company_url' => url('index/company/show', ['id' => $val['company_id']]),
                    'status' => $val['status']
                ];
            }
            foreach ($area as $key => $val) {
                $area[$key]['positions'] = isset($position[$val['id']]) ? $position[$val['id']] : [];
            }
        }
        if ($jobfair['position_img']) {
            $position_img = model('Uploadfile')->getFileUrlBatch(explode(',', $jobfair['position_img']));
        } else {
            $position_img = [];
        }
        $this->ajaxReturn(200, '获取数据成功', [
            'items' => $area,
            'position_img' => $position_img
        ]);
    }
    public function positionAdd() {
        $data['id'] = input('post.id/d', 0, 'intval');
        if (!$data['id']) $this->ajaxReturn(500, '请选择招聘会');
        $data['area'] = input('post.area/a');
        $data['position_start'] = input('post.position_start/a');
        $data['position_end'] = input('post.position_end/a');
        foreach ($data['position_start'] as $key => $value) {
            if ($value == '') {
                $this->ajaxReturn(500, '请填写展位号');
            }
        }
        foreach ($data['position_end'] as $key => $value) {
            if ($value == '') {
                $this->ajaxReturn(500, '请填写展位号');
            }
        }
        $reg = model('JobfairPosition')->PositionAdd($data, $this->admininfo);
        $this->ajaxReturn($reg['state'] ? 200 : 500, $reg['msg']);
    }
    public function positionDelete() {
        $id = input('post.id/d', 0, 'intval');
        if (!$id) $this->ajaxReturn(500, '请选择展位');
        $jobfair_id = input('post.jobfair_id/s', '', 'trim');
        if (!$jobfair_id) $this->ajaxReturn(500, '请选择招聘会');
        $info = model('JobfairPosition')->find($id);
        if (!$info || $info['jobfair_id'] != $jobfair_id) $this->ajaxReturn(500, '展位不存在');
        if (!$jobfair = model('Jobfair')->find($jobfair_id)) $this->ajaxReturn(500, '招聘会不存在');
        if (model('JobfairPosition')->where('id', $id)->delete()) {
            if ($info['company_id']) model('JobfairExhibitors')->where('position_id', $info['id'])->delete();
            model('AdminLog')->record(
                '删除招聘会展位。招聘会ID【' . $jobfair['id'] . '】，招聘会标题【' . $jobfair['title'] . '】,展位【' . $info['position'] . '】',
                $this->admininfo
            );
            $this->ajaxReturn(200, '删除成功');
        }
        $this->ajaxReturn(500, '删除失败');
    }
    public function positionBatchDelete() {
        $id = input('post.id/d', 0, 'intval');
        if (!$id) $this->ajaxReturn(500, '请选择招聘会');
        $area = input('post.area/s', '', 'trim');
        if (!$area) $this->ajaxReturn(500, '请选择展区');
        $position_start = input('post.position_start/d', 0, 'intval');
        $position_end = input('post.position_end/d', 0, 'intval');
        if (!$position_start || !$position_end) $this->ajaxReturn(500, '请填写展位');
        if (!$jobfair = model('Jobfair')->find($id)) $this->ajaxReturn(500, '招聘会不存在');
        for ($i = $position_start; $i <= $position_end; $i++) {
            $position = model('JobfairPosition')->where(['jobfair_id' => $jobfair['id'], 'position' => $area . $i])->find();
            if ($position) {
                model('JobfairExhibitors')->where('position_id', $position['id'])->delete();
                model('JobfairPosition')->where('id', $position['id'])->delete();
            }
        }
        model('AdminLog')->record(
            '批量删除招聘会展位。招聘会ID【' . $jobfair['id'] . '】，招聘会标题【' . $jobfair['title'] . '】,展位【' . $position_start . '-' . $position_end . '】',
            $this->admininfo
        );
        $this->ajaxReturn(200, '删除成功');
    }
    public function areaDelete() {
        $id = input('post.id/d', 0, 'intval');
        if (!$id) $this->ajaxReturn(500, '请选择招聘会');
        $area = input('post.area/d', '', 'intval');
        if (!$area) $this->ajaxReturn(500, '请选择展区');
        if (!$jobfair = model('Jobfair')->find($id)) $this->ajaxReturn(500, '招聘会不存在');
        if (!$areaInfo = model('JobfairArea')->find($area)) $this->ajaxReturn(500, '招聘会展区不存在');
        if (model('JobfairArea')->where('id', $area)->delete()) {
            $position_arr = model('JobfairPosition')->where(['jobfair_id' => $jobfair['id'], 'area_id' => $area])->column('id');
            model('JobfairPosition')->where(['jobfair_id' => $jobfair['id'], 'area_id' => $area])->delete();
            if ($position_arr) model('JobfairExhibitors')->where('position_id', 'in', $position_arr)->delete();
            model('AdminLog')->record(
                '删除招聘会展区。招聘会ID【' . $jobfair['id'] . '】，招聘会标题【' . $jobfair['title'] . '】,展区【' . $areaInfo['area'] . '】',
                $this->admininfo
            );
            $this->ajaxReturn(200, '删除成功');
        }
        $this->ajaxReturn(500, '删除失败');
    }
    public function positionPause() {
        $id = input('post.id/d', 0, 'intval');
        if (!$id) $this->ajaxReturn(500, '请选择展位');
        $jobfair_id = input('post.jobfair_id/d', '', 'intval');
        if (!$jobfair_id) $this->ajaxReturn(500, '请选择招聘会');
        $info = model('JobfairPosition')->find($id);
        if (!$info || $info['jobfair_id'] != $jobfair_id) $this->ajaxReturn(500, '展位不存在');
        if (!$jobfair = model('Jobfair')->find($jobfair_id)) $this->ajaxReturn(500, '招聘会不存在');
        $status = input('post.pause/d', 0, 'intval') ? 3 : 0;
        if (model('JobfairPosition')->where('id', $id)->setfield('status', $status)) {
            model('AdminLog')->record(
                '将招聘会展位状态变更为【' . ($status ? '暂停预订' : '可预订') . '】。招聘会ID【' . $jobfair['id'] . '】，招聘会标题【' . $jobfair['title'] . '】,展位【' . $info['position'] . '】',
                $this->admininfo
            );
            $this->ajaxReturn(200, '修改成功', $status);
        }
        $this->ajaxReturn(500, '修改失败');
    }
    public function jobfairTplImgAdd() {
        $id = input('post.id/d', 0, 'intval');
        if (!$id) $this->ajaxReturn(500, '请选择招聘会');
        $img = input('post.img/d', 0, 'intval');
        if (!$img) $this->ajaxReturn(500, '请上传图片');
        if (!$jobfair = model('Jobfair')->find($id)) $this->ajaxReturn(500, '招聘会不存在');
        $imgs = $jobfair['position_img'] ? explode(',', $jobfair['position_img']) : [];
        $imgs[] = $img;
        $imgs = array_unique($imgs);
        if (model('Jobfair')->where('id', $id)->setfield('position_img', implode(',', $imgs))) {
            model('AdminLog')->record(
                '招聘会新增展位图。招聘会ID【' . $jobfair['id'] . '】，招聘会标题【' . $jobfair['title'] . '】,图片ID【' . $img . '】',
                $this->admininfo
            );
            $this->ajaxReturn(200, '上传成功');
        }
        $this->ajaxReturn(500, '上传图片失败');
    }
    public function jobfairTplImgDelete() {
        $id = input('post.id/d', 0, 'intval');
        if (!$id) $this->ajaxReturn(500, '请选择展位图片');
        $jobfair_id = input('post.jobfair_id/d', 0, 'intval');
        if (!$jobfair_id) $this->ajaxReturn(500, '请选择招聘会');
        if (!$jobfair = model('Jobfair')->find($jobfair_id)) $this->ajaxReturn(500, '展位模板不存在');
        if (!$jobfair['position_img'] || !$imgs = explode(',', $jobfair['position_img'])) $this->ajaxReturn(500, '展位图不存在');
        if (!isset($imgs[array_search($id, $imgs)])) $this->ajaxReturn(500, '展位图不存在');
        unset($imgs[array_search($id, $imgs)]);
        if (model('Jobfair')->where('id', $jobfair_id)->setfield('position_img', count($imgs) > 0 ? implode(',', $imgs) : '')) {
            model('AdminLog')->record(
                '招聘会删除展位图。招聘会ID【' . $jobfair['id'] . '】，招聘会标题【' . $jobfair['title'] . '】,图片ID【' . $id . '】',
                $this->admininfo
            );
            $this->ajaxReturn(200, '删除成功');
        }
        $this->ajaxReturn(500, '删除失败');
    }
}
