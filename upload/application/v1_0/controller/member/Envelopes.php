<?php

namespace app\v1_0\controller\member;

class Envelopes extends \app\v1_0\controller\common\Base {
    /** 用户余额等查询 */
    public function balance() {
        $return  = [];
        $money = model('MemberBalance')->getMemberBalance($this->userinfo->uid);
        $bind = model('MemberBind')->where('uid', $this->userinfo->uid)->find();
        if (empty($bind)) {
            $return['bind'] = 0;
        } else {
            $return['bind'] = 1;
        }
        $return['wechat_qrcode'] = model('Uploadfile')->getFileUrl(config('global_config.wechat_qrcode'));
        $return['invite_register_is_open'] = config('global_config.invite_register')['is_open'];
        $return['tips'] = config('global_config.withdrawal')['tips'];
        $return['amount_limit'] = config('global_config.withdrawal')['amount_limit'];
        $return['amount_setting'] = config('global_config.withdrawal')['amount_setting'];
        $invite_register = config('global_config.invite_register');
        $return['invite_register_price'] = $invite_register['price'];
        $return['money'] = $money;
        $member_balance = model('MemberBalance')
            ->where('uid', $this->userinfo->uid)
            ->find();
        $return['is_blacklist'] = $member_balance['is_blacklist'];
        $return['total_money'] = model('MemberBalanceLog')
            ->where('op', 1)
            ->where('uid', $this->userinfo->uid)
            ->sum('money');
        $this->ajaxReturn(200, '红包余额查询', $return);
    }
    /** 红包记录 */
    public function index() {
        $where['uid'] = $this->userinfo->uid;
        $current_page = input('get.page/d', 1, 'intval');
        $pagesize = input('get.pagesize/d', 10, 'intval');
        $list = model('MemberBalanceLog')
            ->where($where)
            ->where('op', 1)
            ->order('id desc')
            ->page($current_page . ',' . $pagesize)
            ->select();
        $this->ajaxReturn(200, '获取数据成功', ['items' => $list]);
    }
    /** 提现记录 */
    public function withdrawalList() {
        $where['uid'] = $this->userinfo->uid;
        $current_page = input('get.page/d', 1, 'intval');
        $pagesize = input('get.pagesize/d', 10, 'intval');
        $list = model('MemberWithdrawalRecord')
            ->where($where)
            ->order('id desc')
            ->page($current_page . ',' . $pagesize)
            ->select();
        foreach ($list as $k => $v) {
            if ($v['state'] == 0) {
                $list[$k]['state_msg'] = '拒绝';
            } else if ($v['state'] == 2) {
                $list[$k]['state_msg'] = '审核中';
            } else {
                $list[$k]['state_msg'] = '同意';
            }
        }
        $this->ajaxReturn(200, '获取数据成功', ['items' => $list]);
    }
    /** 申请提现 */
    public function withdrawal() {
        $uid = $this->userinfo->uid;
        $withdrawal = config('global_config.withdrawal');
        $member = model('Member')->where('uid', $uid)->find();
        if ($withdrawal['enterprise_certification'] == 1) {
            if ($member['utype'] == 1) {
                $company = model('Company')->where('uid', $uid)->find();
                if ($company['audit'] != 1) {
                    $this->ajaxReturn(200, '请先完成企业认证');
                }
            }
        }
        $money = input('get.money/s', 0, 'trim');
        //最低提现额度需要后台配置

        if ($withdrawal['amount_limit'] == 0) {
            $withdrawal = config('global_config.withdrawal')['amount_setting'];
            if ($withdrawal > $money) {
                $this->ajaxReturn(500, '提现金额不能低于' . $withdrawal . '元');
            }
        }
        $member_balance = model('MemberBalance')->where('uid', $uid)->find();
        if ($member_balance['money'] - $money < 0) {
            $this->ajaxReturn(500, '可用余额不足');
        }
        $member_bind = model('MemberBind')->where('uid', $uid)->find();
        if (empty($member_bind)) {
            $this->ajaxReturn(500, '未绑定微信');
        }
        $res = model('MemberWithdrawalRecord')->save([
            'uid' => $uid,
            'price' => $money,
            'addtime' => time(),
            'state' => 2
        ]);
        if ($res === false) {
            $this->ajaxReturn(500, '提交申请失败');
        }
        model('MemberBalance')->where('uid', $uid)->setDec('money', $money);
        $this->ajaxReturn(200, '提交申请成功');
    }
    /** 分享海报二维码 */
    public function share() {
        $uid = $this->userinfo->uid;
        $info = model('MemberBalance')
            ->where('uid', $uid)
            ->find();
        if (empty($info)) {
            model('MemberBalance')->save([
                'uid' => $uid,
                'money' => 0,
                'is_blacklist' => 0,
                'token' => uuid()
            ]);
        }
        $poster = new \app\common\lib\Poster;
        $result = $poster->redEnvelopes($uid);
        $this->ajaxReturn(200, '生成海报成功', $result);
    }
    /** 邀请记录 */
    public function invitationRecord() {
        $where['pid'] = $this->userinfo->uid;
        $current_page = input('get.page/d', 1, 'intval');
        $pagesize = input('get.pagesize/d', 10, 'intval');
        $list = model('MemberInviteRegister')
            ->where($where)
            ->order('id desc')
            ->page($current_page . ',' . $pagesize)
            ->select();
        foreach ($list as $k => $v) {
            if ($v['state'] == 0) {
                $list[$k]['state_msg'] = '邀请中';
            } else {
                $list[$k]['state_msg'] = '邀请成功';
            }
            $member = model('Member')->where('uid', $v['uid'])->find();
            if ($member['utype'] == 1) {
                $company = model('Company')->where('uid', $v['uid'])->find();
                if (!empty($company)) {
                    $list[$k]['username'] = $company['companyname'];
                } else {
                    $list[$k]['username'] = $member['username'];
                }
            }
            if ($member['utype'] == 2) {
                $resume = model('Resume')->where('uid', $v['uid'])->find();
                if (!empty($resume)) {
                    $list[$k]['username'] = $resume['fullname'];
                } else {
                    $list[$k]['username'] = $member['username'];
                }
            }
        }
        $this->ajaxReturn(200, '获取数据成功', $list);
    }
}
