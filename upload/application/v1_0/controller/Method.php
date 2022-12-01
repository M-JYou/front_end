<?php

namespace app\v1_0\controller;

use app\common\lib\Http;
use app\common\model\CategoryDistrict;

function getLast($str = '', $simple = false) {
  $ret = '';
  if (!empty($str)) {
    try {
      $ret = explode(" ", $str)[1];
      if ($simple) {
        $ret = '/' . floatval($ret) . '-';
      }
    } catch (\Throwable $th) {
    }
  }
  return $ret;
}
function getMd($str = '') {
  $d = explode('-', $str);
  return '/' . intval($d[1]) . '月' . intval($d[2]) . '日/';
}
class Method extends \app\v1_0\controller\common\Base {
  /** 根据model生成二维码 */
  public function url() {
    $model = input('get.model/s', '', 'trim');
    $mid = input('get.mid/d', 0, 'intval');
    return config("global_config.sitedomain") . "/$model/$mid.html";
  }
  public function qr() {
    ob_clean();
    $url = htmlspecialchars_decode($this->url(), ENT_QUOTES);
    vendor('phpqrcode.phpqrcode');
    $qrcode = new \QRcode(new \QRinput());
    ob_clean();
    $qrcode::png($url, false, 'H', 8, 2);
    exit;
  }
  public function clearCache() {
    clearCache();
    ext(200, '刷新缓存成功', null);
  }
  public function geturl($url) {
    $http = new Http();
    return $http->get($url);
  }
  public function getDistrict($pid = 0) {
    return json((new CategoryDistrict())->getByPid($pid));
  }
  public function website() {
    $this->ajaxReturn(200, '获取成功', model('Link')->getCache(input('param.type/d', 0, 'intval')));
  }
  public function article() {
    $param = input('param.');
    $this->__find($param, model('Article'));
  }
  public function bank() {
    $param = input('param.');
    $this->_find($param, '*,id as `article`', null, null, model('Bank'));
  }
  public function link() {
    $param = input('param.');
    $this->__find($param, model('Link'), 'type asc,sort_id desc,id asc');
  }
  public function linkType() {
    $param = input('param.');
    $this->__find($param, model('LinkType'));
  }
  public function linkGroup() {
    $d = clo(model('LinkType')
      ->where('id', 'in', [1, 2, 3, 4, 5, 6, 7, 13])
      ->field('id,name,id `children`')->order('id asc')->select());
    // 处理 办事官网
    $t = [];
    $index = findArr($d, 2, 'id', true);
    if ($index >= 0) {
      foreach ($d[$index]['children'] as $v) {
        if (!isset($t[$v['notes']])) {
          $t[$v['notes']] = [];
        }
        $t[$v['notes']][] = $v;
      }
      $d[$index]['children'] = [];
      foreach ($t as $k => $v) {
        $d[$index]['children'][] = ['name' => $k, 'children' => $v];
      }
    }
    // 处理 应用中心
    $t = [];
    $index = findArr($d, 4, 'id', true);
    if ($index >= 0) {
      foreach ($d[$index]['children'] as $k => $v) {
        foreach ($v['content'] as $kk => $kv) {
          $v['content'][$kk]['link'] = make_file_url($kv['link'], 1);
        }
        $d[$index]['children'][$k]['children'] = $v['content'];
        unset($d[$index]['children'][$k]['content']);
      }
    }
    // 处理 财务便捷窗口
    $t = [];
    $index = findArr($d, 13, 'id', true);
    if ($index >= 0) {
      $d[$index]['name'] = substr($d[$index]['name'], 7);
      foreach ($d[$index]['children'] as $k => $v) {
        foreach ($v['content'] as $kk => $kv) {
          $v['content'][$kk]['link'] = make_file_url($kv['link'], 1);
        }
        $d[$index]['children'][$k]['children'] = $v['content'];
        unset($d[$index]['children'][$k]['content']);
      }
    }
    ext($d);
  }
  public function ad() {
    $p = input('param.');
    $p['is_display'] = 1;
    $this->_find($p, '*,`type` `type_`', 'sort desc', null, model('Ads'));
  }
  public function adType() {
    $p = input('param.');
    $this->_find($p, '*', null, null, model('AdsType'));
  }
  public function adGroup() {
    $p = input('param.');
    $this->_find($p, '*,`id` `children`', null, null, model('AdsType'));
  }

  public function CategoryJob() {
    $param = input('param.');
    $model = model('CategoryJob');
    if (isset($param['tree']) && $param['tree']) {
      $this->_tree($model);
    } else {
      $this->__find($param, $model);
    }
  }

