<?php

namespace app\common\model;

class MemberBalance  extends \app\common\model\BaseModel {
    public function getMemberBalance($uid) {
        $info = model('MemberBalance')
            ->where('uid', $uid)
            ->find();
        if ($info === null) {
            $return = 0;
        } else {
            $return = $info->money;
        }
        return $return;
    }

    /**
     * 余额添加和添加审核记录
     * @return void
     */
    public function moneyRecord($uid, $config_name, $content, $score = 0, $is_invitation = 0, $openid = '') {
        $config = config('global_config.' . $config_name);
        if ($config_name == 'binding_red_envelopes' || $config_name == 'follow_red_envelopes' || $config_name == 'improve_resume_red_envelopes') {
            $member_balance_log = model('MemberBalanceLog')
                ->where('type', $config_name)
                ->where('uid', $uid)
                ->find();
            if (!empty($member_balance_log)) {
                return false;
            }
            if ($openid != '') {
                $member_balance_log_openid = model('MemberBalanceLog')
                    ->where('type', $config_name)
                    ->where('openid', $openid)
                    ->find();
                if (!empty($member_balance_log_openid)) {
                    return false;
                }
            }
        }
        if ($config['is_open'] == '0') {
            return false;
        }
        if ($score > 0) {
            if ($score < $config['Integrity']) {
                return false;
            }
        }
        if ($is_invitation == 1) {
            $money = $config['price'];
        } else {
            $money = rand($config['min_price'] * 100, $config['max_price'] * 100) / 100;
        }
        $info = model('MemberBalance')
            ->where('uid', $uid)
            ->find();
        if (empty($info)) {
            model('MemberBalance')->save([
                'uid' => $uid,
                'money' => $money,
                'is_blacklist' => 0,
                'token' => uuid()
            ]);
        } else {
            model('MemberBalance')->where('uid', $uid)->setInc('money', $money);
        }
        $res = model('MemberBalanceLog')->save([
            'uid' => $uid,
            'type' => $config_name,
            'money' => $money,
            'op' => 1,
            'content' => $content,
            'addtime' => time(),
            'openid' => $openid
        ]);
        if ($res === false) {
            return false;
        }
        return true;
    }

    /**
     * 发送红包接口
     * @param $money 金额
     * @param $openid 接收者ID
     * @param $wishing 欢迎语
     * @param $nick_name 活动名称
     * @return mixed
     */
    public function send($money, $openid, $wishing, $nick_name, $order_id) {
        $wechatRed = new \app\common\lib\WechatRed();
        $data = array();
        $data['wxappid'] = config('global_config.wechat_appid'); //公众号appid
        $data['mch_id'] = config('global_config.payment_wechat_mchid'); //商户号
        $data['mch_billno'] = $order_id; //商户订单号 28位
        $data['client_ip'] = get_client_ip();; //本机ip
        $data['re_openid'] = $openid; //接受人
        $data['total_amount'] = $money * 100; //收红包的用户的金额，精确到分
        $data['min_value'] = $money * 100; //最小金额
        $data['max_value'] = $money * 100; //最大金额
        $data['total_num'] = 1; //发送数量
        $data['nick_name'] = config('global_config.sitename'); //红包商户名称
        $data['send_name'] = config('global_config.sitename'); //红包派发者名称
        $data['wishing'] = $wishing; //欢迎语
        $data['act_name'] = $nick_name; //活动名称
        $data['remark'] = $wishing; //备注
        $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack"; //发红包api
        $res = $wechatRed->pay($url, $data);
        libxml_disable_entity_loader(true);
        $postObj = simplexml_load_string($res, 'SimpleXMLElement', LIBXML_NOCDATA);
        $val = json_decode(json_encode($postObj), true);
        return $val;
    }
}
