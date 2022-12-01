<?php

namespace app\index\controller;


class Newly extends \app\index\controller\Base {
  public function _initialize() {
    parent::_initialize();
    $this->assign('navSelTag', 'linkList');
  }
  private function getSearch(string $modelName) {
    $t = explode(' ', input('param.search/s', '', 'trim'));
    $search = [];
    foreach ($t as $v) {
      if ($v) {
        $search[] = "%$v%";
      }
    }
    $hot = model('AdsType')->where('model', $modelName)->find()['content'];
    if (!$hot) {
      $hot = [];
    }
    foreach ($hot as $k => $v) {
      if (is_numeric($v) && ($t = model($modelName)->where('id', $v)->find())) {
        if (isset($t['name'])) {
          $hot[$k] = $t['name'];
        } elseif (isset($t['title'])) {
          $hot[$k] = $t['title'];
        }
      }
    }
    $this->assign('hot', $hot);
    $this->assign('search', str_replace('%', '', join(' ', $search)));
    return $search;
  }

  public function tst() {
    return "测试用添加链接, 请于后台修改对应url链接地址!";
  }
  public function tstlink() {
    return "测试用添加链接, 请于后台修改对应url链接地址!";
  }

  public function linkList() {
    $type = request()->route('type/d', 0, 'intval');
    $data = model('Link')->getByType($type);
    $this->assign('links', $data);
    $this->assign('type', $type);
    return $this->fetch('linkList');
  }
  public function jobWanted() {
    return $this->fetch('jobWanted');
  }

  public function city() {
    $this->assign('navSelTag', 'city');

    $search = $this->getSearch('CategoryDistrict');
    $returl = input('returl/s', '/', 'trim');
    $this->assign('returl', $returl);

    $m = model('CategoryDistrict');

    // die(encode($m->where('pid', 0)->field('id,pid,name,"全国" pname')->select()));
    $d = ((array)$this->view)["\0*\0data"]['weather'];
    $id = request()->route('id/d', 0, 'intval');
    if ($id && $id != $d['id']) {
      $t = $m->where('id', $id)->field('id')->find();
      if ($t) {
        $d = $m->getWeather($t['id']);
        $this->assign('weather', $d);
      }
    }
    if ($search) {
      $result = $m->getModel(['#like' => $search])->field('id,pid,name')->select();
      $this->assign('result', $result);
    } else {
      $d = [
        'sj' => $m->where('id', $d['pid'])->find(),
        'pj' => $m->where('pid', $d['pid'])->select(),
        'xj' => $m->where('pid', $d['id'])->select(),
      ];
      foreach ($d as $k => $v) {
        $this->assign($k, $v);
      }
    }
    return $this->fetch('city');
  }

