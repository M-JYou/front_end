<?php

namespace app\common\lib\tpl\index;

class tpl4 extends def {
  public $visitor = null;

  public function __construct($visitor) {
    $this->visitor = $visitor;
  }

  public function getData($pageCache, $pageAlias) {
    $return['category_jobs'] = $this->getCategoryJobs();
    $return['notice_list'] = $this->getNoticeList(4);
    $return['event_list'] = $this->getEventList();
    $return['tag_list'] = $this->getTagList();
    $return['new_today_list'] = $this->getNewTodayList();
    $return['emergency_list'] = $this->getEmergencyList(10);
    $return['hotword_list'] = $this->getHotwordList(15);
    $return['famous_list'] = $this->getFamousList(15);
    $return['hotjob_list'] = $this->getHotjobList();
    $return['company_list'] = $this->getCompanyList();
    $return['resume_list'] = $this->getResumeList(7);
    $return['article_list'] = $this->getArticleList();
    $return['hrtool_list'] = $this->getHrtoolList();
    $return['flink_list'] = $this->getFlinkList();
    $return['banner_list'] = $this->getBannerList();
    // 最新职位
    $return['newjob_list'] = $this->getNewjobList(12);
    // 推荐企业
    $return['hot_company_list'] = $this->getHotCompanyList();
    // 名企
    $return['famous_company_list'] = $this->getFamousCompany(16);
    // 新闻资讯分类
    $return['article_category'] = $this->getArticleCategory(6);
    // 招聘会
    $return['jobfair_list'] = $this->getJobfairList(4);

    if ($pageCache['expire'] > 0) {
      model('Page')->writeCacheByAlias($pageAlias, $return, $pageCache['expire']);
    }
    return $return;
  }

  /** 获取职位分类 */
  protected function getCategoryJobs() {
    $list = model('CategoryJob')->getCache('');
    return $list;
  }

  /** 公告列表 */
  protected function getNoticeList($limit = 10) {
    $list = model('Notice')->where('is_display', 1)->order('sort_id desc,id desc')->limit($limit)->column('id,title,link_url');
    foreach ($list as $key => $value) {
      $list[$key]['link_url'] = $value['link_url'] == '' ? url('index/notice/show', ['id' => $value['id']]) : $value['link_url'];
      $list[$key]['target'] = $value['link_url'] == '' ? '_self' : '_blank';
    }
    return $list;
  }

  /** 动态列表 */
  protected function getEventList() {
    //发布职位（包含刷新职位）
    $list1 = model('JobSearchRtime')->alias('a')->join(config('database.prefix') . 'job b', 'a.id=b.id', 'LEFT')->join(config('database.prefix') . 'company c', 'a.uid=c.uid', 'LEFT')->where('c.id', 'not null')->order('a.refreshtime desc')->limit(15)->column('a.refreshtime,a.id,a.company_id,b.jobname,c.companyname', 'a.id');

    //申请职位
    $list2 = model('JobApply')->alias('a')->join(config('database.prefix') . 'resume b', 'a.resume_id=b.id', 'LEFT')->order('a.id desc')->limit(15)->column('a.addtime,a.resume_id,a.jobid,a.jobname,b.fullname,b.sex,b.display_name', 'a.id');

    //刷新简历
    $list3 = model('ResumeSearchRtime')->alias('a')->join(config('database.prefix') . 'resume b', 'a.id=b.id', 'LEFT')->where('addtime', 'egt', strtotime('-1 hour'))->order('a.refreshtime desc')->limit(15)->column('a.refreshtime,a.id,b.fullname,b.sex,b.display_name', 'a.id');

    $list = [];
    foreach ($list1 as $key => $value) {
      $arr = [];
      $arr['type'] = 'jobadd';
      $arr['job_url'] = url('index/job/show', ['id' => $value['id']]);
      $arr['jobname'] = $value['jobname'];
      $arr['company_url'] = url('index/company/show', ['id' => $value['company_id']]);
      $arr['companyname'] = $value['companyname'];
      $arr['time'] = $value['refreshtime'];
      $arr['time_cn'] = daterange(time(), $value['refreshtime']);;
      $list[] = $arr;
    }
    foreach ($list2 as $key => $value) {
      $arr = [];
      $arr['type'] = 'jobapply';
      $arr['job_url'] = url('index/job/show', ['id' => $value['jobid']]);
      $arr['jobname'] = $value['jobname'];
      $arr['resume_url'] = url('index/resume/show', ['id' => $value['resume_id']]);
      $arr['fullname'] = $value['fullname'];
      if ($value['display_name'] == 0) {
        if ($value['sex'] == 1) {
          $arr['fullname'] = cut_str(
            $value['fullname'],
            1,
            0,
            '先生'
          );
        } elseif ($value['sex'] == 2) {
          $arr['fullname'] = cut_str(
            $value['fullname'],
            1,
            0,
            '女士'
          );
        } else {
          $arr['fullname'] = cut_str(
            $value['fullname'],
            1,
            0,
            '**'
          );
        }
      }
      $arr['time'] = $value['addtime'];
      $arr['time_cn'] = daterange(time(), $value['addtime']);;
      $list[] = $arr;
    }
    foreach ($list3 as $key => $value) {
      $arr = [];
      $arr['type'] = 'resume_refresh';
      $arr['resume_url'] = url('index/resume/show', ['id' => $value['id']]);
      $arr['fullname'] = $value['fullname'];
      if ($value['display_name'] == 0) {
        if ($value['sex'] == 1) {
          $arr['fullname'] = cut_str(
            $value['fullname'],
            1,
            0,
            '先生'
          );
        } elseif ($value['sex'] == 2) {
          $arr['fullname'] = cut_str(
            $value['fullname'],
            1,
            0,
            '女士'
          );
        } else {
          $arr['fullname'] = cut_str(
            $value['fullname'],
            1,
            0,
            '**'
          );
        }
      }
      $arr['time'] = $value['refreshtime'];
      $arr['time_cn'] = daterange(time(), $value['refreshtime']);;
      $list[] = $arr;
    }
    $sortArr = array_column($list, 'time');
    array_multisort($sortArr, SORT_DESC, $list);
    $list = array_slice($list, 0, 15);
    return $list;
  }

