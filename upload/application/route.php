<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;
// 注册路由到index模块的News控制器的read操作
Route::get('s/:code', 'v1_0/home.ShortUrl/index');
Route::get('m', 'index/Mobile/index');
Route::get('member', 'index/MemberCenter/index');

Route::rule('/$', 'index/index/index', 'GET', ['ext' => '']);
Route::rule('job/:id$', 'index/job/show');
Route::rule('resume/:id$', 'index/resume/show');
// Route::rule('company/:id$', 'index/company/show');
Route::rule('article/:id$', 'index/article/show');
Route::rule('explain/:id$', 'index/explain/show');
Route::rule('notice/:id$', 'index/notice/show');
Route::rule('hrtool/:id$', 'index/hrtool/show');
Route::rule('jobfair/:id$', 'index/jobfair/show');
Route::rule('jobfairol/:id$', 'index/jobfairol/show');
Route::rule('campus/school/:id$', 'index/campus/school_show');
Route::rule('campus/school/election/:id$', 'index/campus/school_election');
Route::rule('campus/school/preach/:id$', 'index/campus/school_preach');
Route::rule('campus/election/:id$', 'index/campus/election_show');
Route::rule('campus/preach/:id$', 'index/campus/preach_show');
Route::rule('campus/notice/:id$', 'index/campus/notice_show');
Route::rule('freelance/resume/:id$', 'index/freelance/resume_show');
Route::rule('freelance/subject/:id$', 'index/freelance/subject_show');

Route::rule('resume/contrast', 'index/resume/contrast');
// Route::rule('company', 'index/company/index');
Route::rule('article', 'index/article/index'); // 咨询 文章
Route::rule('notice', 'index/notice/index');
Route::rule('help', 'index/help/show');
Route::rule('hrtool', 'index/hrtool/index');
Route::rule('map', 'index/map/index');
Route::rule('video/:id', 'index/video/main');
Route::rule('jobfair', 'index/jobfair/index');
Route::rule('jobfairol/details/:id$', 'index/jobfairol/details'); //网络招聘会详情
Route::rule('jobfairol', 'index/jobfairol/index');
Route::rule('campus/school', 'index/campus/school');
Route::rule('campus/election', 'index/campus/election'); //校园招聘双选会列表页
Route::rule('campus/preach', 'index/campus/preach'); //校园招聘宣讲会列表页
Route::rule('campus/job', 'index/campus/job'); //校园招聘职位列表页
Route::rule('campus/notice', 'index/campus/notice'); //校园招聘资讯列表页
Route::rule('campus', 'index/campus/index'); //校园招聘首页
Route::rule('freelance', 'index/freelance/index'); //自由职业首页
Route::rule('freelance/resume', 'index/freelance/resume'); //自由职业简历列表
Route::rule('freelance/subject', 'index/freelance/subject'); //自由职业项目列表
Route::rule('fast/job', 'index/fast/job'); //快捷招聘
Route::rule('fast/resume', 'index/fast/resume'); //快捷招聘

Route::rule('dailyDetail/:id$', 'index/recruitment/show'); //今日招聘详情
Route::rule('dailyList', 'index/recruitment/index'); //今日招聘列表
Route::rule('job/register', 'index/job/register'); // 求职登记
//修改路由名称触屏PC保持一致
Route::rule('shortvideo', 'index/video_recruitment/index'); // 视频招聘

Route::rule('job', 'index/job/index'); //免费求职
// Route::rule('company', 'index/company/index');
Route::rule('job/contrast', 'index/job/contrast');

Route::rule('/tstlink', 'index/newly/tstlink');


//*** frx修改添加***
Route::rule('resume', 'index/resume/index'); //免费招聘


Route::rule('fast/job', 'index/fast/job'); //代找工作
Route::rule('fast/resume', 'index/fast/resume'); //代招人才

// Route::rule('company', 'index/company/index');
Route::rule('job/contrast', 'index/job/contrast');

