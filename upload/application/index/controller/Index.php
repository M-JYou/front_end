<?php

namespace app\index\controller;

use app\common\lib\tpl\index\def;

class Index extends \app\index\controller\Base {
  public function _initialize() {
    parent::_initialize();
  }
  public function index() {
    // if (is_mobile_request() === true) {
    //   $this->redirect(config('global_config.mobile_domain'), 302);
    //   exit;
    // }
    if ($this->subsite !== null) {
      $index_tpl = $this->subsite->tpl;
    } else {
      $index_tpl = config('global_config.index_tpl');
    }
    $index_tpl = $index_tpl ? $index_tpl : 'def';
    $instance = new \app\common\lib\Tpl($this->visitor);
    $return = $instance->index($index_tpl);

    $return['pageHeader'] = $this->pageHeader;
    $return['navSelTag'] = 'index';

    foreach ($return as $key => $value) {
      $this->assign($key, $value);
    }
    $this->initPageSeo('index');


    // $this->assign('district', $dcd->getByPid());
    return $this->fetch('index/' . $index_tpl . '/index');

    return json_encode($return);
  }
  public function json() {
    ext((new \app\common\lib\Tpl($this->visitor))->index('def'));
  }
  public function search() {
    $k = safeSql(input('param.keyword/s', '', 'trim'));

    ext(model('Model')->where('id', '>', 2)->field("*,$k `search`")
    // ->fetchSql()
    ->select());
  }
}