  /** 职位福利 */
  protected function getTagList() {
    $list = [];
    $data = model('Category')->getCache('QS_jobtag');
    $counter = 0;
    foreach ($data as $key => $value) {
      if ($counter == 12) {
        break;
      }
      $list[] = ['id' => $key, 'name' => $value];
      $counter++;
    }
    return $list;
  }

  /** 今日更新 */
  protected function getNewTodayList() {
    $subsiteCondition = get_subsite_condition('a');
    $companyList = model('Company')->alias('a')
      ->join(config('database.prefix') . 'job_search_rtime b', 'a.uid=b.uid', 'LEFT')
      ->where($subsiteCondition)
      ->where('a.is_display', 1)
      ->where('b.id', 'not null')
      ->order('a.refreshtime desc')
      ->limit(9)
      ->distinct('a.id')
      ->column('a.id,a.companyname,a.audit,a.setmeal_id,a.refreshtime', 'a.id');

    $company_id_arr = $setmeal_id_arr = $setmeal_list = [];
    foreach ($companyList as $key => $value) {
      $company_id_arr[] = $value['id'];
      $setmeal_id_arr[] = $value['setmeal_id'];
    }
    if (!empty($setmeal_id_arr)) {
      $setmeal_list = model('Setmeal')
        ->where('id', 'in', $setmeal_id_arr)
        ->column('id,icon,name', 'id');
    }
    $company_job_arr = [];
    if (!empty($company_id_arr)) {
      $jobAll = model('Job')->whereIn('company_id', $company_id_arr)->where('audit', 1)->where('is_display', 1)->order('refreshtime desc')->column('id,jobname,company_id');
      foreach ($jobAll as $key => $value) {
        // if(isset($company_job_arr[$value['company_id']]) && count($company_job_arr[$value['company_id']])>=2){
        // continue;
        // }
        $company_job_arr[$value['company_id']][] = $value;
      }
    }
    $list = [];
    foreach ($companyList as $key => $value) {
      $arr = [];
      $arr['id'] = $value['id'];
      $arr['companyname'] = $value['companyname'];
      $arr['audit'] = $value['audit'];
      if (isset($setmeal_list[$value['setmeal_id']])) {
        $arr['setmeal_icon'] =
          $setmeal_list[$value['setmeal_id']]['icon'] > 0
          ? model('Uploadfile')->getFileUrl(
            $setmeal_list[$value['setmeal_id']]['icon']
          )
          : model('Setmeal')->getSysIcon($value['setmeal_id']);
      } else {
        $arr['setmeal_icon'] = '';
      }
      $arr['joblist'] = isset($company_job_arr[$value['id']]) ? $company_job_arr[$value['id']] : [];
      $list[] = $arr;
    }
    return $list;
  }

  protected function getEmergencyList($limit = 5) {
    $subsiteCondition = get_subsite_condition('a');
    $list = model('JobSearchRtime')->alias('a')
      ->join(config('database.prefix') . 'job b', 'a.id=b.id', 'LEFT')
      ->join(config('database.prefix') . 'company c', 'a.uid=c.uid', 'LEFT')
      ->where($subsiteCondition)
      ->where('a.emergency', 1)
      ->where('c.id', 'not null')
      ->order('a.refreshtime desc')
      ->limit($limit)
      ->column('b.id,b.emergency,b.jobname,b.negotiable,b.minwage,b.maxwage,b.company_id,c.companyname');
    foreach ($list as $key => $value) {
      $arr = $value;
      $arr['wage_text'] = model('BaseModel')->handle_wage(
        $arr['minwage'],
        $arr['maxwage'],
        $arr['negotiable']
      );
      $list[$key] = $arr;
    }
    return $list;
  }

  /** 热门关键字 */
  protected function getHotwordList($limit = 15) {
    $list = model('Hotword')->getList($limit);
    return $list;
  }

