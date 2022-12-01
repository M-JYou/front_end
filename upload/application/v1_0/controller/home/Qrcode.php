<?php

namespace app\v1_0\controller\home;

class Qrcode extends \app\v1_0\controller\common\Base {
  public function _initialize() {
    parent::_initialize();
  }
  public function index() {
    $type = input('get.type/s', '', 'trim');
    if ($type == '') {
      $type = config('global_config.qrcode_type');
    }

    switch ($type) {
      case 'normal':
        $this->makeNormalQrcode();
        break;
      case 'miniprogram':
        $this->makeMiniprogramQrcode();
        break;
      case 'wechat':
        $this->makeWechatQrcode();
        break;
      case 'bind_weixin':
        $this->makeBindWechatQrcode();
        break;
      
      default:
        $this->makeNormalQrcode();
        break;
    }

  }
  /** 生成普通二维码（跳转到指定链接） */
  protected function makeNormalQrcode() {
    if ($url = input('get.url/s', '', 'trim')) {
      ob_clean();
      $url = htmlspecialchars_decode($url, ENT_QUOTES);
      vendor('phpqrcode.phpqrcode');
      $qrcode = new \QRcode(new \QRinput());
      ob_clean();
      $qrcode::png($url, false, 'H', 8, 2);
      exit;
    }
  }
  /** 生成微信小程序二维码 */
  protected function makeMiniprogramQrcode() {
    $type = input('get.alias/s', '', 'trim');
    $page = '';
    $id = 0;
    if ($type == 'subscribe_job') {
      $page = 'pages/jobs/jobs_show/jobs_show';
      $id = input('get.jobid/d', 0, 'intval');
    } else if ($type == 'subscribe_company') {
      $page = 'pages/jobs/company_show/company_show';
      $id = input('get.comid/d', 0, 'intval');
    } else if ($type == 'subscribe_resume') {
      $page = 'pages/resume/show/show';
      $id = input('get.resumeid/d', 0, 'intval');
    } else if ($type == 'recruitment_today') {  //今日招聘
      $page = 'pages/resume/show/show';
      $id = input('get.recruitment_today_id/d', 0, 'intval');
    } else {
      $this->makeNormalQrcode();
      exit;
    }
    if ($page && $id) {
      $class = new \app\common\lib\WechatMiniprogram;
      $qrcode = $class->makeQrcode($page, ['id' => $id]);
      if ($qrcode) {
        $this->showImg($qrcode);
      }
    }
  }
  /** 生成微信带参数二维码 */
  protected function makeWechatQrcode() {
    if ($alias = input('get.alias/s', 'mapQrcode', 'trim')) {
      $params = input('get.');
      $class = new \app\common\lib\Wechat;
      $qrcode = $class->makeQrcode($params);
      if ($qrcode) {
        if (input('get.getsrc/d', 0, 'intval')) {
          $this->ajaxReturn(200, '', $qrcode);
        }
        $this->showImg($qrcode);
      } else {
        $this->ajaxReturn(501, 'server error');
      }
    }
  }
  /** 生成微信绑定二维码 */
  protected function makeBindWechatQrcode() {
    $alias = 'member_bind_weixin';
    $params = [
      'alias' => $alias,
      'uid' => $this->userinfo->uid,
      'utype' => $this->userinfo->utype
    ];
    $class = new \app\common\lib\Wechat;
    $qrcode = $class->makeQrcode($params);
    if ($qrcode) {
      $this->ajaxReturn(200, '', $qrcode);
    } else {
      $this->ajaxReturn(501, 'server error');
    }
  }
  protected function showImg($img) {
    $size = getimagesize($img);
    $fp = fopen($img, "rb");
    if ($size && $fp) {
      header("Content-type: {$size['mime']}");
      fpassthru($fp);
      exit;
    }
  }
}
