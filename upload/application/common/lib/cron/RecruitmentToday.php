<?php


namespace app\common\lib\cron;


use app\common\lib\Poster;

class RecruitmentToday {
  public function execute() {
    $config = model('RecruitmentTodayConfig')->select();
    $arr = [];
    foreach ($config as $k => $v) {
      $arr[$v['name']] = $v['value'];
    }
    if ($arr['is_automatic'] == 1) {
      $time = time();
      $recruitment_today_arr = [
        'subject_name' => date('n月j日') . $arr['subject_name'],
        'is_display' => 1,
        'addtime' => $time,
        'refreshtime' => $time,
        'template_id' => $arr['template_id']
      ];
      $where = [];

      switch ($arr['filter_company']) {
        case 1: //过滤无职位企业
          $where['j.id'] = ['not null', ''];
          break;
        case 2: //过滤未认证企业
          $where['c.audit'] = 1;
          break;
        case 3: //过滤不显示企业
          $where['c.is_display'] = 1;
          break;
      }
      if ($arr['sort_id'] == 1) {
        $order = ['c.refreshtime' => 'desc'];
      } else {
        $order = ['c.addtime' => 'desc'];
      }
      $where['c.companyname'] = ['neq', ''];
      $company_id = model('company')
        ->alias('c')
        ->join('JobSearchRtime j', 'j.company_id=c.id', 'left')
        ->where($where)->order($order)
        ->group('c.id')
        ->limit($arr['number'])
        ->column('c.id');
      $recruitment_today_id = model('RecruitmentToday')->insertGetId($recruitment_today_arr);

      foreach ($company_id as $v) {
        $recruitment_today_company[] = [
          'recruitment_today_id' => $recruitment_today_id,
          'company_id' => $v,
          'addtime' => $time,
          'refreshtime' => $time,
          'data_sources' => 1
        ];
      }
      model('RecruitmentTodayCompany')->insertAll($recruitment_today_company);
      $this->todayLogo($recruitment_today_id);
    }
  }

  public function todayLogo($id) {
    $poster = new \app\common\lib\Poster();
    $pc_result = $poster->recruitmentTodayLogo($id);
    $mobile_result = $poster->recruitmentTodayLogo($id, 'mobile');
    $mobile_result = $poster->recruitmentTodayLogo($id, 'list');
    return true;
  }
}