  /** 优选职位 */
  protected function getFamousList($limit = 15) {
    $famous_enterprises_setmeal = config(
      'global_config.famous_enterprises'
    );
    $famous_enterprises_setmeal =
      $famous_enterprises_setmeal == ''
      ? []
      : explode(',', $famous_enterprises_setmeal);
    $list = [];
    if (!empty($famous_enterprises_setmeal)) {
      $subsiteCondition = get_subsite_condition('a');
      $list = model('JobSearchRtime')
        ->alias('a')
        ->join(
          config('database.prefix') . 'job b',
          'a.id=b.id',
          'LEFT'
        )
        ->join(
          config('database.prefix') . 'company c',
          'a.uid=c.uid',
          'LEFT'
        )
        ->join(
          config('database.prefix') . 'setmeal d',
          'a.setmeal_id=d.id',
          'LEFT'
        )
        ->where($subsiteCondition)
        ->where('c.id', 'not null')
        ->where('a.setmeal_id', 'in', $famous_enterprises_setmeal)
        ->order('a.refreshtime desc')
        ->limit($limit)
        ->column('b.id,b.addtime,b.jobname,b.refreshtime,b.district,b.education,b.experience,b.negotiable,b.minwage,b.maxwage,b.tag,b.setmeal_id,b.company_id,c.companyname,c.audit as company_audit,d.icon');
      $comid_arr = $companyList = $icon_id_arr = $icon_arr = [];
      foreach ($list as $key => $value) {
        $comid_arr[] = $value['id'];
        $value['icon'] > 0 && ($icon_id_arr[] = $value['icon']);
      }
      if (!empty($icon_id_arr)) {
        $icon_arr = model('Uploadfile')->getFileUrlBatch(
          $icon_id_arr
        );
      }
      $category_data = model('Category')->getCache();
      $category_district_data = model('CategoryDistrict')->getCache();
      foreach ($list as $key => $value) {
        $arr = $value;
        if ($arr['district']) {
          $arr['district_text'] = isset(
            $category_district_data[$arr['district']]
          )
            ? $category_district_data[$arr['district']]
            : '';
        } else {
          $arr['district_text'] = '';
        }
        $arr['wage_text'] = model('BaseModel')->handle_wage(
          $arr['minwage'],
          $arr['maxwage'],
          $arr['negotiable']
        );

        $arr['education_text'] = isset(
          model('BaseModel')->map_education[$arr['education']]
        )
          ? model('BaseModel')->map_education[$arr['education']]
          : '学历不限';
        $arr['experience_text'] = isset(
          model('BaseModel')->map_experience[$arr['experience']]
        )
          ? model('BaseModel')->map_experience[$arr['experience']]
          : '经验不限';
        $arr['refreshtime'] = daterange_format(
          $arr['addtime'],
          $arr['refreshtime']
        );
        $arr['tag_arr'] = [];
        if ($arr['tag']) {
          $counter = 0;
          $tag_arr = explode(',', $arr['tag']);
          foreach ($tag_arr as $k => $v) {
            if ($counter >= 4) {
              break;
            }
            $counter++;
            if (
              is_numeric($v) &&
              isset($category_data['QS_jobtag'][$v])
            ) {
              $arr['tag_arr'][] = $category_data['QS_jobtag'][$v];
            } else {
              $arr['tag_arr'][] = $v;
            }
          }
        } else {
          $arr['tag_arr'] = [];
        }
        $arr['setmeal_icon'] = isset($icon_arr[$arr['icon']]) ? $icon_arr[$arr['icon']] : model('Setmeal')->getSysIcon($arr['setmeal_id']);
        $list[$key] = $arr;
      }
    }
    return $list;
  }

  /** 热门职位 */
  protected function getHotjobList() {
    $subsiteCondition = get_subsite_condition('a');
    $list = model('JobSearchRtime')->alias('a')
      ->join(config('database.prefix') . 'job b', 'a.id=b.id', 'LEFT')
      ->join(config('database.prefix') . 'company c', 'a.uid=c.uid', 'LEFT')
      ->where($subsiteCondition)
      ->where('c.id', 'not null')
      ->order('b.click desc,a.refreshtime desc')
      ->limit(10)
      ->column('b.id,b.jobname,b.district,b.negotiable,b.minwage,b.maxwage,b.company_id,c.companyname');
    $category_district_data = model('CategoryDistrict')->getCache();
    foreach ($list as $key => $value) {
      $arr = $value;
      $arr['jobname'] = cut_str($arr['jobname'], 16, 0, '...');
      $arr['wage_text'] = model('BaseModel')->handle_wage(
        $arr['minwage'],
        $arr['maxwage'],
        $arr['negotiable']
      );
      if ($arr['district']) {
        $arr['district_text'] = isset(
          $category_district_data[$arr['district']]
        )
          ? $category_district_data[$arr['district']]
          : '';
      } else {
        $arr['district_text'] = '';
      }
      $list[$key] = $arr;
    }
    return $list;
  }

