<?php

namespace app\apiadmin\controller;

use think\Request;

class RedEnvelopes  extends \app\common\controller\Backend {
    /**
     * 红包配置修改
     * @return void
     */
    public function save() {
        $value = input('post.value/a', '', 'trim');
        $type = input('post.type/s', '', 'trim');
        $res = model('Config')->save(['value' => json_encode($value)], ['name' => $type]);
        if ($res === false) {
            $this->ajaxReturn(500, '修改配置失败');
        }
        model('AdminLog')->record(
            '红包配置修改',
            $this->admininfo
        );
        $this->ajaxReturn(200, '修改配置成功');
    }
    public function logList() {
        $where = [];
        $id = input('get.id/d', 0, 'intval');
        $basics = [];
        if (isset($id) && $id != 0) {
            $where['uid'] = $id;
            $member = model('Member')->where('uid', $id)->find();
            $member_bind = model('MemberBind')->where('uid', $id)->find();
            if (empty($member_bind)) {
                $basics['is_weixin'] = 0;
            } else {
                $basics['is_weixin'] = 1;
            }
            if ($member['utype'] == 1) {

                $company = model('company')->where('uid', $id)->find();
                if ($company['audit'] == 1) {
                    $audit = '已通过企业认证';
                } else {
                    $audit = '未通过企业认证';
                }
                $basics['basics'] = $company['companyname'] . '/' . $audit;
                $basics['link'] = url('index/company/show', ['id' => $company['id']]);
            }
            if ($member['utype'] == 2) {
                $resume = model('Resume')->where('uid', $id)->find();
                $basics['link'] = url('index/resume/show', ['id' =>  $resume['id']]);
                $basics['basics'] = $resume['fullname'] . '/' . model('Resume')->map_sex[$resume['sex']] . '/' . (date('Y') - intval($resume['birthday'])) . '岁 简历完整度' . model('Resume')->countCompletePercent(0, $id) . '%';
            }
            $basics['utype'] = $member['utype'];
            $basics['mobile'] = $member['mobile'];
            $basics['reg_time'] = $member['reg_time'];
        }
        $current_page = input('get.page/d', 1, 'intval');
        $pagesize = input('get.pagesize/d', 10, 'intval');
        $total = model('MemberBalanceLog')
            ->where($where)
            ->count();
        $list = model('MemberBalanceLog')
            ->order('id desc')
            ->where($where)
            ->page($current_page . ',' . $pagesize)
            ->select();
        foreach ($list as $k => $v) {
            $member = model('Member')->where('uid', $v['uid'])->find();
            if ($member['utype'] == 1) {
                $company = model('Company')->where('uid', $member['uid'])->find();
                if (!empty($company)) {
                    $list[$k]['username'] = $company['companyname'];
                }
                $list[$k]['utype_msg'] = '企业会员';
            }
            if ($member['utype'] == 2) {
                $resume = model('Resume')->where('uid', $member['uid'])->find();
                if (!empty($resume)) {
                    $list[$k]['username'] = $resume['fullname'];
                }
                $list[$k]['utype_msg'] = '个人会员';
            }
        }
        $return['items'] = $list;
        $return['basics'] = $basics;
        $return['total'] = $total;
        $return['current_page'] = $current_page;
        $return['pagesize'] = $pagesize;
        $return['total_page'] = ceil($total / $pagesize);
        $this->ajaxReturn(200, '获取数据成功', $return);
    }