  // 学习模块
  public function study() {
    $types = model('StudyType')->where('is_display', 1)->field('id,name')->select();
    $this->assign('types', $types);
    $type = request()->route('type/d', null, 'intval');
    if ($type && !findArr($types, $type)) {
      $type = null;
    }
    $this->assign('type', $type);
    // 是否财税
    $finance = request()->route('finance/d', null, 'intval');
    if ($finance) {
      $finance = 1;
    }
    $this->assign('finance', $finance);
    // 是否视频
    $video = request()->route('video/d', null, 'intval');
    if ($video) {
      $video = 1;
    }
    $this->assign('video', $video);

    $search = $this->getSearch('Study');
    $m = model('Study');
    $data = $m->getModel([
      'type' => $type,
      'is_caishui' => $finance,
      'is_video' => $video,
      '#like' => count($search) ? $search : null,
    ])->field('id,name,simple,cover,id `history`,id `report`,id `likes`,id `reprint`')->select();
    $this->assign('data', $data);

    return $this->fetch('newly/study/index');
  }
  public function studyInfo() {
    $id = request()->route('id/d', 0, 'intval');

    $k = 'page_study_' . $id;
    if ($id && !($data = cache($k))) {
      $data = model('Study')->where('id', $id)
        ->field('*,id `info5`')
        ->find();
      if (!$data) {
        abort(404, '页面不存在');
      }
      cache($k, $data);
    }
    if (!$data) {
      abort(404, '页面不存在');
    }

    $this->assign('data', $data);
    return $this->fetch('newly/study/' . ($data['is_video'] ? 'video' : 'article') . 'Details');
  }
  // 考试模块
  public function exam() {
    $types = model('ExamType')->where('is_display', 1)->field('id,name')->select();
    $this->assign('types', $types);

    $type = request()->route('type/d', null, 'intval');
    if ($type && !findArr($types, $type)) {
      $type = null;
    }
    $this->assign('type', $type);

    $search = $this->getSearch('Exam');

    $m = model('Exam');
    $data = $m->getModel([
      'type' => $type,
      '#like' => count($search) ? $search : null,
    ])->field('id,name,score,cover,id `history`,id `report`,id `likes`,id `reprint`')->select();
    $this->assign('data', $data);

    return $this->fetch('newly/exam/index');
  }
  public function examInfo() {
    $id = request()->route('id/d', 0, 'intval');
    $k = 'page_exam_' . $id;
    if ($id && !($data = cache($k))) {
      $data = clo(model('Exam')->where('id', $id)
        ->field('*,id `info5`')
        ->find());
      foreach ($data['content'] as $k => $v) {
        unset($v['answer']);
        $data['content'][$k] = $v;
      }
      if (!$data) {
        abort(404, '页面不存在');
      }
      cache($k, $data);
    }

    $this->assign('data', $data);
    return $this->fetch('newly/exam/show');
  }
  // 商店
  public function shop() {
    if (request()->route('goods/d', 0, 'intval') > 0) {
      return $this->goods();
    }
    $mn = 'Shop';
    $search = $this->getSearch($mn);
    $shop = request()->route('shop/d', null, 'intval');
    if ($shop !== null && $shop >= 0) { // 商店详情
      $this->assign('shop', $shop);
      $tj = [
        'shop' => $shop, '#like' => $search,
        '#method' => 'select',
        '#field' => 'id,name,cover,expense,sales,`create` user',
        '#alias' => 'a',
        '#method' => 'select',
        '#order' => 'sort_id desc,sort desc',
      ];
      $type = request()->route('type/s', null, 'trim');
      $this->assign('type', $type);
      $type2 = request()->route('type2/s', null, 'trim');
      $this->assign('type2', $type2);
      $data = model($mn)->r(
        ['id' => $shop],
        '*,`create` user'
          . ',\'' . encode($tj) . '\' `goods`'
          . ',concat(\'{"create":"\',`create`,\'","#method":"select"}\') type2'
      );
      // outp($data['goods']);
      $this->assign('data', $data);
      $types = model('GoodsType')->tree();
      $this->assign('types', $types);
      $types2 = model('GoodsType2')->where(['create' => $data['create'], 'pid' => 0])->field('id,name,pid,id children')->select();
      $this->assign('types2', $types2);
      return $this->fetch('newly/shop/info');
    }


    $list = model($mn)->r(['#like' => $search], '*,`create` `user`');
    $this->assign('list', $list);

    return $this->fetch('newly/shop/index');
  }
  public function goods() {
    $mn = 'Goods';
    $goods = request()->route('goods/d', 0, 'intval');
    if ($goods > 0) {
      $this->assign('search', '');
      $data = model($mn)->where(['id' => $goods, 'is_display' => 1])
        ->field('id,sum,name,simple,content,cover,banner,expense,type,type2,shop,id sales')
        // ->fetchSql()
        ->find();
      // outp($data);
      if ($data) {
        $types2 = model('GoodsType2')->where(['create' => $data['shop']['create'], 'pid' => 0])->field('id,name,pid,id children')->select();
        $this->assign('types2', $types2);

        $this->assign('data', $data);
        return $this->fetch('newly/shop/goods');
      }
      return '商品不存在';
    }
    $order = request()->route('order/s', '', 'trim');
    $this->assign('order', $order);
    $orderkey = request()->route('orderkey/d', 0, 'intval');
    $this->assign('orderkey', $orderkey);

    $search = $this->getSearch($mn);
    $type = request()->route('type/s', null, 'trim');
    $this->assign('type', $type);
    $types = model('GoodsType')->tree();
    $this->assign('types', $types);
    $o = $order ? ("$order " . ($orderkey ? 'desc' : 'asc')) : 'sort_id desc,sort desc';
    $data = model($mn)->getModel(['#like' => $search, 'type' => $type, 'is_display' => 1])
      ->field('id,name,cover,expense,sales,`create` user')
      ->order($o)
      // ->fetchSql()
      ->select();
    // die($data);
    $this->assign('data', $data);
    return $this->fetch('newly/shop/list');
  }