  /** 企业主页 */
  protected function getCompanyList() {
    $subsiteCondition = get_subsite_condition();
    $list = model('Company')->where('district1', 'gt', 0)->where('is_display', 1)->where($subsiteCondition)->order('refreshtime desc')->limit(9)->column('id,logo,companyname');
    $logo_arr = $logo_id_arr = [];
    foreach ($list as $key => $value) {
      $value['logo'] > 0 && ($logo_id_arr[] = $value['logo']);
    }
    if (!empty($logo_id_arr)) {
      $logo_arr = model('Uploadfile')->getFileUrlBatch($logo_id_arr);
    }
    foreach ($list as $key => $value) {
      $arr = $value;
      $arr['logo_src'] = isset($logo_arr[$arr['logo']])
        ? $logo_arr[$arr['logo']]
        : default_empty('logo');
      $list[$key] = $arr;
    }
    return $list;
  }

  /** 优选人才 */
  protected function getResumeList($limit = 7) {

    $list = model('ResumeSearchRtime')->alias('a')
      ->join(config('database.prefix') . 'resume b', 'a.id=b.id', 'LEFT');
    if ($this->visitor !== null && $this->visitor['utype'] == 1) {
      $shield_find = model('Shield')
        ->where('company_uid', $this->visitor['uid'])
        ->find();
      if ($shield_find !== null) {
        $list = $list->join(config('database.prefix') . 'shield c', 'a.uid=c.personal_uid', 'LEFT')
          ->where('c.company_uid<>' . $this->visitor['uid'] . ' OR c.id is NULL');
      }
    }
    $subsiteCondition = get_subsite_condition('d');
    if (!empty($subsiteCondition)) {
      $list = $list->join(config('database.prefix') . 'resume_intention d', 'a.id=d.rid', 'LEFT')->where($subsiteCondition);
    }
    $list = $list->order('a.refreshtime desc')
      ->limit($limit)
      ->column('b.id,b.stick,b.high_quality,b.service_tag,b.photo_img,b.fullname,b.display_name,b.sex,b.birthday,b.education,b.enter_job_time');
    $resume_id_arr = $photo_arr = $photo_id_arr = [];
    foreach ($list as $key => $value) {
      $resume_id_arr[] = $value['id'];
      $value['photo_img'] > 0 && ($photo_id_arr[] = $value['photo_img']);
    }
    if (!empty($resume_id_arr)) {
      if (!empty($photo_id_arr)) {
        $photo_arr = model('Uploadfile')->getFileUrlBatch(
          $photo_id_arr
        );
      }
      $category_job_data = model('CategoryJob')->getCache();
      $category_district_data = model('CategoryDistrict')->getCache();
      $intention_data = model('ResumeIntention')
        ->where('rid', 'in', $resume_id_arr)
        ->order('id asc')
        ->select();
      $intention_arr = [];
      foreach ($intention_data as $key => $value) {
        $intention_arr[$value['rid']][] = $value;
      }
      foreach ($list as $key => $value) {
        $arr = $value;
        if ($arr['display_name'] == 0) {
          if ($arr['sex'] == 1) {
            $arr['fullname'] = cut_str(
              $arr['fullname'],
              1,
              0,
              '先生'
            );
          } elseif ($arr['sex'] == 2) {
            $arr['fullname'] = cut_str(
              $arr['fullname'],
              1,
              0,
              '女士'
            );
          } else {
            $arr['fullname'] = cut_str(
              $arr['fullname'],
              1,
              0,
              '**'
            );
          }
        }
        $arr['photo_img_src'] = isset($photo_arr[$arr['photo_img']])
          ? $photo_arr[$arr['photo_img']]
          : default_empty('photo');
        $arr['sex_text'] = model('Resume')->map_sex[$arr['sex']];
        $arr['age_text'] = date('Y') - intval($arr['birthday']);
        $arr['education_text'] = isset(
          model('BaseModel')->map_education[$arr['education']]
        )
          ? model('BaseModel')->map_education[$arr['education']]
          : '';

        $arr['experience_text'] =
          $arr['enter_job_time'] == 0
          ? '尚未工作'
          : format_date($arr['enter_job_time']);

        //求职意向
        $district_arr = $category_arr = [];
        if (isset($intention_arr[$arr['id']])) {
          foreach ($intention_arr[$arr['id']] as $k => $v) {
            if ($v['category']) {
              $category_arr[] = isset(
                $category_job_data[$v['category']]
              )
                ? $category_job_data[$v['category']]
                : '';
            }
            if ($v['district']) {
              $district_arr[] = isset(
                $category_district_data[$v['district']]
              )
                ? $category_district_data[$v['district']]
                : '';
            }
          }
        }
        if (!empty($category_arr)) {
          $category_arr = array_unique($category_arr);
          $arr['intention_jobs'] = implode(',', $category_arr);
        } else {
          $arr['intention_jobs'] = '';
        }
        if (!empty($district_arr)) {
          $district_arr = array_unique($district_arr);
          $arr['intention_district'] = implode(
            ',',
            $district_arr
          );
        } else {
          $arr['intention_district'] = '';
        }

        $arr['complete_percent'] = model('Resume')
          ->countCompletePercent($arr['id']);

        $list[$key] = $arr;
      }
    }
    return $list;
  }