    /** 用户余额查询 */
    public function userBalanceList() {
        $where = [];
        $key_type = input('get.key_type/d', 0, 'intval');
        $keyword = input('get.keyword/s', '', 'trim');
        $status = input('get.status/d', 0, 'intval');
        $utype = input('get.utype/d', 0, 'intval');
        $is_openid = input('get.is_openid/d', 0, 'intval');
        $current_page = input('get.page/d', 1, 'intval');
        $pagesize = input('get.pagesize/d', 10, 'intval');
        if ($keyword && $key_type) {
            switch ($key_type) {
                case 1:
                    $where['a.uid'] = ['eq', $keyword];
                    break;
                case 2:
                    $where['a.username'] = ['like', '%' . $keyword . '%'];
                    break;
                case 3:
                    $where['a.mobile'] = ['like', '%' . $keyword . '%'];
                    break;
                case 4:
                    $where['o.companyname'] = ['like', '%' . $keyword . '%'];
                    break;
                case 5:
                    $where['c.fullname'] = ['like', '%' . $keyword . '%'];
                    break;
                default:
                    break;
            }
        }

        if ($utype > 0) {
            $where['a.utype'] = $utype;
        }
        if ($status === 1) {
            $where['b.is_blacklist'] = ['eq', 1];
        }
        $wheres = '';
        if ($is_openid === 1) {
            $wheres .= 'd.id is not null';
        } elseif ($is_openid === 2) {
            $wheres .= 'd.id is null';
        }
        $total = model('Member')
            ->alias('a');
        $total = $total->join(config('database.prefix') . 'company o', 'a.uid=o.uid', 'LEFT')
            ->join(config('database.prefix') . 'resume c', 'a.uid=c.uid', 'LEFT')
            ->where(function ($query) {
                $query->where('(o.companyname !="" or o.companyname is not  NULL) AND a.utype=1')->whereOr('c.fullname is not null AND a.utype=2');
            });
        if ($key_type == 4) {
            $total = $total->where('a.utype', 1)
                ->where('o.companyname', 'neq', '');
        }
        if ($key_type == 5) {
            $total = $total->where('a.utype', 2)
                ->where('c.fullname', 'NOT NULL');
        }
        $total = $total
            ->join(config('database.prefix') . 'member_bind d', 'd.uid=a.uid and d.type="weixin"', 'left')
            ->join(config('database.prefix') . 'member_balance b', 'b.uid=a.uid', 'left')
            ->field('a.*,b.is_blacklist,b.is_blacklist,b.money,d.openid')
            ->where($where)
            ->where($wheres)
            ->count();
        $list = model('Member')->alias('a');
        $list = $list->join(config('database.prefix') . 'company o', 'a.uid=o.uid', 'LEFT')
            ->join(config('database.prefix') . 'resume c', 'a.uid=c.uid', 'LEFT')
            ->where(function ($query) {
                $query->where('(o.companyname !="" or o.companyname is not  NULL) AND a.utype=1')->whereOr('c.fullname is not null AND a.utype=2');
            });
        if ($key_type == 4) {
            $list = $list->where('a.utype', 1)
                ->where('o.companyname', 'neq', '');
        }
        if ($key_type == 5) {
            $list = $list->where('a.utype', 2)
                ->where('c.fullname', 'NOT NULL');
        }
        $list = $list
            ->join(config('database.prefix') . 'member_bind d', 'd.uid=a.uid and d.type="weixin"', 'left')
            ->join(config('database.prefix') . 'member_balance b', 'b.uid=a.uid', 'left')
            ->field('a.*,b.is_blacklist,b.is_blacklist,b.money,d.openid')
            ->order('a.uid desc')
            ->where($where)
            ->where($wheres)
            ->page($current_page . ',' . $pagesize)
            ->select();
        foreach ($list as $k => $v) {
            if ($v['utype'] == 1) {
                $company = model('Company')->where('uid', $v['uid'])->find();
                if (!empty($company)) {
                    $list[$k]['username'] = $company['companyname'];
                }
                $list[$k]['utype_msg'] = '企业会员';
            }
            if ($v['utype'] == 2) {
                $resume = model('Resume')->where('uid', $v['uid'])->find();
                if (!empty($resume)) {
                    $list[$k]['username'] = $resume['fullname'];
                }
                $list[$k]['utype_msg'] = '个人会员';
            }
            if (isset($v['openid']) && $v['openid'] == null) {
                $list[$k]['bind_msg'] = '未绑定';
            } else {
                $list[$k]['bind_msg'] = '已绑定';
            }
            $member_balance = model('MemberBalance')->where('uid', $v['uid'])->field('is_blacklist')->find();
            if ($member_balance['is_blacklist'] == 0) {
                $list[$k]['is_blacklist_msg'] = '否';
            } else {
                $list[$k]['is_blacklist_msg'] = '是';
            }
            $list[$k]['money'] = model('MemberBalance')->getMemberBalance($v['uid']);
            $list[$k]['sum_money']  = model('MemberBalanceLog')->where('uid', $v['uid'])->sum('money');
            $list[$k]['withdrawal_count'] = model('member_withdrawal_record')->where('uid', $v['uid'])->count();
        }
        $return['items'] = $list;
        $return['total'] = $total;
        $return['current_page'] = $current_page;
        $return['pagesize'] = $pagesize;
        $return['total_page'] = ceil($total / $pagesize);
        $this->ajaxReturn(200, '获取数据成功', $return);
    }