  // FRX新增
  // 商家中心(我的账户)
  public function merchantCenter() {
    return $this->fetch('personalCenter');
  }
  // 个人中心(我的账户)
  public function personalCenter() {
    return $this->fetch('personalCenter');
  }
  // 维权中心（投诉举报）
  public function defendRight() {
    return $this->fetch('defendRight');
  }
  // 维权中心（投诉举报）
  public function complaint() {
    return $this->fetch('complaint');
  }
  // 维权中心（申诉复议）
  public function appeal() {
    return $this->fetch('appeal');
  }
  // 关于籽虾
  public function zixiaAbout() {
    return $this->fetch('zixiaAbout');
  }
  // 籽虾常见问题详情页
  public function zixiaDetails() {
    return $this->fetch('zixiaDetails');
  }

  //代找帐
  public function financial() {
    return $this->fetch('financial');
  }
  // 实操实习--帮找实习单位
  public function financialJob() {
    return $this->fetch('financialJob');
  }
  // 实操实习--线下实操培训
  public function financialResume() {
    return $this->fetch('financialResume');
  }

  // 财务外包
  public function outsourcing() {
    return $this->fetch('outsourcing');
  }
  // 包就业
  public function employment() {
    return $this->fetch('employment');
  }
  // 劳务派遣用工
  public function jobLabor() {
    return $this->fetch('jobLabor');
  }
  // 劳务派遣就业
  public function resumeLabor() {
    return $this->fetch('resumeLabor');
  }
  // 人才调查
  public function assessment() {
    return $this->fetch('assessment');
  }
  // 背景调查
  public function investigation() {
    return $this->fetch('investigation');
  }

  // 背景调查--个人
  public function personalSurvey() {
    return $this->fetch('personalSurvey');
  }

  // 背景调查--单位
  public function companySurvey() {
    return $this->fetch('companySurvey');
  }

  //背景调查(查询报告)
  public function invesqueryReport() {
    return $this->fetch('invesqueryReport');
  }



  // 人才测评(理论知识考试系统--试卷列表)
  public function asstheoryTest() {
    return $this->fetch('asstheoryTest');
  }
  // 人才测评(理论知识考试系统--试卷详情)
  public function asstheoryTestDetails() {
    return $this->fetch('asstheoryTestDetails');
  }

  // 人才测评(查询报告)
  public function assqueryReport() {
    return $this->fetch('assqueryReport');
  }


  public function askPut() {
    return "提问页面";
  }
  public function askAnswer() {
    return "回答页面,问题列表";
  }





  // 财税问答
  public function askFinancial() {
    return $this->fetch('newly/ask/financial');
  }
  // 人社问答 
  public function askSocial() {
    return $this->fetch('newly/ask/social');
  }
  // 问答详情
  public function askDetails() {
    return $this->fetch('newly/ask/askDetails');
  }

  // 财税法规
  public function statuteFinancial() {
    return $this->fetch('newly/statute/financial');
  }
  // 人社法规 
  public function statuteSocial() {
    return $this->fetch('newly/statute/social');
  }
  // 法规详情页
  public function statuteDetails() {
    return $this->fetch('newly/statute/statuteDetails');
  }

  //财税游戏
  public function games() {
    return $this->fetch('games');
  }
  // 戏说财税
  public function entertain() {
    return $this->fetch('entertain');
  }
  // 领赏上传文档
  public function upload() {
    return $this->fetch('newly/file/upload');
  }
  // 免费下载文档
  public function download() {
    return $this->fetch('newly/file/download');
  }

  //籽虾早报
  public function morning() {
    return $this->fetch('morning');
  }
  // 籽虾月刊
  public function monthly() {
    return $this->fetch('monthly');
  }
  // 月刊(详情页)
  public function mDetails() {
    return $this->fetch('mDetails');
  }

  // 银行
  public function bank() {
    return $this->fetch('newly/officialWeb/bank');
  }


  // 购物
  public function shopping() {
    return $this->fetch('newly/officialWeb/shopping');
  }
  // 购物(购物车)
  public function shopCart() {
    return $this->fetch('newly/officialWeb/shopCart');
  }
  // 购物(店铺)
  public function shops() {
    return $this->fetch('newly/officialWeb/shops');
  }
  // 购物(我的)
  public function shopMy() {
    return $this->fetch('newly/officialWeb/shopMy');
  }
  // 购物(订单详情)
  public function shopOrder() {
    return $this->fetch('newly/officialWeb/shopOrder');
  }
  // 购物(商品详情页)
  public function shopDetails() {
    return $this->fetch('newly/officialWeb/shopDetails');
  }
  // //购物(我的订单)
  public function shopMyorder() {
    return $this->fetch('newly/officialWeb/shopMyorder');
  }
  //购物(收藏店铺)
  public function collectShop() {
    return $this->fetch('newly/officialWeb/collectShop');
  }
  //购物(收藏商品)
  public function collectProduct() {
    return $this->fetch('newly/officialWeb/collectProduct');
  }
  //购物(管理地址)
  public function shopAddress() {
    return $this->fetch('newly/officialWeb/shopAddress');
  }