Route::rule([
  'url' => 'v1_0/Method/url',
  'qr' => 'v1_0/Method/qr',
  'study/:id$' => 'index/newly/studyInfo', // 学习详情模块
  'study' => 'index/newly/study', // 学习列表模块

  // 'study/article' => 'index/newly/article', //免费学习(图文)
  // 'study/articleDetails' => 'index/newly/articleDetails', //免费学习(图文详情)
  // 'study/video' => 'index/newly/video', //免费学习(视频)
  // 'study/videoDetails' => 'index/newly/videoDetails', //免费学习(视频详情)


  'exam/:id$' => 'index/newly/examInfo', // 考试详情模块
  'exam' => 'index/newly/exam', // 考试列表模块

  'shop' => 'index/newly/shop', // 商店列表
  'goods' => 'index/newly/goods', // 商品列表

  'officialWeb/shopping' => 'index/newly/shopping', //购物
  'officialWeb/shopDetails' => 'index/newly/shopDetails', //购物(商品详情页)
  'officialWeb/shopCart' => 'index/newly/shopCart', //购物(购物车)
  'officialWeb/shops' => 'index/newly/shops', //购物(店铺首页)
  'officialWeb/shopMy' => 'index/newly/shopMy', //购物(我的)
  'officialWeb/shopMyorder' => 'index/newly/shopMyorder', //购物(我的订单)
  'officialWeb/shopOrderDetails' => 'index/newly/shopOrderDetails', //购物(订单详情)
  'officialWeb/collectShop' => 'index/newly/collectShop', //购物(收藏店铺)
  'officialWeb/collectProduct' => 'index/newly/collectProduct', //购物(收藏商品)
  'officialWeb/shopAddress' => 'index/newly/shopAddress', //购物(管理地址)


  'job/myPost' => 'index/newly/myPost', //招聘（我的发布）
  'job/myCompany' => 'index/newly/myCompany', //招聘（公司介绍）
  'job/positionDet' => 'index/newly/positionDet', //招聘（职位详情）
  'job/recruitmentInfo' => 'index/newly/recruitmentInfo', //发布招聘信息

  'linkList/:type' => 'index/newly/linkList', // 链接列表
  'linkList' => 'index/newly/linkList', // 链接列表
  'jobwant' => 'index/newly/jobWanted', // 免费求职（个人）
  'city/:id$' => 'index/newly/city', // 切换城市
  'city' => 'index/newly/city', // 切换城市

  'merchantCenter' => 'index/newly/merchantCenter', //商家中心
  'personalCenter' => 'index/newly/personalCenter', //个人中心


  'defendRight' => 'index/newly/defendRight', //维权中心
  'complaint' => 'index/newly/complaint', //投诉举报
  'appeal' => 'index/newly/appeal', //申诉复议

  'zixiaAbout' => 'index/newly/zixiaAbout', //关于籽虾
  'zixiaDetails' => 'index/newly/zixiaDetails', //籽虾常见问题详情页



  'financial' => 'index/newly/financial', //代找帐
  'financialJob' => 'index/newly/financialJob', //实操实习-帮找实习单位
  'financialResume' => 'index/newly/financialResume', //实操实习-线下实操培训
  'outsourcing' => 'index/newly/outsourcing', //财务外包
  'employment' => 'index/newly/employment', //包就业
  'jobLabor' => 'index/newly/jobLabor', //劳务派遣用工
  'resumeLabor' => 'index/newly/resumeLabor', //劳务派遣就业

  'assessment' => 'index/newly/assessment', //人才测评
  'asstheoryTest' => 'index/newly/asstheoryTest', //人才测评(理论知识考试系统--试卷列表)
  'assqueryReport' => 'index/newly/assqueryReport', //人才测评(查询报告)
  'asstheoryTestDetails' => 'index/newly/asstheoryTestDetails', //人才测评(理论知识考试系统--试卷详情·)


  'personalSurvey' => 'index/newly/personalSurvey', //个人背景调查
  'companySurvey' => 'index/newly/companySurvey', //单位背景调查
  'invesqueryReport' => 'index/newly/invesqueryReport', //背景调查(查询报告)




  'ask/put' => 'index/newly/askPut', // 提问页面
  'ask/answer' => 'index/newly/askAnswer', // 回答页面.问题列表
  'ask/financial' => 'index/newly/askFinancial', //财税问答
  'ask/social' => 'index/newly/askSocial', //人社问答
  'ask/askDetails' => 'index/newly/askDetails', //问答详情
  'ask/:id$' => 'index/newly/askDetails',

  'statute/financial' => 'index/newly/statuteFinancial', //财税法规
  'statute/social' => 'index/newly/statuteSocial', //人社法规
  'statute/statuteDetails' => 'index/newly/statuteDetails', //法规详情页

  'games' => 'index/newly/games', //财税游戏
  'entertain' => 'index/newly/entertain', //戏说财税
  
  'file/upload' => 'index/newly/upload', //领赏上传文档
  'file/download' => 'index/newly/download', //免费下载文档
  'download' => 'index/newly/download', //免费下载文档

  'morning' => 'index/newly/morning', //籽虾早报
  'monthly' => 'index/newly/monthly', //籽虾月刊
  'mDetails' => 'index/newly/mDetails', //（早报、月刊）详情页

  'officialWeb/bank' => 'index/newly/bank', //银行





  'company/companySell' => 'index/newly/companySell', //公司转让
  'company/companyBuy' => 'index/newly/companyBuy', //公司求购
  'company/trademarkSell' => 'index/newly/trademarkSell', //商标转让
  'company/trademarkBuy' => 'index/newly/trademarkBuy', //商标求购
  'company/buyCompanyDet' => 'index/newly/buyCompanyDet', //公司求购详情页
  'company/buyTrademarkDet' => 'index/newly/buyTrademarkDet', //商标求购详情页
  'company/sellCompanyDet' => 'index/newly/sellCompanyDet', // 公司转让详情页
  'company/sellTrademarkDet' => 'index/newly/sellTrademarkDet', //商标转让详情页
  'company/companyMy' => 'index/newly/companyMy', //公司转让求购（我的）








  'officialWeb/service' => 'index/newly/service', //企服
  'officialWeb/serviceHome' => 'index/newly/serviceHome', //企服首页


  'officialWeb/invoice' => 'index/newly/invoice', //发票检查

  'officialWeb/bbsTime' => 'index/newly/bbsTime', //虾时光(虾圈)
  'officialWeb/bbsStruggle' => 'index/newly/bbsStruggle', //虾时光(我的奋斗)
  'officialWeb/bbsNotepad' => 'index/newly/bbsNotepad', //虾时光(记事本)
  'officialWeb/bbsRemind' => 'index/newly/bbsRemind', //虾时光(提醒)
  'officialWeb/bbsMaster' => 'index/newly/bbsMaster', //虾时光(籽虾达人)
  'officialWeb/bbsSpace' => 'index/newly/bbsSpace', //虾时光(我的空间)
  'officialWeb/bbsSpaceDet' => 'index/newly/bbsSpaceDet', //虾时光(我的空间详情页)




  'officialWeb/academic' => 'index/newly/academic', //学术研究
  'officialWeb/academicArticles' => 'index/newly/academicArticles', //学术文章
  'officialWeb/academicDetails' => 'index/newly/academicDetails', //学术文章(查看详情)



  'officialWeb/store' => 'index/newly/store', //应用中心
  'officialWeb/yFinance' => 'index/newly/yFinance', //云财税
  'chain' => 'index/newly/chain', //财税生态链

  'comTransfer' => 'index/newly/comTransfer', //公司转让

  'offWebsite' => 'index/newly/offWebsite', //办事官网
  'webNav' => 'index/newly/webNav', //网站导航
  'conWindow' => 'index/newly/conWindow', //财务便捷窗口










]);