    /**
     * 拉黑操作
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function block() {
        $uid = input('get.uid/d', 1, 'intval');
        $member_balance = model('MemberBalance')->where('uid', $uid)->find();
        if (empty($member_balance)) {
            $res = model('MemberBalance')->save([
                'uid' => $uid,
                'money' => 0,
                'is_blacklist' => 1
            ]);
        } else {
            $res = model('MemberBalance')->save(['is_blacklist' => 1], ['uid' => $uid]);
        }
        if ($res === false) {
            $this->ajaxReturn(500, '拉黑失败');
        }
        model('AdminLog')->record(
            '将用户ID为【' . $uid . '】,进行拉黑，拉黑后将无法提现',
            $this->admininfo
        );
        $this->ajaxReturn(200, '拉黑成功');
    }

    /**
     * 解除黑名单操作
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function relieveBlock() {
        $uid = input('get.uid/d', 1, 'intval');
        $member_balance = model('MemberBalance')->where('uid', $uid)->find();
        if (empty($member_balance)) {
            $res = model('MemberBalance')->save([
                'uid' => $uid,
                'money' => 0,
                'is_blacklist' => 0
            ]);
        } else {
            $res = model('MemberBalance')->save(['is_blacklist' => 0], ['uid' => $uid]);
        }
        if ($res === false) {
            $this->ajaxReturn(500, '解除失败');
        }
        model('AdminLog')->record(
            '将用户ID为【' . $uid . '】,解除拉黑操作',
            $this->admininfo
        );
        $this->ajaxReturn(200, '解除成功');
    }
    /** 用户申请提现记录 */
    public function withdrawalApplication() {
        $where = [];
        $id = input('get.id/d', 0, 'intval');
        $basics = [];
        if (isset($id) && $id != 0) {
            $where['a.uid'] = $id;
            $member = model('Member')->where('uid', $id)->find();
            $member_bind = model('MemberBind')->where('uid', $id)->find();
            if (empty($member_bind)) {
                $basics['is_weixin'] = 0;
            } else {
                $basics['is_weixin'] = 1;
            }
            if ($member['utype'] == 1) {

                $company = model('company')->where('uid', $id)->find();
                if ($company['audit'] == 1) {
                    $audit = '已通过企业认证';
                } else {
                    $audit = '未通过企业认证';
                }
                $basics['basics'] = $company['companyname'] . '/' . $audit;
                $basics['link'] = url('index/company/show', ['id' => $company['id']]);
            }
            if ($member['utype'] == 2) {
                $resume = model('Resume')->where('uid', $id)->find();
                $basics['link'] = url('index/resume/show', ['id' => $resume['id']]);
                $basics['basics'] = $resume['fullname'] . '/' . model('Resume')->map_sex[$resume['sex']] . '/' . (date('Y') - intval($resume['birthday'])) . '岁 简历完整度' . model('Resume')->countCompletePercent(0, $id) . '%';
            }
            $basics['utype'] = $member['utype'];
            $basics['mobile'] = $member['mobile'];
            $basics['reg_time'] = $member['reg_time'];
        }
        $key_type = input('get.key_type/d', 0, 'intval');
        $keyword = input('get.keyword/s', '', 'trim');
        $status = input('get.status/d', 0, 'intval');
        $utype = input('get.utype/d', 0, 'intval');
        $is_openid = input('get.is_openid/d', 0, 'intval');
        $current_page = input('get.page/d', 1, 'intval');
        $pagesize = input('get.pagesize/d', 10, 'intval');
        if ($keyword && $key_type) {
            switch ($key_type) {
                case 1:
                    $where['a.uid'] = ['eq', $keyword];
                    break;
                case 2:
                    $where['a.username'] = ['like', '%' . $keyword . '%'];
                    break;
                case 3:
                    $where['a.mobile'] = ['like', '%' . $keyword . '%'];
                    break;
                case 4:
                    $where['b.companyname'] = ['like', '%' . $keyword . '%'];
                    break;
                case 5:
                    $where['c.fullname'] = ['like', '%' . $keyword . '%'];
                    break;
                default:
                    break;
            }
        }
        if ($status === 1) {
            $where['r.state'] = ['eq', 0];
        } elseif ($status === 2) {
            $where['r.state'] = ['eq', 1];
        } elseif ($status === 3) {
            $where['r.state'] = ['eq', 2];
        }
        if ($utype > 0) {
            $where['a.utype'] = $utype;
        }
        $wheres = '';
        if ($is_openid === 1) {
            $wheres .= 'd.id is not null';
        } elseif ($is_openid === 2) {
            $wheres .= 'd.id is null';
        }
        $total = model('MemberWithdrawalRecord')->alias('r');
        if ($key_type == 4) {
            $total = $total->join(config('database.prefix') . 'company b', 'r.uid=b.uid', 'LEFT')->where('a.utype', 1)->where('b.companyname', 'neq', '');
        }
        if ($key_type == 5) {
            $total = $total->join(config('database.prefix') . 'resume c', 'r.uid=c.uid', 'LEFT')->where('a.utype', 2)->where('c.fullname', 'NOT NULL');
        }
        $total = $total->join(config('database.prefix') . 'member a', 'r.uid=a.uid', 'left')
            ->join(config('database.prefix') . 'member_bind d', 'd.uid=r.uid and d.type="weixin"', 'left')
            ->where($where)
            ->where($wheres)
            ->count();
        $list = model('MemberWithdrawalRecord')->alias('r');
        if ($key_type == 4) {
            $list = $list->join(config('database.prefix') . 'company b', 'r.uid=b.uid', 'LEFT')->where('a.utype', 1)->where('b.companyname', 'neq', '');
        }
        if ($key_type == 5) {
            $list = $list->join(config('database.prefix') . 'resume c', 'r.uid=c.uid', 'LEFT')->where('a.utype', 2)->where('c.fullname', 'NOT NULL');
        }
        $list = $list->join(config('database.prefix') . 'member a', 'r.uid=a.uid', 'left')
            ->field('r.id,a.uid,r.price,r.addtime,r.state,r.reason,r.examinetime')
            ->join(config('database.prefix') . 'member_bind d', 'd.uid=r.uid and d.type="weixin"', 'left')
            ->where($where)
            ->where($wheres)
            ->order('r.id desc')
            ->page($current_page . ',' . $pagesize)
            ->select();
        foreach ($list as $k => $v) {
            $member = model('Member')->where('uid', $v['uid'])->find();
            if ($member['utype'] == 1) {
                $list[$k]['utype_msg'] = '企业会员';
                $company = model('Company')->where('uid', $v['uid'])->field('companyname')->find();
                $list[$k]['username'] = $company['companyname'];
            }
            if ($member['utype'] == 2) {
                $list[$k]['utype_msg'] = '个人会员';
                $company = model('Resume')->where('uid', $v['uid'])->field('fullname')->find();
                $list[$k]['username'] = $company['fullname'];
            }
            $member_bind = model('MemberBind')->where('uid', $v['uid'])->find();
            if (empty($member_bind)) {
                $list[$k]['bind_msg'] = '未绑定';
            } else {
                $list[$k]['bind_msg'] = '已绑定';
            }
        }
        $return['basics'] = $basics;
        $return['items'] = $list;
        $return['total'] = $total;
        $return['current_page'] = $current_page;
        $return['pagesize'] = $pagesize;
        $return['total_page'] = ceil($total / $pagesize);
        $this->ajaxReturn(200, '获取数据成功', $return);
    }

