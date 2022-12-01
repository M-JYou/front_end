<?php

namespace app\apiadmin\controller;

class RecruitmentTodayConfig extends \app\common\controller\Backend {
    public function index() {
        if (request()->isGet()) {
            $info = model('RecruitmentTodayConfig')->column('name,value');
            $info['logoUrl'] = model('Uploadfile')->getFileUrl($info['logo']);

            foreach ($info as $key => $value) {

                if (is_json($value)) {
                    $info[$key] = json_decode($value, true);
                }
            }
            $this->ajaxReturn(200, '获取数据成功', $info);
        } else {
            $inputdata = input('post.');
            $configlist = model('RecruitmentTodayConfig')->column('name,id');
            $sqldata = [];
            $setmeal_id = [];
            foreach ($inputdata['setmeal_id'] as $k => $v) {
                array_push($setmeal_id, intval($v));
            }
            $inputdata['setmeal_id'] = $setmeal_id;
            foreach ($inputdata as $key => $value) {

                if (!isset($configlist[$key])) {
                    continue;
                }
                $arr['id'] = $configlist[$key];
                $arr['name'] = $key;
                if (is_array($value)) {
                    $arr['value'] = json_encode($value, JSON_UNESCAPED_UNICODE);
                } else {
                    $arr['value'] = $value;
                }
                if ($arr['name'] == 'subject_name' && mb_strlen($value, 'UTF-8') > 4) {
                    $this->ajaxReturn(400, '主题名称最多可输入四个字');
                }
                $sqldata[] = $arr;
            }
            model('RecruitmentTodayConfig')
                ->isUpdate()
                ->saveAll($sqldata);
            $name_list = [];
            foreach ($sqldata as $key => $value) {
                $name_list[] = $value['name'];
            }
            model('AdminLog')->record(
                '修改今日招聘配置信息。配置标识【' . implode(',', $name_list) . '】',
                $this->admininfo
            );
            $this->ajaxReturn(200, '保存数据成功');
        }
    }
}
