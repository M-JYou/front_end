<?php

namespace app\v1_0\controller\home;

use app\common\lib\Pager;

class Live extends \app\v1_0\controller\common\Base {
    protected $baseUrl = '';
    public function _initialize() {
        parent::_initialize();
        $this->baseUrl = 'https://live.74cms.com/';
    }

    public function pageList() {

        $current_page = input('get.page/d', 1, 'intval');
        $pagesize = input('get.pagesize/d', 10, 'intval');
        $result = curl_file_get_contents($this->baseUrl . 'index/Live/index?page=' . $current_page . '&pagesize=' . $pagesize, config('global_config.live_app_key'), config('global_config.live_app_secret'));
        $result_list = json_decode($result, true);
        if ($result_list['code'] != 200) {
            $this->ajaxReturn(500, $result_list['message']);
        }
        $this->ajaxReturn(200, '获取数据成功', $result_list['data']);
    }
}