    /** 审核 */
    public function examine() {
        $id = input('post.id/d', 0, 'intval');
        $audit = input('post.audit/d', '', 'intval');
        $reason = input('post.reason/s', '', 'trim');
        if ($audit === '') {
            $this->ajaxReturn(500, '请选择审核状态');
        }
        $wechat_withdrawal_record = model('MemberWithdrawalRecord')->where('id', $id)->find();
        if (empty($wechat_withdrawal_record)) {
            $this->ajaxReturn(500, '未查询到提现记录');
        }
        if ($wechat_withdrawal_record['state'] != 2) {
            $this->ajaxReturn(500, '提现申请已审核');
        }
        if ($audit == 1) {
            $re_state = model('MemberWithdrawalRecord')->where('id', $id)->update(['state' => 0, 'reason' => $reason, 'examinetime' => time()]);
            $res_state = model('MemberBalance')->where('uid', $wechat_withdrawal_record['uid'])->setInc('money', $wechat_withdrawal_record['price']);
            if ($re_state === false || $res_state === false) {
                $this->ajaxReturn(200, '审核失败');
            }
            model('AdminLog')->record(
                '审核红包提现。ID【' .
                    $id .
                    '】',
                $this->admininfo
            );
            $this->ajaxReturn(200, '审核成功');
        } else {
            $member_bind = model('MemberBind')->where('uid', $wechat_withdrawal_record['uid'])->find();
            if (empty($member_bind)) {
                $this->ajaxReturn(500, '用户未绑定微信');
            }

            $order_id = config('global_config.payment_wechat_mchid') . date('YmdHis') . rand(1000, 9999);
            $re = model('MemberBalance')->send(
                $wechat_withdrawal_record['price'],
                $member_bind['openid'],
                '钱包提现',
                '钱包提现',
                $order_id
            );
            if ($re['result_code'] == 'SUCCESS') {
                $res =  model('MemberWithdrawalRecord')->where('id', $id)->update(['state' => 1, 'examinetime' => time()]);
                if ($res === false) {
                    $this->ajaxReturn(500, '审核失败');
                }
                model('AdminLog')->record(
                    '审核红包提现。ID【' .
                        $id .
                        '】',
                    $this->admininfo
                );
                $this->ajaxReturn(200, $re['return_msg']);
            } else {
                $this->ajaxReturn(500, $re['return_msg']);
            }
        }
    }