  /** 获取充值套餐列表 */
  public function pointsList() {
    $this->checkLogin();
    ext(200, '获取充值套餐列表成功', model('CompanyServicePoints')
      ->field('is_display', true)
      ->where('is_display', 1)
      ->order('sort_id desc')
      ->select());
  }
  /** 充值点券 */
  public function pay() {
    $payment = input('param.payment/s', 'wxpay', 'trim');
    $id = input('param.id/d', 0, 'intval');
    if (!$id) {
      $id = input('param.service_id/d', 0, 'intval');
    }
    $result = model('Order')->addPointsOrder([
      'uid' => $this->userinfo->uid,
      'service_id' => $id,
      'service_type' => 'points',
      'payment' => $payment == 'wxpay' ? $payment : 'alipay',
      'code' => input('param.code/s', '', 'trim'),
      'openid' => input('param.openid/s', '', 'trim'),
      'return_url' => input('param.return_url/s', '', 'trim'),
      'platform' => config('platform'),
    ]);
    if (false === $result) {
      $this->ajaxReturn(500, model('Order')->getError());
    }
    $order = model('Order')->where('oid', $result['order_oid'])->find();
    model('AdminNotice')->send(
      6,
      '企业订单通知',
      '订单号:【' . $result['order_oid'] . '】' . "\r\n" .
        '用户名称:【' . model('MemberInfo')->where('id', $this->userinfo->uid)->field('name')->find()['name'] . '】' . "\r\n" .
        '服务内容:【充值' . config('global_config.points_byname') . '】' . "\r\n" .
        '订单金额:【' . $order['amount'] . '】' . "\r\n" .
        '订单状态:【' . model('Order')->map_status[$order['status']] . '】',
      '订单通知，请及时跟进（后台->业务->企业业务管理->订单管理）'
    );
    $this->writeMemberActionLog($this->userinfo->uid, '下订单【订单号：' . $result['order_oid'] . '】');

    $this->ajaxReturn(200, '下单成功', $result);
  }
  /** 获取充值记录 */
  public function payOrder() {
    $payment = input('param.payment/s', 'wxpay', 'trim');
    ext(model('Order')->where([
      'uid' => $this->userinfo->uid,
      'service_type' => 'points',
      'payment' => $payment == 'wxpay' ? $payment : 'alipay',
    ])->select());
  }
  public function pointsLog() {
    $d = input('post.');
    $d['uid'] = $this->userinfo->uid;
    $m = model('memberPointsLog');
    $total = $m->getModel($d)->count();
    $page = input('post.page/d', 1, 'intval');
    $pagesize = input('post.pagesize/d', 10, 'intval');
    ext([
      'item' => $m->getModel($d)
        ->field('if(op=1,"增加","减少") ops, `points` `sum`, `content`, `addtime`')
        ->order('addtime desc')
        ->select(),
      'total' => $total,
      'current_page' => $page,
      'pagesize' => $pagesize,
      'total_page' => ceil($total / $pagesize),
    ]);
  }

  public function getData() {
    $t = [
      'Reprint', 'Favorites', 'Comment', 'Report', 'Likes', 'Blacklist',
      'Buy', 'Visitor', 'History', 'Sign'
    ];
    $param = input('param.');
    if (isset($param['#name']) && in_array($param['#name'], $t, true)) {
      $t = $param['#name'];
      unset($param['#name']);
      $param['create'] = $this->userinfo->uid;
      $field = '*';
      if (isset($param['#modelData'])) {
        $field .= ',mid modelData';
      }
      $this->_find($param, $field, null, null, model($t));
    }
    ext(500, '#name值错误', $t);
  }

  public function model() {
    ext([
      ['model' => 'Company', 'name' => '企业'],
      ['model' => 'MemberInfo', 'name' => '用户'],
      ['model' => 'Article', 'name' => '文章'],
      ['model' => 'Qa', 'name' => '问答'],
      ['model' => 'Goods', 'name' => '商品'],
      ['model' => 'Exam', 'name' => '题库'],
      ['model' => 'Xtime', 'name' => '虾时光'],
      ['model' => 'Game', 'name' => '财税游戏'],
      ['model' => 'Study', 'name' => '学习'],
      ['model' => 'Expert', 'name' => '专家'],
      ['model' => 'CompanyTransfer', 'name' => '公司转让'],
      ['model' => 'CompanyBuy', 'name' => '公司求购'],
      ['model' => 'TrademarkTransfer', 'name' => '商标转让'],
      ['model' => 'TrademarkBuy', 'name' => '商标求购'],
      ['model' => 'Shop', 'name' => '商店'],
      ['model' => 'Files', 'name' => '文档'],
    ]);
  }
  public function tst() {
    $r = request()->header('origin');
    ext($r);
  }

  public function getWeather() {
    $id = input('param.id/d', 0, 'intval');
    if (!$id) {
      $id = getCityId();
    }
    ext(model('CategoryDistrict')->getWeather($id));
  }
  public function imgs() {
    $this->_find(input('param.'), '*', 'sort desc, id desc', null, model('Imgs'));
  }

  public function purl() {
    outp(input('param.'));
    return input('param.');
  }
}
