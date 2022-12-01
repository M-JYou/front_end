<?php

namespace app\apiadmin\controller;

class JobfairPosition extends \app\common\controller\Backend {
    public function _initialize() {
        parent::_initialize();
    }
    public function index() {
        $where = [];
        $status = input('get.status/d', 0, 'intval');
        $key_type = input('get.key_type/d', 0, 'intval');
        $keyword = input('get.keyword/s', '', 'trim');
        $current_page = input('get.page/d', 1, 'intval');
        $pagesize = input('get.pagesize/d', 10, 'intval');
        if ($keyword && $key_type) {
            switch ($key_type) {
                case 1:
                    $where['title'] = ['like', '%' . $keyword . '%'];
                    break;
            }
        }
        if ($status > 0) $where['status'] = $status;
        $total = model('JobfairPositionTpl')->where($where)->count();
        $list = model('JobfairPositionTpl')->where($where)->order('id desc')->page($current_page . ',' . $pagesize)->select();
        $return['items'] = $list;
        $return['total'] = $total;
        $return['current_page'] = $current_page;
        $return['pagesize'] = $pagesize;
        $return['total_page'] = ceil($total / $pagesize);
        $this->ajaxReturn(200, '获取数据成功', $return);
    }
    public function delete() {
        $id = input('post.id/a');
        if (!$id) $this->ajaxReturn(500, '请选择展位模板');
        $reg = model('JobfairPositionTpl')->tplDelete($id, $this->admininfo);
        $this->ajaxReturn($reg['state'] ? 200 : 500, $reg['msg']);
    }
    public function add() {
        $data['title'] = input('post.title/s', '', 'trim');
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
        $reg = model('JobfairPositionTpl')->tplAdd($data, $this->admininfo);
        $this->ajaxReturn($reg['state'] ? 200 : 500, $reg['msg']);
    }
    public function info() {
        $id = input('get.id/d', 0, 'intval');
        if (!$id) $this->ajaxReturn(500, '请选择展位模板');
        $position = model('JobfairPositionTpl')->find($id);
        if (!$position) $this->ajaxReturn(500, '展位模板不存在');
        $position['area'] = unserialize($position['area']);
        $position['position'] = unserialize($position['position']);
        $position['position_img'] = $position['position_img'] ? model('Uploadfile')->getFileUrlBatch(explode(',', $position['position_img'])) : [];
        $this->ajaxReturn(200, '获取成功', ['info' => $position]);
    }
    public function positionAdd() {
        $data['id'] = input('post.id/d', 0, 'intval');
        if (!$data['id']) $this->ajaxReturn(500, '请选择展位模板');
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
        $reg = model('JobfairPositionTpl')->tplPositionAdd($data, $this->admininfo);
        $this->ajaxReturn($reg['state'] ? 200 : 500, $reg['msg']);
    }
    public function positionDelete() {
        $id = input('post.id/d', 0, 'intval');
        if (!$id) $this->ajaxReturn(500, '请选择展位模板');
        $area = input('post.area/s', '', 'trim');
        if (!$area) $this->ajaxReturn(500, '请选择展区');
        $position = input('post.position/s', '', 'trim');
        if (!$position) $this->ajaxReturn(500, '请选择展位');
        if (!$info = model('JobfairPositionTpl')->find($id)) $this->ajaxReturn(500, '展位模板不存在');
        $data = unserialize($info['position']);
        unset($data[$area][array_search($position, $data[$area])]);
        $data[$area] = array_values($data[$area]);
        if (model('JobfairPositionTpl')->where('id', $id)->setfield('position', serialize($data))) {
            model('AdminLog')->record(
                '删除招聘会展位模板的展位。展位模板ID【' . $info['id'] . '】;展位模板标题【' . $info['title'] . '】,展位【' . $position . '】',
                $this->admininfo
            );
            $this->ajaxReturn(200, '删除成功');
        }
        $this->ajaxReturn(500, '删除失败');
    }
    public function positionBatchDelete() {
        $id = input('post.id/d', 0, 'intval');
        if (!$id) $this->ajaxReturn(500, '请选择展位模板');
        $area = input('post.area/s', '', 'trim');
        if (!$area) $this->ajaxReturn(500, '请选择展区');
        $position_start = input('post.position_start/d', 0, 'intval');
        $position_end = input('post.position_end/d', 0, 'intval');
        if (!$position_start || !$position_end) $this->ajaxReturn(500, '请填写展位');
        if (!$info = model('JobfairPositionTpl')->find($id)) $this->ajaxReturn(500, '展位模板不存在');
        $position = unserialize($info['position']);
        for ($i = $position_start; $i <= $position_end; $i++) {
            if (in_array($area . $i, $position[$area])) {
                unset($position[$area][array_search($area . $i, $position[$area])]);
            }
        }
        if (model('JobfairPositionTpl')->where('id', $id)->setfield('position', serialize($position))) {
            model('AdminLog')->record(
                '批量删除招聘会展位模板的展位。展位模板ID【' . $info['id'] . '】;展位模板标题【' . $info['title'] . '】,展位【' . $position_start . '-' . $position_end . '】',
                $this->admininfo
            );
            $this->ajaxReturn(200, '删除成功');
        }
        $this->ajaxReturn(500, '删除失败');
    }
    public function areaDelete() {
        $id = input('post.id/d', 0, 'intval');
        if (!$id) $this->ajaxReturn(500, '请选择展位模板');
        $area = input('post.area/s', '', 'trim');
        if (!$area) $this->ajaxReturn(500, '请选择展区');
        if (!$info = model('JobfairPositionTpl')->find($id)) $this->ajaxReturn(500, '展位模板不存在');
        $edit_sql['area'] = unserialize($info['area']);
        $edit_sql['position'] = unserialize($info['position']);
        unset($edit_sql['area'][array_search($area, $edit_sql['area'])]);
        unset($edit_sql['position'][$area]);
        $edit_sql['area'] = serialize(array_values($edit_sql['area']));
        $edit_sql['position'] = serialize($edit_sql['position']);
        if (model('JobfairPositionTpl')->where('id', $id)->setfield($edit_sql)) {
            model('AdminLog')->record(
                '删除招聘会展位模板的展区。展位模板ID【' . $info['id'] . '】;展位模板标题【' . $info['title'] . '】,展区【' . $area . '】',
                $this->admininfo
            );
            $this->ajaxReturn(200, '删除成功');
        }
        $this->ajaxReturn(500, '删除失败');
    }
    public function imgAdd() {
        $id = input('post.id/d', 0, 'intval');
        if (!$id) $this->ajaxReturn(500, '请选择展位模板');
        $img = input('post.img/d', 0, 'intval');
        if (!$img) $this->ajaxReturn(500, '请上传图片');
        if (!$position = model('JobfairPositionTpl')->find($id)) $this->ajaxReturn(500, '展位模板不存在');
        $imgs = $position['position_img'] ? explode(',', $position['position_img']) : [];
        $imgs[] = $img;
        $imgs = array_unique($imgs);
        if (model('JobfairPositionTpl')->where('id', $id)->setfield('position_img', implode(',', $imgs))) {
            model('AdminLog')->record(
                '招聘会展位模板新增展位图。展位模板ID【' . $position['id'] . '】;展位模板标题【' . $position['title'] . '】,图片ID【' . $img . '】',
                $this->admininfo
            );
            $this->ajaxReturn(200, '上传成功');
        }
        $this->ajaxReturn(500, '上传图片失败');
    }
    public function imgDelete() {
        $id = input('post.id/d', 0, 'intval');
        if (!$id) $this->ajaxReturn(500, '请选择展位图片');
        $pid = input('post.pid/d', 0, 'intval');
        if (!$pid) $this->ajaxReturn(500, '请选择展位模板');
        if (!$position = model('JobfairPositionTpl')->find($pid)) $this->ajaxReturn(500, '展位模板不存在');
        if (!$position['position_img'] || !$imgs = explode(',', $position['position_img'])) $this->ajaxReturn(500, '展位图不存在');
        if (!isset($imgs[array_search($id, $imgs)])) $this->ajaxReturn(500, '展位图不存在');
        unset($imgs[array_search($id, $imgs)]);
        if (model('JobfairPositionTpl')->where('id', $pid)->setfield('position_img', count($imgs) > 0 ? implode(',', $imgs) : '')) {
            model('AdminLog')->record(
                '删除招聘会展位模板的展位图。展位模板ID【' . $position['id'] . '】;展位模板标题【' . $position['title'] . '】,图片ID【' . $id . '】',
                $this->admininfo
            );
            $this->ajaxReturn(200, '删除成功');
        }
        $this->ajaxReturn(500, '删除失败');
    }
}