  /** 职场资讯 */
  protected function getArticleList($limit = 8) {
    $list = model('Article')
      ->alias('a')
      ->join(config('database.prefix') . 'article_category b', 'a.cid=b.id', 'LEFT')
      ->where('a.is_display', 1)
      ->limit($limit)
      ->order('a.sort_id desc,a.id desc')
      ->column('a.id,a.title,a.link_url,a.addtime,a.cid,b.name as cname');
    foreach ($list as $key => $value) {
      $arr = $value;
      $arr['link_url'] = $arr['link_url'] == '' ? url('index/article/show', ['id' => $arr['id']]) : $arr['link_url'];
      $list[$key] = $arr;
    }
    return $list;
  }

  /** hr工具箱 */
  protected function getHrtoolList($limit = 8) {
    $list = model('Hrtool')->orderRaw('rand()')->limit($limit)->column('id,cid,filename');
    return $list;
  }

  /** 友情链接 */
  protected function getFlinkList() {
    $list = model('Link')->order('sort_id desc')->where('is_display', 1)->column('id,name,link_url');
    return $list;
  }

  /** 广告 */
  protected function getBannerList() {
    $alias_arr = [
      'QS_tpl4_a1@web',
      'QS_index_a4@web',
      'QS_index_a6@web',
      'QS_index_a10@web',
      'QS_tpl4_article@web',
      'QS_index_a12@web'
    ];
    $category_arr = model('AdCategory')->whereIn('alias', $alias_arr)->column('id,alias,ad_num', 'id');
    $cid_arr = [];
    foreach ($category_arr as $key => $value) {
      $cid_arr[] = $value['id'];
    }
    $timestamp = time();
    $dataset = model('Ad')
      ->where('is_display', 1)
      ->whereIn('cid', $cid_arr)
      ->where('starttime', '<=', $timestamp)
      ->where(function ($query) use ($timestamp) {
        $query->where('deadline', '>=', $timestamp)->whereOr('deadline', 0);
      })
      ->order('sort_id desc,id desc')
      ->column('id,cid,title,imageid,imageurl,target,link_url,inner_link,inner_link_params,company_id');
    $image_id_arr = $image_arr = [];
    foreach ($dataset as $key => $value) {
      $arr = $value;
      $arr['imageid'] > 0 && ($image_id_arr[] = $arr['imageid']);
      $dataset[$key] = $arr;
    }
    if (!empty($image_id_arr)) {
      $image_arr = model('Uploadfile')->getFileUrlBatch($image_id_arr);
    }
    $list = [];
    $allCompanyId = [];
    foreach ($dataset as $key => $value) {
      $value['image_src'] = isset($image_arr[$value['imageid']]) ? $image_arr[$value['imageid']] : $value['imageurl'];
      if (isset($list[$category_arr[$value['cid']]['alias']]) && count($list[$category_arr[$value['cid']]['alias']]) >= $category_arr[$value['cid']]['ad_num']) {
        continue;
      }
      $arr = [];
      $arr['title'] = $value['title'];
      $arr['image_src'] = $value['image_src'];
      $arr['link_url'] = $value['link_url'];
      $arr['inner_link'] = $value['inner_link'];
      $arr['inner_link_params'] = $value['inner_link_params'];
      $arr['company_id'] = $value['company_id'];
      $arr['companyname'] = '';
      $arr['joblist'] = [];
      $arr['jobnum'] = 0;
      $arr['web_link_url'] = model('Ad')->handlerWebLink($value);
      $list[$category_arr[$value['cid']]['alias']][] = $arr;
      $value['company_id'] && $allCompanyId[] = $value['company_id'];
    }
    foreach ($category_arr as $key => $value) {
      if (!isset($list[$value['alias']])) {
        $list[$value['alias']] = [];
      }
    }
    return $list;
  }

  /** 获取广告位中涉及的所有企业信息 */
  protected function getAllCompany($allCompanyId) {
    $companyData = [];
    if (!empty($allCompanyId)) {
      $companyData = model('Company')->where('is_display', 1)->whereIn('id', $allCompanyId)->column('id,companyname');
    }
    return $companyData;
  }

  /** 获取广告位中涉及的所有职位信息 */
  protected function getAllJob($allCompanyId) {
    $jobData = model('JobSearchRtime')
      ->alias('a')
      ->join(config('database.prefix') . 'job b', 'a.id=b.id', 'LEFT')
      ->whereIn('a.company_id', $allCompanyId)
      ->column('a.id,b.jobname,a.company_id');
    return $jobData;
  }