    /**
     * 邀请注册列表
     * @return void
     */
    public function inviteRegister() {
        $where = [];
        $current_page = input('get.page/d', 1, 'intval');
        $pagesize = input('get.pagesize/d', 10, 'intval');
        $total = model('MemberInviteRegister')->alias('a');
        $total = $total->count();
        $list = model('MemberInviteRegister')->alias('a');
        $list = $list->page($current_page . ',' . $pagesize)
            ->order('id desc')
            ->select();
        foreach ($list as $k => $v) {
            $pid_member =  model('member')->where('uid', $v['pid'])->find();
            $member =  model('member')->where('uid', $v['uid'])->find();
            if ($pid_member['utype'] == 1) {
                $company = model('Company')->where('uid', $pid_member['uid'])->find();
                $list[$k]['pid_member_msg'] = $company['companyname'] . '(企业)';
            }
            if ($pid_member['utype'] == 2) {
                $resume = model('Resume')->where('uid', $pid_member['uid'])->find();
                $list[$k]['pid_member_msg'] = $resume['fullname'] . '(个人)';
            }
            if ($member['utype'] == 1) {
                $company = model('Company')->where('uid', $member['uid'])->find();
                if ($company['audit'] == 1) {
                    $list[$k]['complete_percent'] = '已通过营业执照认证';
                } else {
                    $list[$k]['complete_percent'] = '未通过营业执照认证';
                }
                $list[$k]['member_msg'] = $company['companyname'] . '(企业)';
            }
            if ($member['utype'] == 2) {
                $list[$k]['complete_percent'] = '简历完整度' . model('Resume')->countCompletePercent(0, $member['uid']) . '%';
                $resume = model('Resume')->where('uid', $member['uid'])->find();
                $list[$k]['member_msg'] = $resume['fullname'] . '(个人)';
            }
            $list[$k]['reg_time'] = $member['reg_time'];
        }
        $return['items'] = $list;
        $return['total'] = $total;
        $return['current_page'] = $current_page;
        $return['pagesize'] = $pagesize;
        $return['total_page'] = ceil($total / $pagesize);
        $this->ajaxReturn(200, '获取数据成功', $return);
    }

