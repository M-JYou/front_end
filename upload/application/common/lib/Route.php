<?php

namespace app\common\lib;

class Route {
  protected $def = [
    'rule' => [
      '/' => 'index/index/index', //首页
      'job' => 'index/job/index', //职位列表页
      'job/contrast' => 'index/job/contrast', //职位对比
      'resume' => 'index/resume/index', //简历列表页
      'resume/contrast' => 'index/resume/contrast', //简历对比
      'company' => 'index/company/index', //企业列表页
      'job/:id' => ['index/job/show', ['ext' => 'html'], ['id' => '\d+']], //职位详情页
      'resume/:id' => ['index/resume/show', ['ext' => 'html'], ['id' => '\d+']], //简历详情页
      'company/:id' => [
        'index/company/show',
        ['ext' => 'html'],
        ['id' => '\d+']
      ], //企业详情页
      'article' => 'index/article/index', //资讯列表页
      'article/:id' => [
        'index/article/show',
        ['ext' => 'html'],
        ['id' => '\d+']
      ], //资讯详情页
      'explain/:id' => [
        'index/explain/show',
        ['ext' => 'html'],
        ['id' => '\d+']
      ], //说明页详情页
      'notice' => 'index/notice/index', //公告列表页
      'notice/:id' => [
        'index/notice/show',
        ['ext' => 'html'],
        ['id' => '\d+']
      ], //公告详情页
      'help' => 'index/help/show', //帮助详情页
      'hrtool' => 'index/hrtool/index', //hr工具箱
      'hrtool/:id' => [
        'index/hrtool/show',
        ['ext' => 'html'],
        ['id' => '\d+']
      ], //hr工具箱详情页
      'map' => 'index/map/index', //地图找工作
      'video/:id' => [
        'index/video/main',
        ['ext' => 'html'],
        ['id' => '\d+']
      ], //视频面试
      'jobfair' => 'index/jobfair/index', //招聘会列表页
      'jobfair/:id' => [
        'index/jobfair/show',
        ['ext' => 'html'],
        ['id' => '\d+']
      ], //招聘会详情
      'jobfairol' => 'index/jobfairol/index', //网络招聘会列表页
      'jobfairol/:id' => [
        'index/jobfairol/show',
        ['ext' => 'html'],
        ['id' => '\d+']
      ], //网络招聘会详情
      'campus/school' => 'index/campus/school', //校园招聘院校列表页
      'campus/school/:id' => [
        'index/campus/school_show',
        ['ext' => 'html'],
        ['id' => '\d+']
      ], //校园招聘院校详情
      'campus/school/election/:id' => [
        'index/campus/school_election',
        ['ext' => 'html'],
        ['id' => '\d+']
      ], //校园招聘院校详情双选会列表
      'campus/school/preach/:id' => [
        'index/campus/school_preach',
        ['ext' => 'html'],
        ['id' => '\d+']
      ], //校园招聘院校详情宣讲会列表
      'campus/election' => 'index/campus/election', //校园招聘双选会列表页
      'campus/election/:id' => [
        'index/campus/election_show',
        ['ext' => 'html'],
        ['id' => '\d+']
      ], //校园招聘双选会详情
      'campus/preach' => 'index/campus/preach', //校园招聘宣讲会列表页
      'campus/preach/:id' => [
        'index/campus/preach_show',
        ['ext' => 'html'],
        ['id' => '\d+']
      ], //校园招聘双选会详情
      'campus/job' => 'index/campus/job', //校园招聘职位列表页
      'campus/notice' => 'index/campus/notice', //校园招聘资讯列表页
      'campus/notice/:id' => [
        'index/campus/notice_show',
        ['ext' => 'html'],
        ['id' => '\d+']
      ], //校园招聘资讯详情
      'campus' => 'index/campus/index', //校园招聘首页
      'freelance' => 'index/freelance/index', //自由职业首页
      'freelance/resume' => 'index/freelance/resume', //自由职业简历列表
      'freelance/subject' => 'index/freelance/subject', //自由职业项目列表
      'freelance/resume/:id' => [
        'index/freelance/resume_show',
        ['ext' => 'html'],
        ['id' => '\d+']
      ], //自由职业简历详情
      'freelance/subject/:id' => [
        'index/freelance/subject_show',
        ['ext' => 'html'],
        ['id' => '\d+']
      ], //自由职业项目详情
      'fast/job' => 'index/fast/job', //快捷招聘
      'fast/resume' => 'index/fast/resume', //快捷招聘
    ],
    'ext' => ''
  ];
  protected $qishi_6_0_min = [
    'rule' => [
      '/' => 'index/index/index', //首页
      'jobs/:id' => ['index/job/show', ['ext' => 'html'], ['id' => '\d+']], //职位详情页
      'jobs' => 'index/job/index', //职位列表页
      'jobs/contrast' => 'index/job/contrast', //职位对比
      'resume/:id' => ['index/resume/show', ['ext' => 'html'], ['id' => '\d+']], //简历详情页
      'resume' => 'index/resume/index', //简历列表页
      'resume/contrast' => 'index/resume/contrast', //简历对比
      'companylist' => 'index/company/index', //企业列表页
      'company/:id' => [
        'index/company/show',
        ['ext' => 'html'],
        ['id' => '\d+']
      ], //企业详情页
      'news/:id' => [
        'index/article/show',
        ['ext' => 'html'],
        ['id' => '\d+']
      ], //资讯详情页
      'news' => 'index/article/index', //资讯列表页
      'explain/:id' => [
        'index/explain/show',
        ['ext' => 'html'],
        ['id' => '\d+']
      ], //说明页详情页
      'notice/:id' => [
        'index/notice/show',
        ['ext' => 'html'],
        ['id' => '\d+']
      ], //公告详情页
      'notice' => 'index/notice/index', //公告列表页
      'help' => 'index/help/show', //帮助详情页
      'hrtools/list/:id' => [
        'index/hrtool/show',
        ['ext' => 'html'],
        ['id' => '\d+']
      ], //hr工具箱详情页
      'hrtools' => 'index/hrtool/index', //hr工具箱
      'map' => 'index/map/index', //地图找工作
      'video/:id' => [
        'index/video/main',
        ['ext' => 'html'],
        ['id' => '\d+']
      ], //视频面试
      'jobfair/:id' => [
        'index/jobfair/show',
        ['ext' => 'html'],
        ['id' => '\d+']
      ], //招聘会详情
      'jobfair' => 'index/jobfair/index', //网络招聘会列表页
      'jobfairol/:id' => [
        'index/jobfairol/show',
        ['ext' => 'html'],
        ['id' => '\d+']
      ], //招聘会详情
      'jobfairol' => 'index/jobfairol/index', //网络招聘会列表页
      'campus/school/:id' => [
        'index/campus/school_show',
        ['ext' => 'html'],
        ['id' => '\d+']
      ], //校园招聘院校详情
      'campus/school/election/:id' => [
        'index/campus/school_election',
        ['ext' => 'html'],
        ['id' => '\d+']
      ], //校园招聘院校详情双选会列表
      'campus/school/preach/:id' => [
        'index/campus/school_preach',
        ['ext' => 'html'],
        ['id' => '\d+']
      ], //校园招聘院校详情宣讲会列表
      'campus/school' => 'index/campus/school', //校园招聘院校列表页
      'campus/election/:id' => [
        'index/campus/election_show',
        ['ext' => 'html'],
        ['id' => '\d+']
      ], //校园招聘双选会详情
      'campus/election' => 'index/campus/election', //校园招聘双选会列表页
      'campus/preach/:id' => [
        'index/campus/preach_show',
        ['ext' => 'html'],
        ['id' => '\d+']
      ], //校园招聘双选会详情
      'campus/preach' => 'index/campus/preach', //校园招聘宣讲会列表页
      'campus/job' => 'index/campus/job', //校园招聘职位列表页
      'campus/notice/:id' => [
        'index/campus/notice_show',
        ['ext' => 'html'],
        ['id' => '\d+']
      ], //校园招聘资讯详情
      'campus/notice' => 'index/campus/notice', //校园招聘资讯列表页
      'campus' => 'index/campus/index', //校园招聘首页
      'freelance' => 'index/freelance/index', //自由职业首页
      'freelance/resume' => 'index/freelance/resume', //自由职业简历列表
      'freelance/subject' => 'index/freelance/subject', //自由职业项目列表
      'freelance/resume/:id' => [
        'index/freelance/resume_show',
        ['ext' => 'html'],
        ['id' => '\d+']
      ], //自由职业简历详情
      'freelance/subject/:id' => [
        'index/freelance/subject_show',
        ['ext' => 'html'],
        ['id' => '\d+']
      ], //自由职业项目详情
      'fast/job' => 'index/fast/job', //快捷招聘
      'fast/resume' => 'index/fast/resume', //快捷招聘
    ],
    'ext' => 'html'
  ];
  protected $qishi_3_7 = [
    'rule' => [
      '/' => 'index/index/index', //首页
      'jobs/index' => 'index/job/index', //职位列表页
      'job/contrast' => 'index/job/contrast', //职位对比
      'resume/index' => 'index/resume/index', //简历列表页
      'resume/contrast' => 'index/resume/contrast', //简历对比
      'companylist' => 'index/company/index', //企业列表页
      'jobs/jobs-show-<id>' => ['index/job/show', ['id' => '\d+']], //职位详情页
      'resume/resume-show-<id>' => ['index/resume/show', ['id' => '\d+']], //简历详情页
      'company/company-show-<id>' => [
        'index/company/show',
        ['id' => '\d+']
      ], //企业详情页
      'news/index' => 'index/article/index', //资讯列表页
      'news/news-show-<id>' => ['index/article/show', ['id' => '\d+']], //资讯详情页
      'explain/explain-show-<id>' => ['index/explain/show', ['id' => '\d+']], //说明页详情页
      'notice/notice-show-<id>' => ['index/notice/show', ['id' => '\d+']], //公告详情页
      'notice' => 'index/notice/index', //公告列表页
      'help' => 'index/help/show', //帮助详情页
      'hrtools/index' => 'index/hrtool/index', //hr工具箱
      'hrtools/hrtools-list-<id>' => [
        'index/hrtool/show',
        ['id' => '\d+']
      ], //hr工具箱详情页
      'map' => 'index/map/index', //地图找工作
      'video/:id' => [
        'index/video/main',
        ['ext' => 'html'],
        ['id' => '\d+']
      ], //视频面试
      'jobfair' => 'index/jobfair/index', //招聘会列表页
      'jobfair/:id' => [
        'index/jobfair/show',
        ['ext' => 'html'],
        ['id' => '\d+']
      ], //招聘会详情
      'jobfairol' => 'index/jobfairol/index', //网络招聘会列表页
      'jobfairol/:id' => [
        'index/jobfairol/show',
        ['ext' => 'html'],
        ['id' => '\d+']
      ], //网络招聘会详情
      'campus/school' => 'index/campus/school', //校园招聘院校列表页
      'campus/school/:id' => [
        'index/campus/school_show',
        ['ext' => 'html'],
        ['id' => '\d+']
      ], //校园招聘院校详情
      'campus/school/election/:id' => [
        'index/campus/school_election',
        ['ext' => 'html'],
        ['id' => '\d+']
      ], //校园招聘院校详情双选会列表
      'campus/school/preach/:id' => [
        'index/campus/school_preach',
        ['ext' => 'html'],
        ['id' => '\d+']
      ], //校园招聘院校详情宣讲会列表
      'campus/election' => 'index/campus/election', //校园招聘双选会列表页
      'campus/election/:id' => [
        'index/campus/election_show',
        ['ext' => 'html'],
        ['id' => '\d+']
      ], //校园招聘双选会详情
      'campus/preach' => 'index/campus/preach', //校园招聘宣讲会列表页
      'campus/preach/:id' => [
        'index/campus/preach_show',
        ['ext' => 'html'],
        ['id' => '\d+']
      ], //校园招聘双选会详情
      'campus/job' => 'index/campus/job', //校园招聘职位列表页
      'campus/notice' => 'index/campus/notice', //校园招聘资讯列表页
      'campus/notice/:id' => [
        'index/campus/notice_show',
        ['ext' => 'html'],
        ['id' => '\d+']
      ], //校园招聘资讯详情
      'campus' => 'index/campus/index', //校园招聘首页
      'freelance' => 'index/freelance/index', //自由职业首页
      'freelance/resume' => 'index/freelance/resume', //自由职业简历列表
      'freelance/subject' => 'index/freelance/subject', //自由职业项目列表
      'freelance/resume/:id' => [
        'index/freelance/resume_show',
        ['ext' => 'html'],
        ['id' => '\d+']
      ], //自由职业简历详情
      'freelance/subject/:id' => [
        'index/freelance/subject_show',
        ['ext' => 'html'],
        ['id' => '\d+']
      ], //自由职业项目详情
      'fast/job' => 'index/fast/job', //快捷招聘
      'fast/resume' => 'index/fast/resume', //快捷招聘
    ],
    'ext' => 'htm'
  ];
  protected $qishi_6_0_pathinfo = [
    'rule' => [
      '/' => 'index/index/index', //首页
      'jobs/jobs_list' => 'index/job/index', //职位列表页
      'job/contrast' => 'index/job/contrast', //职位对比
      'resume/resume_list' => 'index/resume/index', //简历列表页
      'resume/contrast' => 'index/resume/contrast', //简历对比
      'jobs/company_list' => 'index/company/index', //企业列表页
      'jobs/jobs_show/id/:id' => ['index/job/show', ['id' => '\d+']], //职位详情页
      'resume/resume_show/id/:id' => [
        'index/resume/show',
        ['id' => '\d+']
      ], //简历详情页
      'jobs/com_show/id/:id' => ['index/company/show', ['id' => '\d+']], //企业详情页
      'news/index' => 'index/article/index', //资讯列表页
      'news/news_show/id/:id' => ['index/article/show', ['id' => '\d+']], //资讯详情页
      'explain/explain_show/id/:id' => [
        'index/explain/show',
        ['id' => '\d+']
      ], //说明页详情页
      'notice/index' => 'index/notice/index', //公告列表页
      'notice/notice_show/id/:id' => [
        'index/notice/show',
        ['id' => '\d+']
      ], //公告详情页
      'help/help_list' => 'index/help/show', //帮助详情页
      'hrtools/index' => 'index/hrtool/index', //hr工具箱
      'hrtool/hrtools_list/:id' => [
        'index/hrtool/show',
        ['id' => '\d+']
      ], //hr工具箱详情页
      'map' => 'index/map/index', //地图找工作
      'video/:id' => [
        'index/video/main',
        ['id' => '\d+']
      ], //视频面试
      'jobfair' => 'index/jobfair/index', //招聘会列表页
      'jobfair/show/id/:id' => [
        'index/jobfair/show',
        ['id' => '\d+']
      ], //招聘会详情
      'jobfairol' => 'index/jobfairol/index', //网络招聘会列表页
      'jobfairol/show/id/:id' => [
        'index/jobfairol/show',
        ['id' => '\d+']
      ], //网络招聘会详情
      'campus/school' => 'index/campus/school', //校园招聘院校列表页
      'campus/school/show/id/:id' => [
        'index/campus/school_show',
        ['id' => '\d+']
      ], //校园招聘院校详情
      'campus/school/election/id/:id' => [
        'index/campus/school_election',
        ['id' => '\d+']
      ], //校园招聘院校详情双选会列表
      'campus/school/preach/id/:id' => [
        'index/campus/school_preach',
        ['id' => '\d+']
      ], //校园招聘院校详情宣讲会列表
      'campus/election' => 'index/campus/election', //校园招聘双选会列表页
      'campus/election/show/id/:id' => [
        'index/campus/election_show',
        ['id' => '\d+']
      ], //校园招聘双选会详情
      'campus/preach' => 'index/campus/preach', //校园招聘宣讲会列表页
      'campus/preach/show/id/:id' => [
        'index/campus/preach_show',
        ['id' => '\d+']
      ], //校园招聘双选会详情
      'campus/job' => 'index/campus/job', //校园招聘职位列表页
      'campus/notice' => 'index/campus/notice', //校园招聘资讯列表页
      'campus/notice/show/id/:id' => [
        'index/campus/notice_show',
        ['id' => '\d+']
      ], //校园招聘资讯详情
      'campus' => 'index/campus/index', //校园招聘首页
      'freelance' => 'index/freelance/index', //自由职业首页
      'freelance/resume' => 'index/freelance/resume', //自由职业简历列表
      'freelance/subject' => 'index/freelance/subject', //自由职业项目列表
      'freelance/resume/:id' => [
        'index/freelance/resume_show',
        ['ext' => 'html'],
        ['id' => '\d+']
      ], //自由职业简历详情
      'freelance/subject/:id' => [
        'index/freelance/subject_show',
        ['ext' => 'html'],
        ['id' => '\d+']
      ], //自由职业项目详情
      'fast/job' => 'index/fast/job', //快捷招聘
      'fast/resume' => 'index/fast/resume', //快捷招聘
    ],
    'ext' => ''
  ];
  public function getRule($alias) {
    switch ($alias) {
      case 'def':
        return $this->def;
        break;
      case 'qishi_3_7':
        return $this->qishi_3_7;
        break;
      case 'qishi_6_0_min':
        return $this->qishi_6_0_min;
        break;
      case 'qishi_6_0_pathinfo':
        return $this->qishi_6_0_pathinfo;
        break;
      default:
        return $this->def;
        break;
    }
  }
}