  /** 处理a2广告 */
  protected function handlerA2($list, $all_company_arr, $all_job_arr) {
    $job_list = [];
    foreach ($all_job_arr as $key => $value) {
      if (isset($job_list[$value['company_id']]) && count($job_list[$value['company_id']]) >= 4) {
        continue;
      }
      $job_list[$value['company_id']][] = $value;
    }
    // var_dump($job_list);die;
    foreach ($list as $key => $value) {
      $list[$key]['companyname'] = isset($all_company_arr[$value['company_id']]) ? $all_company_arr[$value['company_id']] : $value['title'];
      $list[$key]['joblist'] = isset($job_list[$value['company_id']]) ? $job_list[$value['company_id']] : '';
    }
    return $list;
  }

  /** 处理a3广告 */
  protected function handlerA3($list, $all_company_arr, $all_job_arr) {
    $job_list = [];
    foreach ($all_job_arr as $key => $value) {
      $job_list[$value['company_id']][] = $value;
    }
    foreach ($list as $key => $value) {
      $list[$key]['companyname'] = isset($all_company_arr[$value['company_id']]) ? $all_company_arr[$value['company_id']] : $value['title'];
      $list[$key]['jobnum'] = isset($job_list[$value['company_id']]) ? count($job_list[$value['company_id']]) : 0;
    }
    return $list;
  }

  /** 处理a8广告 */
  protected function handlerA8($list, $all_job_arr) {
    $job_list = [];
    foreach ($all_job_arr as $key => $value) {
      if (isset($job_list[$value['company_id']]) && count($job_list[$value['company_id']]) >= 3) {
        continue;
      }
      $job_list[$value['company_id']][] = $value;
    }
    foreach ($list as $key => $value) {
      $list[$key]['joblist'] = isset($job_list[$value['company_id']]) ? $job_list[$value['company_id']] : '';
    }
    return $list;
  }

  /** 处理a9广告 */
  protected function handlerA9($list, $all_job_arr) {
    $job_list = [];
    foreach ($all_job_arr as $key => $value) {
      if (isset($job_list[$value['company_id']]) && count($job_list[$value['company_id']]) >= 3) {
        continue;
      }
      $job_list[$value['company_id']][] = $value;
    }
    foreach ($list as $key => $value) {
      $list[$key]['joblist'] = isset($job_list[$value['company_id']]) ? $job_list[$value['company_id']] : '';
    }
    return $list;
  }

  /** 处理a11广告 */
  protected function handlerA11($list, $all_company_arr) {
    foreach ($list as $key => $value) {
      $list[$key]['companyname'] = isset($all_company_arr[$value['company_id']]) ? $all_company_arr[$value['company_id']] : $value['title'];
    }
    return $list;
  }


  /**
   * @Purpose:
   * 最新职位
   * @Method getNewjobList()
   *
   * @param int $limit
   *
   * @return array|false|string
   *
   * @throws null
   *
   * @link XXXXXXXXXX
   *
   * @author  Mr.yx
   * @version 1.1
   * @since   2022/5/17
   */
  protected function getNewjobList($limit = 10) {
    $subsiteCondition = get_subsite_condition('a');
    $list = model('JobSearchRtime')->alias('a')
      ->join(config('database.prefix') . 'job b', 'a.id=b.id', 'LEFT')
      ->join(config('database.prefix') . 'company c', 'a.uid=c.uid', 'LEFT')
      ->where($subsiteCondition)
      ->where('c.id', 'not null')
      ->order('a.refreshtime desc')
      ->limit($limit)
      ->column('b.id,b.jobname,b.district,b.district1,b.district2,b.district3,b.negotiable,b.minwage,b.maxwage,b.company_id,b.education,b.experience,c.companyname,a.refreshtime');
    $category_district_data = model('CategoryDistrict')->getCache();
    foreach ($list as $key => $value) {
      $arr = $value;
      $arr['jobname'] = cut_str($arr['jobname'], 16, 0, '...');
      $arr['wage_text'] = model('BaseModel')->handle_wage(
        $arr['minwage'],
        $arr['maxwage'],
        $arr['negotiable']
      );
      if ($arr['district']) {
        $arr['district_text'] = isset(
          $category_district_data[$arr['district']]
        )
          ? $category_district_data[$arr['district']]
          : '';
      } else {
        $arr['district_text'] = '';
      }
      if ($arr['district1']) {
        $arr['district_text_full'] = isset(
          $category_district_data[$arr['district1']]
        )
          ? $category_district_data[$arr['district1']]
          : '';
      } else {
        $arr['district_text_full'] = '';
      }

      if ($arr['district_text_full'] != '' && $arr['district2'] > 0) {
        $arr['district_text_full'] .= isset(
          $category_district_data[$arr['district2']]
        )
          ? ' - ' . $category_district_data[$arr['district2']]
          : '';
      }
      if ($arr['district_text_full'] != '' && $arr['district3'] > 0) {
        $arr['district_text_full'] .= isset(
          $category_district_data[$arr['district3']]
        )
          ? ' - ' . $category_district_data[$arr['district3']]
          : '';
      }
      $arr['education_text'] = isset(
        model('BaseModel')->map_education[$arr['education']]
      )
        ? model('BaseModel')->map_education[$arr['education']]
        : '学历不限';
      $arr['experience_text'] = isset(
        model('BaseModel')->map_experience[$arr['experience']]
      )
        ? model('BaseModel')->map_experience[$arr['experience']]
        : '经验不限';
      $arr['refreshtime'] = daterange(time(), $arr['refreshtime']);
      $list[$key] = $arr;
    }
    return $list;
  }