    /**
     * 邀请注册审核
     * @return void
     */
    public function inviteRegisterExamine() {
        $id = input('get.id/d', 0, 'intval');
        $audit = input('get.audit/d', 0, 'intval');
        $reason = input('get.reason/s', 0, 'trim');
        $member_invite_register = model('MemberInviteRegister')->where('id', $id)->find();
        if ($member_invite_register['examine_state'] == 0) {
            if (empty($member_invite_register)) {
                $this->ajaxReturn(500, '暂未查询到审核记录');
            }
            $member_balance = model('MemberBalance')->where('uid', $member_invite_register['pid'])->find();
            if ($member_balance['is_blacklist'] == 1) {
                $this->ajaxReturn(500, '邀请者是黑名单会员');
            }
            if ($audit == 1) {
                $res = model('MemberInviteRegister')
                    ->save(['state' => 2, 'examinetime' => time(), 'examine_state' => 2, 'reason' => $reason], ['id' => $id]);
                if ($res === false) {
                    $this->ajaxReturn(500, '审核失败');
                }
            }
            if ($audit == 0) {
                $res = model('MemberInviteRegister')
                    ->save(['state' => 1, 'examinetime' => time(), 'examine_state' => 1], ['id' => $id]);
                if ($res === false) {
                    $this->ajaxReturn(500, '审核失败');
                }
                model('MemberBalance')->moneyRecord(
                    $member_invite_register['pid'],
                    'invite_register',
                    '邀请红包',
                    0,
                    1,
                    0
                );
            }
            model('AdminLog')->record(
                '红包，邀请注册审核。ID【' .
                    $id .
                    '】',
                $this->admininfo
            );
            $this->ajaxReturn(200, '审核成功');
        }
        $this->ajaxReturn(200, '审核成功');
    }
    /**
     * 删除邀请记录
     * @return void
     */
    public function inviteRegisterDel() {
        $id = input('get.id/d', 0, 'intval');
        $res = model('MemberInviteRegister')->where('id', $id)->delete();
        if ($res === false) {
            $this->ajaxReturn(500, '删除失败');
        }
        model('AdminLog')->record(
            '邀请注册删除。ID【' .
                $id .
                '】',
            $this->admininfo
        );
        $this->ajaxReturn(200, '删除成功');
    }
}