  // 公司转让
  public function companySell() {
    return $this->fetch('newly/company/companySell');
  }
  // 公司求购
  public function companyBuy() {
    return $this->fetch('newly/company/companyBuy');
  }
  // 商标转让
  public function trademarkSell() {
    return $this->fetch('newly/company/trademarkSell');
  }
  // 商标求购
  public function trademarkBuy() {
    return $this->fetch('newly/company/trademarkBuy');
  }
  // 公司求购详情页
  public function buyCompanyDet() {
    return $this->fetch('newly/company/buyCompanyDet');
  }
  // 公司求购详情页
  public function buyTrademarkDet() {
    return $this->fetch('newly/company/buyTrademarkDet');
  }
  // 公司转让详情页
  public function sellCompanyDet() {
    return $this->fetch('newly/company/sellCompanyDet');
  }
  // 商标求购详情页
  public function sellTrademarkDet() {
    return $this->fetch('newly/company/sellTrademarkDet');
  }
  // 公司转让求购（我的）
  public function companyMy() {
    return $this->fetch('newly/company/companyMy');
  }




  // 企服
  public function service() {
    return $this->fetch('newly/officialWeb/service');
  }
  // 企服首页
  public function serviceHome() {
    return $this->fetch('newly/officialWeb/serviceHome');
  }
  // 发票检查
  public function invoice() {
    return $this->fetch('newly/officialWeb/invoice');
  }

  // 虾时光(虾圈)
  public function bbsTime() {
    return $this->fetch('newly/officialWeb/bbsTime');
  }
  // 虾时光(我的奋斗)
  public function bbsStruggle() {
    return $this->fetch('newly/officialWeb/bbsStruggle');
  }
  // 虾时光(记事本)
  public function bbsNotepad() {
    return $this->fetch('newly/officialWeb/bbsNotepad');
  }
  // 虾时光(提醒)positionDet
  public function bbsRemind() {
    return $this->fetch('newly/officialWeb/bbsRemind');
  }
  // 虾时光(籽虾达人)
  public function bbsMaster() {
    return $this->fetch('newly/officialWeb/bbsMaster');
  }
  // 虾时光(我的空间)
  public function bbsSpace() {
    return $this->fetch('newly/officialWeb/bbsSpace');
  }
  // 虾时光(我的空间详情页)
  public function bbsSpaceDet() {
    return $this->fetch('newly/officialWeb/bbsSpaceDet');
  }


  // 学术研究
  public function academic() {
    return $this->fetch('newly/officialWeb/academic');
  }
  // 学术文章
  public function academicArticles() {
    return $this->fetch('newly/officialWeb/academicArticles');
  }
  // 学术文章（详情页）
  public function academicDetails() {
    return $this->fetch('newly/officialWeb/academicDetails');
  }



  // 应用中心
  public function store() {
    return $this->fetch('newly/officialWeb/store');
  }
  // 云财税
  public function yFinance() {
    return $this->fetch('newly/officialWeb/yFinance');
  }
  // 财税生态链
  public function chain() {
    return $this->fetch('newly/chain');
  }

  // 我的发布（招聘）
  public function myPost() {
    return $this->fetch('newly/job/myPost');
  }
  // 我的公司（招聘）
  public function myCompany() {
    return $this->fetch('newly/job/myCompany');
  }
  // 我的公司（职位详情页）
  public function positionDet() {
    return $this->fetch('newly/job/positionDet');
  }
  // 发布招聘信息
  public function recruitmentInfo() {
    return $this->fetch('newly/job/recruitmentInfo');
  }




  // 我的发布
  public function comTransfer() {
    return $this->fetch('newly/comTransfer');
  }


  // 办事官网
  public function offWebsite() {
    return $this->fetch('newly/offWebsite');
  }
  // 网站导航
  public function webNav() {
    return $this->fetch('newly/webNav');
  }
  // 财务便捷窗口
  public function conWindow() {
    return $this->fetch('newly/conWindow');
  }


  // 
  public function ip() {
    $r = model('Member')->getId();
    return json_encode($r, JSON_UNESCAPED_UNICODE); // 
  }
}