  /**
   * @Purpose:
   * 获取推荐企业
   * @Method getHotCompanylist()
   *
   * @param int $id
   *
   * @return array
   *
   * @throws \think\db\exception\DataNotFoundException
   * @throws \think\db\exception\ModelNotFoundException
   * @throws \think\exception\DbException
   *
   * @link XXXXXXXXXX
   *
   * @author  Mr.yx
   * @version 1.1
   * @since   2022/5/17 0017
   */
  protected function getHotCompanyList($id = 0) {
    $returnlist = [];
    $famous_enterprises_setmeal = config(
      'global_config.famous_enterprises'
    );
    $famous_enterprises_setmeal =
      $famous_enterprises_setmeal == ''
      ? []
      : explode(',', $famous_enterprises_setmeal);
    if (empty($famous_enterprises_setmeal)) {
      return $returnlist;
    }
    $subsiteCondition = get_subsite_condition('c');
    $category_data = model('Category')->getCache();
    $list = model('Company')
      ->alias('c')
      ->join(
        config('database.prefix') . 'member_setmeal s',
        's.uid=c.uid',
        'LEFT'
      )
      ->join(
        config('database.prefix') . 'job_search_rtime d',
        'c.uid=d.uid',
        'LEFT'
      )
      ->where('d.id', 'not null')
      ->where('c.is_display', 1)
      ->where($subsiteCondition)
      ->group('c.id');
    if ($id > 0) {
      $list = $list->where('c.id', 'neq', $id);
    }
    $list = $list->where('s.setmeal_id', 'in', $famous_enterprises_setmeal)
      ->field('c.id,c.logo,c.companyname,c.nature,c.trade')
      ->order('c.click desc,c.refreshtime desc')
      ->limit(9)
      ->select();
    $job_list = $comid_arr = $logo_id_arr = $logo_arr = [];
    foreach ($list as $key => $value) {
      $comid_arr[] = $value['id'];
      $value['logo'] > 0 && ($logo_id_arr[] = $value['logo']);
    }
    if (!empty($logo_id_arr)) {
      $logo_arr = model('Uploadfile')->getFileUrlBatch($logo_id_arr);
    }
    if (!empty($comid_arr)) {
      $job_data = model('Job')
        ->where('company_id', 'in', $comid_arr)
        ->where('is_display', 1)
        ->column('id,company_id,jobname', 'id');
      foreach ($job_data as $key => $value) {
        $job_list[$value['company_id']][] = $value['jobname'];
      }
    }
    foreach ($list as $key => $value) {
      $arr = $value->toArray();
      $arr['logo_src'] = isset($logo_arr[$value['logo']])
        ? $logo_arr[$arr['logo']]
        : default_empty('logo');
      $arr['jobnum'] = isset($job_list[$value['id']])
        ? count($job_list[$arr['id']])
        : 0;

      $endtime = time();
      $starttime = $endtime - 3600 * 24 * 14;
      $apply_data = model('JobApply')
        ->field('id,is_look')
        ->where('comid', $arr['id'])
        ->where('addtime', 'between', [$starttime, $endtime])
        ->select();
      if (!empty($apply_data)) {
        $total = $looked = 0;
        foreach ($apply_data as $apply_key => $apply_value) {
          $apply_value['is_look'] == 1 && $looked++;
          $total++;
        }
        $arr['watch_percent'] = round($looked / $total, 2) * 100 . '%';
      } else {
        $arr['watch_percent'] = '100%';
      }

      $arr['company_nature_text'] = isset(
        $category_data['QS_company_type'][$arr['nature']]
      )
        ? $category_data['QS_company_type'][$arr['nature']]
        : '';

      $arr['company_trade_text'] = isset(
        $category_data['QS_trade'][$arr['trade']]
      )
        ? $category_data['QS_trade'][$arr['trade']]
        : '';

      $returnlist[] = $arr;
    }
    return $returnlist;
  }


