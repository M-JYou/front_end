<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/5/9
 * Time: 11:06
 */

namespace app\common\model;


use app\common\lib\Pay;

class CityinfoOrder extends BaseModel {
    protected $autoWriteTimestamp = true;
    protected $createTime = 'addtime';
    protected $updateTime = 'updatetime';
    protected $readonly = ['id', 'uid'];
    protected $error = null;
    const STATUS_PAID = 1;
    const STATUS_INIT = 0;
    const STATUS_CLOSE = 2;
    const PREFIX_ORDER = 'ci';

    const TYPE_PUBLISH_ARTICLE = 1;
    const TYPE_VIEW_ARTICLE = 2;
    const TYPE_PROMOTE = 3;
    const TYPE_PUBLISH_PROMOTE = 4;
    const TYPE_REFRESH_ARTICLE = 5;

    public function getError() {
        return $this->error;
    }

    public function newOrder($uid, $mobile, $type, $fee, $item_id, $title, $desc, $param1 = 0, $param2 = 0) {
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
            'param1' => $param1,
            'param2' => $param2
        ];
        $r = $this->save($data);
        if ($r) {
            return $data;
        } else {
            return false;
        }
    }

    /**
     * @param $curOrder 当前订单
     * @param $payType 支付类型
     * @param $amount 金额 (元)
     * @param $thirdId 第三方订单号
     * @return bool
     */
    public function paid($curOrder, $payType, $amount, $thirdId) {
        $aModel = new CityinfoArticle();
        $tModel = new CityinfoType();
        $amount = intval($amount * 100);
        if (!config('pay_test_mode') && $curOrder['amount'] != $amount) {
            return false;
        }
        switch (intval($curOrder['type'])) {
            case self::TYPE_PUBLISH_ARTICLE:
                $param1 = $curOrder['param1'];
                $aInfo = $aModel->find($curOrder['item_id']);
                $pInfo = $tModel->getPInfoByTypeId($aInfo['type_id']);
                $days = $pInfo['pay_for_create'][$param1]['days'];
                $aModel->setEndtime($aInfo, $days);
                break;
            case self::TYPE_VIEW_ARTICLE:
                break;
            case self::TYPE_PROMOTE:
                $param2 = $curOrder['param2'];
                $set = config('global_config.cityinfo_promote_set');
                $days = $set[$param2]['days'];
                $aModel->promo($curOrder['item_id'], $days);
                break;
            case self::TYPE_PUBLISH_PROMOTE:
                $param1 = $curOrder['param1'];
                $aInfo = $aModel->find($curOrder['item_id']);
                $pInfo = $tModel->getPInfoByTypeId($aInfo['type_id']);
                $days = $pInfo['pay_for_create'][$param1]['days'];
                $aModel->setEndtime($aInfo, $days);
                $param2 = $curOrder['param2'];
                $set = config('global_config.cityinfo_promote_set');
                $days = $set[$param2]['days'];
                $aModel->promo($curOrder['item_id'], $days);
                break;
            case self::TYPE_REFRESH_ARTICLE: //刷新项目
                $aModel->refresh($curOrder['item_id']);
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

    public function hasPaidView($uid, $itemId, $type) {
        return $this->where(['uid' => $uid, 'item_id' => $itemId, 'type' => $type, 'status' => self::STATUS_PAID])->count();
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
}
