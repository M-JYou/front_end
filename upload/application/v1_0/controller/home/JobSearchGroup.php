<?php
namespace app\v1_0\controller\home;

class JobSearchGroup extends \app\v1_0\controller\common\Base
{
    public function _initialize()
    {
        parent::_initialize();
    }
    //获取求职群列表
    public function index()
    {
        $info = model('JobSearchGroup')->column('name,value');
        $info['groupPortraitUrl'] = !empty($info['group_head_portrait']) ? model('Uploadfile')->getFileUrl($info['group_head_portrait']) : default_empty('job_search_group');
        $info['qrcode'] = model('Uploadfile')->getFileUrl($info['qrcode']);
        foreach ($info as $key => $value) {

            if (is_json($value)) {
                $info[$key] = json_decode($value, true);
            }
        }
        $this->ajaxReturn(200, '获取数据成功', $info);
    }

    //获取企业登录信息
    public function getLoginInformation()
    {
        $uid = input('get.uid/d', 0, 'intval');
        if ($uid>0)
        {
            $logo = model('member')->where(['uid'=>$uid])->field('last_login_ip,last_login_time')->find();
            $logo['audit']  = model('Company')->where(['uid'=>$uid])->value('audit');
            $this->ajaxReturn(200, '获取数据成功', $logo);
        }else{
            $this->ajaxReturn(200, '获取数据成功', []);
        }
    }
}