  protected function getFamousCompany($limit = 10) {
    $famous_enterprises_setmeal = config(
      'global_config.famous_enterprises'
    );
    $famous_enterprises_setmeal =
      $famous_enterprises_setmeal == ''
      ? []
      : explode(',', $famous_enterprises_setmeal);
    if (empty($famous_enterprises_setmeal)) {
      $this->ajaxReturn(200, '获取数据成功', ['items' => []]);
    }
    $subsiteCondition = get_subsite_condition('a');
    $list = model('Company')
      ->alias('a')
      ->where('a.is_display', 1)
      ->join(
        config('database.prefix') . 'job_search_rtime c',
        'a.uid=c.uid',
        'LEFT'
      )
      ->where('c.id', 'not null')
      ->where('a.setmeal_id', 'in', $famous_enterprises_setmeal)
      ->where($subsiteCondition)
      ->field('distinct a.id,a.logo,a.companyname,a.district')
      ->order('a.refreshtime desc')
      ->limit($limit)
      ->select();
    $job_list = $comid_arr = $logo_id_arr = $logo_arr = [];
    foreach ($list as $key => $value) {
      $comid_arr[] = $value['id'];
      $value['logo'] > 0 && ($logo_id_arr[] = $value['logo']);
    }
    if (!empty($logo_id_arr)) {
      $logo_arr = model('Uploadfile')->getFileUrlBatch($logo_id_arr);
    }
    if (!empty($comid_arr)) {
      $job_data = model('Job')
        ->where('company_id', 'in', $comid_arr)
        ->where('is_display', 1)
        ->where('audit', 1)
        ->column('id,company_id,jobname', 'id');
      foreach ($job_data as $key => $value) {
        $job_list[$value['company_id']][] = $value['jobname'];
      }
    }
    $return = [];
    $category_district_data = model('CategoryDistrict')->getCache();
    foreach ($list as $key => $value) {
      $arr = $value->toArray();
      $arr['logo'] = isset($logo_arr[$value['logo']])
        ? $logo_arr[$arr['logo']]
        : default_empty('logo');
      $arr['jobnum'] = isset($job_list[$value['id']])
        ? count($job_list[$arr['id']])
        : 0;
      if (isset($job_list[$value['id']]) && !empty($job_list[$value['id']])) {
        $arr['job_text'] = implode($job_list[$value['id']], ' | ');
      }
      if ($arr['district']) {
        $arr['district_text'] = isset(
          $category_district_data[$arr['district']]
        )
          ? $category_district_data[$arr['district']]
          : '';
      } else {
        $arr['district_text'] = '';
      }
      $return[] = $arr;
    }

    return $return;
  }


  /**
   * @Purpose:
   * 获取新闻资讯分类
   * @Method getArticleCategory()
   *
   * @param int $limit
   *
   * @return array
   *
   * @throws null
   *
   * @link XXXXXXXXXX
   *
   * @author  Mr.yx
   * @version 1.1
   * @since   2022/5/20
   */
  protected function getArticleCategory($limit = 6) {
    $catrgory = model('ArticleCategory')->getCache();
    $options = [];
    $counter = 1;
    foreach ($catrgory as $key => $value) {
      if ($counter <= $limit) {
        $options[$key] = $value;
      } else {
        break;
      }
      $counter++;
    }

    return $options;
  }


  /**
   * @Purpose:
   * 通过分类获取文章
   * @Method getArticleListByCategory()
   *
   * @param array $category
   *
   * @return array
   *
   * @throws null
   *
   * @link XXXXXXXXXX
   *
   * @author  Mr.yx
   * @version 1.1
   * @since   2022/5/20 0020
   */
  protected function getArticleListByCategory($category = []) {
    if (0 >= count($category) || !is_array($category)) {
      return [];
    }

    $article_list = [];
    foreach ($category as $cid => $cname) {
      $list = model('Article')
        ->alias('a')
        ->join(config('database.prefix') . 'article_category b', 'a.cid=b.id', 'LEFT')
        ->where('a.is_display', 1)
        ->where('a.cid', $cid)
        ->limit(12)
        ->order('a.sort_id desc,a.id desc')
        ->column('a.id,a.title,a.link_url,a.addtime,a.cid,b.name as cname');
      foreach ($list as $key => $value) {
        $arr = $value;
        $arr['link_url'] = $arr['link_url'] == '' ? url('index/article/show', ['id' => $arr['id']]) : $arr['link_url'];
        $list[$key] = $arr;
      }
      $article_list[$cid] = $list;
    }

    return $article_list;
  }

  protected function getJobfairList($limit = 3) {
    $s_result = $e_result = $list = [];
    $s_limit = $e_limit = '';
    $s_order = 'holddate_start asc,ordid desc,id desc';
    $e_order = 'holddate_start desc,ordid desc,id desc';
    $time = time();
    $s_where = ['holddate_start' => ['gt', $time], 'display' => 1];
    $e_where = ['holddate_start' => ['elt', $time], 'display' => 1];
    $s_count = model('Jobfair')->where($s_where)->count();
    $e_count = model('Jobfair')->where($e_where)->count();
    $firstRow = 0;

    if ($firstRow > $s_count) {
      $e_count && $e_limit = intval($firstRow) - intval($s_count) . ',' . $limit;
    } else {
      $s_count && $s_limit = $firstRow . ',' . $limit;
      if ($e_count && 0 < $e_limit = $firstRow + $limit - $s_count) {
        $e_limit = '0,' . $e_limit;
      } else {
        $e_limit = 0;
      }
    }

    $s_limit && $s_result = model('Jobfair')
      ->where($s_where)
      ->order($s_order)
      ->limit($s_limit)
      ->select();
    $e_limit && $e_result = model('Jobfair')
      ->where($e_where)
      ->order($e_order)
      ->limit($e_limit)
      ->select();
    return array_merge($s_result, $e_result);
  }
}
