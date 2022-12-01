<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/4/6
 * Time: 11:41
 */

namespace app\common\model;


use app\common\lib\Pay;
use Think\Db;
use Think\Model;

class FreelanceOrder extends Model {
    protected $autoWriteTimestamp = true;
    protected $createTime = 'addtime';
    protected $updateTime = 'updatetime';
    protected $readonly = ['id', 'uid'];
    protected $error = null;
    const STATUS_PAID = 1;
    const STATUS_INIT = 0;
    const STATUS_CLOSE = 2;
    const PREFIX_ORDER = 'fl';

    const TYPE_PUBLISH_RESUME = 1;
    const TYPE_PUBLISH_SUBJECT = 2;
    const TYPE_VIEW_RESUME = 3;
    const TYPE_VIEW_SUBJECT = 4;
    const TYPE_REFRESH_SUBJECT = 5;
    const TYPE_SERVICE = 6;

    public function getError() {
        return $this->error;
    }

    public function newOrder($uid, $mobile, $type, $fee, $item_id, $title, $desc, $param = 0) {
        list($msec, $sec) = explode(' ', microtime());
        $data = [
            'order_sn' => sprintf('%s%s%s%s%s', self::PREFIX_ORDER, chr(rand(97, 122)), date('YmdHis'), intval($msec * 10), rand(10, 99)),
            'uid' => $uid,
            'mobile' => $mobile,
            'type' => $type,
            'amount' => $fee, //单位 分
            'item_id' => $item_id,
            'status' => self::STATUS_INIT,
            'title' => $title,
            'desc' => $desc,
            'param' => $param
        ];
        $r = $this->save($data);
        if ($r) {
            return $data;
        } else {
            return false;
        }
    }

    public function getList($status, $type, $key, $pay_type, $order_type, $addtime, $paytime, $page, $pagesize) {
        $where = [];
        if ($key) {
            if ($type == 1) {
                $where['uid'] = trim($key);
            } else if ($type == 2) {
                $where['mobile'] = trim($key);
            }
        }
        if ($pay_type) {
            $where['pay_type'] = $pay_type;
        }
        if ($order_type) {
            $where['type'] = $order_type;
        }
        if ($status > -1) {
            $where['status'] = $status;
        }
        if ($addtime) {
            $where['addtime'] = ['egt', time() - 86400 * $addtime];
        }
        if ($paytime) {
            $where['paytime'] = ['egt', time() - 86400 * $paytime];
        }
        return [
            'list' => $this->where($where)->order('paytime desc')->limit(($page - 1) * $pagesize, $pagesize)->select(),
            'total' => $this->where($where)->count()
        ];
    }

    public function callPay($order, $param) {
        $pay = new Pay(config('platform'), $param['pay_type']);
        $rst = $pay->callPay([
            'oid' => $order['order_sn'],
            'service_name' => $order['desc'],
            'amount' => $order['amount'] / 100,
            'redirect_url' => $param['redirect_url'],
            'return_url' => $param['redirect_url'],
            'openid' => $param['openid']
        ]);
        if ($rst === false) {
            $this->error = $pay->getError();
            return false;
        }
        return $rst;
    }

    /**
     * @param $curOrder 当前订单
     * @param $payType 支付类型
     * @param $amount 金额 (元)
     * @param $thirdId 第三方订单号
     * @return bool
     */
    public function paid($curOrder, $payType, $amount, $thirdId) {
        $ps = new FreelanceSubject();
        $amount = intval($amount * 100);
        if (!config('pay_test_mode') && $curOrder['amount'] != $amount) {
            return false;
        }
        switch (intval($curOrder['type'])) {
            case self::TYPE_PUBLISH_RESUME:
                (new FreelanceResume())->save(['is_published' => 1], ['id' => $curOrder['item_id']]);
                $old = (new FreelanceResume())->find($curOrder['item_id']);
                $old['is_published'] = 1;
                (new FreelanceSearchResume())->updatesearch($old);
                break;
            case self::TYPE_PUBLISH_SUBJECT:
                $old = (new FreelanceResume())->find($curOrder['item_id']);
                $ps->save(['is_published' => 1], ['id' => $curOrder['item_id']]);
                $old['is_published'] = 1;
                (new FreelanceSearchSubject())->updatesearch($old);
                break;
            case self::TYPE_VIEW_RESUME:
                break;
            case self::TYPE_VIEW_SUBJECT:
                break;
            case self::TYPE_SERVICE:
                $this->processService($curOrder);
                break;
            case self::TYPE_REFRESH_SUBJECT: //刷新项目
                (new FreelanceSubject())->refresh($curOrder['item_id']);
                break;
        }
        $r = $this->save([
            'status' => self::STATUS_PAID,
            'pay_type' => $payType,
            'third_order_id' => $thirdId,
            'paytime' => time()
        ], ['id' => $curOrder['id']]);
        if ($r) {
            if ($payType == 'wxpay') {
                cache('wxpay_' . $curOrder['order_sn'], 'ok', 60);
            }
        }
        return !!$r;
    }

    public function hasPaid($uid, $itemId, $type) {
        return $this->where(['uid' => $uid, 'item_id' => $itemId, 'type' => $type, 'status' => self::STATUS_PAID])->count();
    }

    public function processService($order) {
        $subjectId = $order['item_id'];
        $refreshDays = $order['param'];
        $subject = new FreelanceSubject();
        $subject->save(['is_top' => 1], ['id' => $subjectId]);
        (new ServiceQueue())->save([
            'utype' => 1,
            'pid' => $subjectId,
            'type' => 'pt-stick-subject',
            'addtime' => time(),
            'deadline' => time() + $refreshDays * 86400
        ]);
    }
}
