<?php

namespace app\apiadmin\controller;

use app\common\controller\Backend;

class Notification extends \app\common\controller\Backend {
    public function index() {
        $data = model('AdminNotice')->select();
        foreach ($data as $k => $v) {
            if ($v['is_open'] == 1) {
                $data[$k]['is_open'] = true;
            } else {
                $data[$k]['is_open'] = false;
            }
            $config_arr = model('AdminNoticeConfig')
                ->alias('c')
                ->join(
                    config('database.prefix') . 'admin a',
                    'c.admin_id=a.id',
                    'LEFT'
                )
                ->where('notice_id', $v['id'])
                ->select();
            $data[$k]['tag'] = array_column($config_arr, 'admin_id');
            $username_arr = array_column($config_arr, 'username');
            $data[$k]['admin_name'] = $username_arr;
        }
        $this->ajaxReturn(200, '获取数据成功', $data);
    }
    public function adminList() {
        $admin_list =  model('admin')->field('id,username')->select();
        $list = [];
        foreach ($admin_list as $k => $v) {
            $list[$k]['value'] = $v['id'];
            $list[$k]['label'] = $v['username'];
            $list[$k]['tag'] = [];
        }
        $this->ajaxReturn(200, '获取数据成功', $list);
    }
    /** 保存 */
    public function save() {
        $notice_id = input('post.notice_id/d', 0, 'intval');
        $tag = input('post.tag/s', '', 'trim');
        if (!isset($notice_id) || empty($notice_id)) {
            $this->ajaxReturn(500, '通知项ID有误');
        }
        $AdminNotice['title'] = '';
        if ($tag != '') {
            $tag_arr = explode(',', $tag);
            //解决 管理员微信通知数量不对应问题（前段代码统计条数错误，由前段限制改为接口限制）
            if (count($tag_arr) > 10) {
                $this->ajaxReturn(500, '管理员最多可以添加10位');
            }
            model('AdminNoticeConfig')->destroy(['notice_id' => $notice_id]);
            $AdminNotice = model('AdminNotice')->where('id', $notice_id)->find();
            if (!empty($tag_arr)) {
                $add  = [];
                foreach ($tag_arr as $k => $v) {
                    $add[$k]['admin_id'] = $v;
                    $add[$k]['notice_id'] = $notice_id;
                }
                $res = model('AdminNoticeConfig')->saveAll($add);
                if ($res === false) {
                    $this->ajaxReturn(500, model('AdminNoticeConfig')->getError());
                }
            }
        }
        model('AdminLog')->record(
            '编辑待办事项通知ID【' .
                $notice_id .
                '】;通知项【' .
                $AdminNotice['title'] .
                '】',
            $this->admininfo
        );
        $this->ajaxReturn(200, '编辑成功');
    }
    /** 开启/关闭 */
    public function switchButton() {
        $notice_id = input('get.notice_id/d', 0, 'intval');
        if (!isset($notice_id) || empty($notice_id)) {
            $this->ajaxReturn(500, '通知项ID有误');
        }
        $AdminNotice = model('AdminNotice')->where('id', $notice_id)->find();
        if (empty($AdminNotice)) {
            $this->ajaxReturn(500, '未查询到指定通知项');
        }
        if ($AdminNotice['is_open'] == 0) {
            $res = model('AdminNotice')->save(['is_open' => 1], ['id' => $notice_id]);
            if ($res === false) {
                $this->ajaxReturn(500, model('AdminNotice')->getError());
            }
        } else {
            $res = model('AdminNotice')->save(['is_open' => 0], ['id' => $notice_id]);
            if ($res === false) {
                $this->ajaxReturn(500, model('AdminNotice')->getError());
            }
        }
        model('AdminLog')->record(
            '编辑待办事项通知ID【' .
                $notice_id .
                '】;通知项【' .
                $AdminNotice['title'] .
                '】',
            $this->admininfo
        );
        $this->ajaxReturn(200, '编辑成功');
    }
}
