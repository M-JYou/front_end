<?php

namespace app\v1_0\controller\home;

use app\v1_0\controller\qscmslib\Magapp;
use app\v1_0\controller\qscmslib\Qianfanyunapp;

class MobileClass extends \app\v1_0\controller\common\Base
{
    protected $visitor = null;

    public function getAppUser()
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];

        $Magappx = strpos($userAgent, "MAGAPPX");
        $QianFan = strpos($userAgent, "QianFan");
        $getUser = [];
        if (!empty($Magappx) && !isset($this->visitor['login']) && config('global_config.magappx_secret'))//判断是否马甲app
        {
            $getUser = $this->magappx();//跳转马甲
        }

        if (!empty($QianFan) && !isset($this->visitor['login']) && config('global_config.qianfan_token'))//判断是否千帆app
        {
            $getUser = $this->qianfanyunapp();//跳转千帆
        }
        $data['type'] = 'mobile';
        $data['is_login'] = false;
        if (isset($getUser) && $getUser) {//判断app是否登录，app登录后se登录
            if ($getUser['code'] == 200) {
                $data = $this->login();
                $data['type'] = 'app';
                $data['is_login'] = true;
                $this->ajaxReturn(200, '获取数据成功', $data);//前段进行绑定登录信息跳转会员中心页面
            } else {
                $data['type'] = 'app';
                $this->ajaxReturn(200, '暂未注册', $data);//前段跳转注册会员企业选择页面
            }

        }
        $this->ajaxReturn(200, '暂未注册', $data);//前段跳转注册会员企业选择页面

    }

    protected function magappx()
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];

        $info = strstr($userAgent, "MAGAPPX");
        $info = explode('|', $info);

        $agent = array(
            'name' => $info[0],
            'token' => $info[7]//用户token 用户的token信息
        );
        if ($agent['name'] == 'MAGAPPX') {
            if ($agent['token']) {
                $magapp = new Magapp();
                $user = $magapp->get_user_info($agent['token']);//获取app用户登录信息
                if (false !== $user) {
                    $userbind = model('MemberBind')->where(['type' => 'magapp', 'magapp_uid' => $user['id']])->find();//查看app会员绑定
                    if ($userbind) {
                        $this->visitor['login'] = $userbind['uid'];
                        return ['code' => 200, 'msg' => '', 'data' => $userbind['uid']];
                    } else {
                        $member = model('Member')->where(['mobile' => $user['phone']])->field('uid,utype,mobile')->find();
                        if (!empty($member)) {
                            /**
                             * 【BUG】马甲APP绑定错误
                             */
                            $insert_result = model('MemberBind')
                                ->insert([
                                    'uid' => $member['uid'],
                                    'type' => 'magapp',
                                    'openid' => '',
                                    'unionid' => '',
                                    'nickname' => $user['username'],
                                    'avatar' => $user['avatar'],
                                    'bindtime' => time(),
                                    'magapp_uid' => $user['id']
                                ]);
                            if ($insert_result) {
                                return ['code' => 200, 'msg' => '', 'data' => $member['uid']];
                            }
                        }
                        return ['code' => 401, 'msg' => '暂未注册', 'data' => 0];
                    }
                }
            }
        }
        return false;
    }

    protected function qianfanyunapp()
    {
        if (isset($_COOKIE['wap_token'])) {
            $qianfanyunapp = new Qianfanyunapp();
            $s = $qianfanyunapp->is_login();//查看千帆是否登录
            if (false !== $s) {
                $user = $qianfanyunapp->get_user_info($s);//获取app用户登录信息
                if (false !== $user) {
                    $userbind = model('MemberBind')->where(['type' => 'qianfanyunapp', 'qianfanyunapp_uid' => $user['id']])->find();
                    if ($userbind) {
                        $this->visitor['login'] = $userbind['uid'];
                        return ['code' => 200, 'msg' => '', 'data' => $userbind['uid']];
                    } else {//未绑定千帆会员
                        $member = model('Member')->where(['mobile' => $user['phone']])->field('uid,utype,mobile')->find();
                        if (!empty($member)) {
                            /**
                             * 【BUG】千帆APP绑定错误
                             */
                            $insert_result = model('MemberBind')
                                ->insert([
                                    'uid' => $member['uid'],
                                    'type' => 'qianfanyunapp',
                                    'openid' => '',
                                    'unionid' => '',
                                    'nickname' => $user['username'],
                                    'avatar' => $user['avatar'],
                                    'bindtime' => time(),
                                    'qianfanyunapp_uid' => $user['id']
                                ]);
                            if ($insert_result) {
                                return ['code' => 200, 'msg' => '', 'data' => $member['uid']];
                            }
                        }
                        return ['code' => 401, 'msg' => '暂未注册', 'data' => 0];
                    }
                }
            }
        }
        return false;
    }

    //登录
    protected function login()
    {
        config('platform', 'app');
        $user = model('Member')->where(['uid' => $this->visitor['login']])->field('uid,utype,mobile,password')->find()->toArray();
        if ($user) {
            return $this->loginExtra(
                $user['uid'],
                $user['utype'],
                $user['mobile']
            );
        }
    }

}
