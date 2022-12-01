<?php

namespace app\v1_0\controller\home;

class Campus extends \app\v1_0\controller\common\Base {
  public function _initialize() {
    parent::_initialize();
  }
  /**  校园招聘首页 */
  public function index() {
    $data = [];
    $data['school_list'] = $this->get_school_list();
    $data['notice_list'] = $this->get_notice();
    $data['election_list'] = $this->get_election();
    $data['job_list'] = $this->get_job();
    $return['data'] = $data;
    $this->ajaxReturn(200, '获取数据成功', $return);
  }
  /**  院校列表 */
  public function school_list() {
    $where = [];
    $keyword = input('get.keyword/s', '', 'trim,addslashes');
    $current_page = input('get.page/d', 1, 'intval');
    $pagesize = input('get.pagesize/d', 10, 'intval');
    $level = input('get.level/d', 0, 'intval');
    $type = input('get.type/d', 0, 'intval');
    $district1 = input('get.district1/d', 0, 'intval');
    $district2 = input('get.district2/d', 0, 'intval');
    $district3 = input('get.district3/d', 0, 'intval');
    if ($keyword != '') {
      $where['name'] = ['like', '%' . $keyword . '%'];
    }
    if ($level > 0) {
      $where['level'] = ['eq', $level];
    }
    if ($type > 0) {
      $where['type'] = ['eq', $type];
    }
    if ($district3 > 0) {
      $where['district3'] = ['eq', $district3];
    } elseif ($district2 > 0) {
      $where['district2'] = ['eq', $district2];
    } elseif ($district1 > 0) {
      $where['district1'] = ['eq', $district1];
    }
    $where['display'] = ['eq', 1];
    $list = model('CampusSchool')
      ->where($where)
      ->field('id,name,logo,district1,district2,district3,level,type')
      ->order('addtime desc')
      ->page($current_page, $pagesize)
      ->select();
    $total = model('CampusSchool')
      ->where($where)
      ->count();
    $image_id_arr = $image_list = [];
    foreach ($list as $key => $value) {
      $value['logo'] && ($image_id_arr[] = $value['logo']);
    }
    if (!empty($image_id_arr)) {
      $image_list = model('Uploadfile')->getFileUrlBatch($image_id_arr);
    }
    $preach_count_arr = model('CampusPreach')->group('school_id')->column('school_id,count(*) as num');
    $election_count_arr = model('CampusElection')->group('school_id')->column('school_id,count(*) as num');
    $category_district_data = model('CategoryDistrict')->getCache('all');
    foreach ($list as $key => $value) {
      $value['logo_url'] = isset($image_list[$value['logo']])
        ? $image_list[$value['logo']]
        : '';
      $value['level_cn'] = model('CampusSchool')->map_level[$value['level']];
      $value['type_cn'] = model('CampusSchool')->map_type[$value['type']];
      $district_index =
        $value['district3'] != 0
        ? $value['district3']
        : ($value['district2'] != 0
          ? $value['district2']
          : $value['district1']);
      $value['district_cn'] = isset(
        $category_district_data[$district_index]
      )
        ? $category_district_data[$district_index]
        : '';
      $value['preach_count'] = isset($preach_count_arr[$value['id']]) ? $preach_count_arr[$value['id']] : 0;
      $value['election_count'] = isset($election_count_arr[$value['id']]) ? $election_count_arr[$value['id']] : 0;
      $value['link_url'] = config('global_config.mobile_domain') . 'campus/school/show/' . $value['id'];
      $list[$key] = $value;
    }
    $return['items'] = $list;
    $return['total'] = $total;
    $return['current_page'] = $current_page;
    $return['pagesize'] = $pagesize;
    $return['total_page'] = $total == 0 ? 0 : ceil($total / $pagesize);
    $this->ajaxReturn(200, '获取数据成功', $return);
  }
  /**  院校层次 */
  public function get_level_category() {
    $return = model('CampusSchool')->map_level;
    $this->ajaxReturn(200, '获取数据成功', $return);
  }
  /**  院校类型 */
  public function get_type_category() {
    $return = model('CampusSchool')->map_type;
    $this->ajaxReturn(200, '获取数据成功', $return);
  }
  /**  院校详情 */
  public function school_show() {
    $id = input('get.id/d', 0, 'intval');
    if (!$id) {
      $this->ajaxReturn(500, '请选择院校');
    }
    $info = model('CampusSchool')
      ->field('id,name,logo,district1,district2,district3,level,type,introduction,address,tel')
      ->where('id', $id)
      ->where('display', 1)
      ->find();
    if (null === $info) {
      $this->ajaxReturn(500, '院校不存在或已删除');
    }
    $category_district_data = model('CategoryDistrict')->getCache('all');
    $info['level_cn'] = model('CampusSchool')->map_level[$info['level']];
    $info['type_cn'] = model('CampusSchool')->map_type[$info['type']];
    $district_index =
      $info['district3'] != 0
      ? $info['district3']
      : ($info['district2'] != 0
        ? $info['district2']
        : $info['district1']);
    $info['district_cn'] = isset(
      $category_district_data[$district_index]
    )
      ? $category_district_data[$district_index]
      : '';
    $logo_url = model('Uploadfile')->getFileUrl($info['logo']);
    $info['logo_url'] = isset($logo_url) ? $logo_url : '';
    $info['preach_count'] = model('CampusPreach')->where('school_id', $info['id'])->count();
    $info['election_count'] = model('CampusElection')->where('school_id', $info['id'])->count();
    $info['share_url'] = config('global_config.mobile_domain') . 'campus/school/show/' . $info['id'];
    $return['info'] = $info;
    model('CampusSchool')->where('id', $id)->setInc('click', 1);
    $this->ajaxReturn(200, '获取数据成功', $return);
  }
  /**  ajax获取院校分类 */
  public function get_school() {
    $school_list = model('CampusSchool')->field('id,name')->where('display', 1)->select();
    $this->ajaxReturn(200, '获取数据成功', $school_list);
  }
  /**  双选会列表 */
  public function election_list() {
    $where = [];
    $keyword = input('get.keyword/s', '', 'trim,addslashes');
    $current_page = input('get.page/d', 1, 'intval');
    $pagesize = input('get.pagesize/d', 10, 'intval');
    $school_id = input('get.school_id/d', 0, 'intval');
    $district1 = input('get.district1/d', 0, 'intval');
    $district2 = input('get.district2/d', 0, 'intval');
    $district3 = input('get.district3/d', 0, 'intval');
    $timecase = input('get.timecase/s', '', 'trim');
    if ($keyword != '') {
      $where['a.subject'] = ['like', '%' . $keyword . '%'];
    }
    if ($district3 > 0) {
      $where['b.district3'] = ['eq', $district3];
    } elseif ($district2 > 0) {
      $where['b.district2'] = ['eq', $district2];
    } elseif ($district1 > 0) {
      $where['b.district1'] = ['eq', $district1];
    }
    if ($timecase != '') {
      $timecase_map = model('CampusElection')->timecase_map($timecase);
      if ($timecase_map) {
        $where['a.starttime'] = $timecase_map;
      }
    }
    if ($school_id != '') {
      $where['a.school_id'] = ['eq', $school_id];
    }

    $list = model('CampusElection')
      ->alias('a')
      ->join(config('database.prefix') . 'campus_school b', 'a.school_id=b.id', 'left')
      ->field('a.id,a.school_id,a.subject,a.address,a.starttime,a.endtime,a.company_count,a.graduate_count,b.name school_name')
      ->where($where)
      ->where('a.display', 1)
      ->order('a.addtime desc')
      ->page($current_page, $pagesize)
      ->select();
    $total = model('CampusElection')
      ->alias('a')
      ->join(config('database.prefix') . 'campus_school b', 'a.school_id=b.id', 'left')
      ->where($where)
      ->where('a.display', 1)
      ->count();
    $timestamp = time();
    foreach ($list as $key => $value) {
      if ($value['starttime'] <= $timestamp && $value['endtime'] > $timestamp) {
        $value['score'] = 2; // 进行中
      } elseif ($value['starttime'] > $timestamp) {
        $value['score'] = 1; // 即将开始
      } else {
        $value['score'] = 0; // 已结束
      }
      $value['link_url'] = config('global_config.mobile_domain') . 'campus/election/show/' . $value['id'];
      $list[$key] = $value;
    }
    $return['items'] = $list;
    $return['total'] = $total;
    $return['current_page'] = $current_page;
    $return['pagesize'] = $pagesize;
    $return['total_page'] = $total == 0 ? 0 : ceil($total / $pagesize);
    $this->ajaxReturn(200, '获取数据成功', $return);
  }
  /**  双选会详情 */
  public function election_show() {
    $id = input('get.id/d', 0, 'intval');
    if (!$id) {
      $this->ajaxReturn(500, '请选择双选会');
    }
    $info = model('CampusElection')
      ->field('id,school_id,subject,address,starttime,endtime,introduction,company_count,graduate_count')
      ->where('id', $id)
      ->where('display', 1)
      ->find();
    if (null === $info) {
      $this->ajaxReturn(500, '双选会不存在或已删除');
    }
    $info['school_name'] = model('CampusSchool')->where('id', $info['school_id'])->value('name');
    $info['share_url'] = config('global_config.mobile_domain') . 'campus/election/show/' . $info['id'];

    $info['introduction'] = htmlspecialchars_decode($info['introduction'], ENT_QUOTES);
    $return['info'] = $info;
    model('CampusElection')->where('id', $id)->setInc('click', 1);
    $this->ajaxReturn(200, '获取数据成功', $return);
  }
  /**  宣讲会列表 */
  public function preach_list() {
    $where = [];
    $keyword = input('get.keyword/s', '', 'trim,addslashes');
    $current_page = input('get.page/d', 1, 'intval');
    $pagesize = input('get.pagesize/d', 10, 'intval');
    $school_id = input('get.school_id/d', 0, 'intval');
    $district1 = input('get.district1/d', 0, 'intval');
    $district2 = input('get.district2/d', 0, 'intval');
    $district3 = input('get.district3/d', 0, 'intval');
    $timecase = input('get.timecase', '', 'intval');
    if ($keyword != '') {
      $where['a.subject'] = ['like', '%' . $keyword . '%'];
    }
    if ($district3 > 0) {
      $where['b.district3'] = ['eq', $district3];
    } elseif ($district2 > 0) {
      $where['b.district2'] = ['eq', $district2];
    } elseif ($district1 > 0) {
      $where['b.district1'] = ['eq', $district1];
    }
    if ($timecase != '') {
      $timecase_map = model('CampusElection')->timecase_map($timecase);
      if ($timecase_map) {
        $where['a.starttime'] = $timecase_map;
      }
    }
    if ($school_id != '') {
      $where['a.school_id'] = ['eq', $school_id];
    }

    $list = model('CampusPreach')
      ->alias('a')
      ->join(config('database.prefix') . 'campus_school b', 'a.school_id=b.id', 'left')
      ->field('a.id,a.school_id,a.subject,a.address,a.starttime,b.name school_name,b.logo,b.district1,b.district2,b.district3')
      ->where($where)
      ->where('a.display', 1)
      ->order('a.addtime desc')
      ->page($current_page, $pagesize)
      ->select();
    $total = model('CampusPreach')
      ->alias('a')
      ->join(config('database.prefix') . 'campus_school b', 'a.school_id=b.id', 'left')
      ->where($where)
      ->where('a.display', 1)
      ->count();
    $image_id_arr = $image_list = [];
    foreach ($list as $key => $value) {
      $value['logo'] && ($image_id_arr[] = $value['logo']);
    }
    if (!empty($image_id_arr)) {
      $image_list = model('Uploadfile')->getFileUrlBatch($image_id_arr);
    }
    $category_district_data = model('CategoryDistrict')->getCache('all');
    foreach ($list as $key => $value) {
      $district_index =
        $value['district3'] != 0
        ? $value['district3']
        : ($value['district2'] != 0
          ? $value['district2']
          : $value['district1']);
      $value['district_cn'] = isset(
        $category_district_data[$district_index]
      )
        ? $category_district_data[$district_index]
        : '';
      $value['logo_url'] = isset($image_list[$value['logo']])
        ? $image_list[$value['logo']]
        : '';
      $value['link_url'] = config('global_config.mobile_domain') . 'campus/preach/show/' . $value['id'];
      $list[$key] = $value;
    }
    $return['items'] = $list;
    $return['total'] = $total;
    $return['current_page'] = $current_page;
    $return['pagesize'] = $pagesize;
    $return['total_page'] = $total == 0 ? 0 : ceil($total / $pagesize);
    $this->ajaxReturn(200, '获取数据成功', $return);
  }
  /**  宣讲会详情 */
  public function preach_show() {
    $id = input('get.id/d', 0, 'intval');
    if (!$id) {
      $this->ajaxReturn(500, '请选择宣讲会');
    }
    $info = model('CampusPreach')
      ->field('id,school_id,subject,address,starttime,introduction')
      ->where('id', $id)
      ->where('display', 1)
      ->find();
    if (null === $info) {
      $this->ajaxReturn(500, '宣讲会不存在或已删除');
    }
    $school = model('CampusSchool')->where('id', $info['school_id'])->field('name,tel')->find();
    $info['school_name'] = $school['name'];
    $info['school_tel'] = $school['tel'];
    $info['share_url'] = config('global_config.mobile_domain') . 'campus/preach/show/' . $info['id'];
    $return['info'] = $info;
    model('CampusPreach')->where('id', $id)->setInc('click', 1);
    $this->ajaxReturn(200, '获取数据成功', $return);
  }
  /**  院校双选会 */
  public function school_election() {
    $school_id = input('get.school_id/d', 0, 'intval');
    $current_page = input('get.page/d', 1, 'intval');
    $pagesize = input('get.pagesize/d', 10, 'intval');
    if (!$school_id) {
      $this->ajaxReturn(500, '请选择院校');
    }
    $list = model('CampusElection')
      ->where('school_id', $school_id)
      ->field('id,subject,address,starttime,endtime')
      ->where('display', 1)
      ->order('addtime desc')
      ->page($current_page, $pagesize)
      ->select();
    $total = model('CampusElection')
      ->where('school_id', $school_id)
      ->where('display', 1)
      ->count();
    $timestamp = time();
    foreach ($list as $key => $value) {
      if ($value['starttime'] <= $timestamp && $value['endtime'] > $timestamp) {
        $value['score'] = 2; // 进行中
      } elseif ($value['starttime'] > $timestamp) {
        $value['score'] = 1; // 即将开始
      } else {
        $value['score'] = 0; // 已结束
      }
      $value['starttime'] = date('Y-m-d', $value['starttime']);
      $value['endtime'] = date('Y-m-d', $value['endtime']);
      $list[$key] = $value;
    }
    $return['item'] = $list;
    $return['total'] = $total;
    $return['current_page'] = $current_page;
    $return['pagesize'] = $pagesize;
    $return['total_page'] = $total == 0 ? 0 : ceil($total / $pagesize);
    $this->ajaxReturn(200, '获取数据成功', $return);
  }
  /**  院校宣讲会 */
  public function school_preach() {
    $school_id = input('get.school_id/d', 0, 'intval');
    $current_page = input('get.page/d', 1, 'intval');
    $pagesize = input('get.pagesize/d', 10, 'intval');
    if (!$school_id) {
      $this->ajaxReturn(500, '请选择院校');
    }
    $list = model('CampusPreach')
      ->where('school_id', $school_id)
      ->field('id,subject,address,starttime')
      ->where('display', 1)
      ->order('addtime desc')
      ->page($current_page, $pagesize)
      ->select();
    $total = model('CampusPreach')
      ->where('school_id', $school_id)
      ->where('display', 1)
      ->count();
    foreach ($list as $key => $value) {
      $value['starttime'] = date('Y-m-d', $value['starttime']);
      $list[$key] = $value;
    }
    $return['item'] = $list;
    $return['total'] = $total;
    $return['current_page'] = $current_page;
    $return['pagesize'] = $pagesize;
    $return['total_page'] = $total == 0 ? 0 : ceil($total / $pagesize);
    $this->ajaxReturn(200, '获取数据成功', $return);
  }
  /**  校招职位 */
  public function campus_job() {
    $keyword = input('get.keyword/s', '', 'trim,addslashes');
    $emergency = input('get.emergency/d', 0, 'intval');
    $famous = input('get.famous/d', 0, 'intval');
    $company_id = input('get.company_id/d', 0, 'intval');
    $category1 = input('get.category1/d', 0, 'intval');
    $category2 = input('get.category2/d', 0, 'intval');
    $category3 = input('get.category3/d', 0, 'intval');
    $district1 = input('get.district1/d', 0, 'intval');
    $district2 = input('get.district2/d', 0, 'intval');
    $district3 = input('get.district3/d', 0, 'intval');
    $experience = input('get.experience/d', 0, 'intval');
    $minwage = input('get.minwage/d', 0, 'intval');
    $maxwage = input('get.maxwage/d', 0, 'intval');
    $filter_apply = input('get.filter_apply/d', 0, 'intval');
    $nature = input('get.nature/d', 0, 'intval');
    $education = input('get.education/d', 0, 'intval');
    $tag = input('get.tag/s', '', 'trim');
    $settr = input('get.settr/d', 0, 'intval');
    $lat = input('get.lat/f', 0, 'floatval');
    $lng = input('get.lng/f', 0, 'floatval');
    $range = input('get.range/d', 0, 'intval');
    $south_west_lat = input('get.south_west_lat/f', 0, 'floatval');
    $south_west_lng = input('get.south_west_lng/f', 0, 'floatval');
    $north_east_lat = input('get.north_east_lat/f', 0, 'floatval');
    $north_east_lng = input('get.north_east_lng/f', 0, 'floatval');
    $sort = input('get.sort/s', '', 'trim');
    $current_page = input('get.page/d', 1, 'intval');
    $pagesize = input('get.pagesize/d', 10, 'intval');
    $count_total = input('get.count_total/d', 0, 'intval');

    $params['count_total'] = $count_total;
    $params['current_page'] = $current_page;
    $params['pagesize'] = $pagesize;

    if ($keyword != '') {
      $params['keyword'] = $keyword;
    }
    $distanceData = [];
    if ($lat > 0 && $lng > 0) {
      $params['lat'] = $lat;
      $params['lng'] = $lng;
      if ($range > 0) {
        $params['range'] = $range;
      }
      $distanceData = [
        'current_lat' => $lat,
        'current_lng' => $lng
      ];
    } else {
      if ($district1 > 0) {
        $params['district1'] = $district1;
      }
      if ($district2 > 0) {
        $params['district2'] = $district2;
      }
      if ($district3 > 0) {
        $params['district3'] = $district3;
      }
    }

    if ($company_id > 0) {
      $params['company_id'] = $company_id;
    }
    if ($category1 > 0) {
      $params['category1'] = $category1;
    }
    if ($category2 > 0) {
      $params['category2'] = $category2;
    }
    if ($category3 > 0) {
      $params['category3'] = $category3;
    }
    if ($emergency > 0) {
      $params['emergency'] = $emergency;
    }
    if ($famous > 0) {
      $params['famous'] = $famous;
    }
    if ($minwage > 0) {
      $params['minwage'] = $minwage;
    }
    if ($maxwage > 0) {
      $params['maxwage'] = $maxwage;
    }
    if (
      $filter_apply == 1 &&
      $this->userinfo !== null &&
      $this->userinfo->utype == 2
    ) {
      $params['filter_apply_uid'] = $this->userinfo->uid;
    }
    if ($nature > 0) {
      $params['nature'] = $nature;
    }
    // 教育经历和工作经历为不限
    $params['education'] = -1;
    $params['experience'] = -1;
    if ($tag != '') {
      $tag = str_replace(",", "_", $tag);
      $params['tag'] = $tag;
    }
    if ($settr > 0) {
      $params['settr'] = $settr;
    }
    if ($sort != '') {
      $params['sort'] = $sort;
    }
    if (
      $south_west_lat > 0 &&
      $south_west_lng > 0 &&
      $north_east_lat > 0 &&
      $north_east_lng > 0
    ) {
      $params['south_west_lat'] = $south_west_lat;
      $params['south_west_lng'] = $south_west_lng;
      $params['north_east_lat'] = $north_east_lat;
      $params['north_east_lng'] = $north_east_lng;
    }

    $instance = new \app\common\lib\JobSearchEngine($params);

    $searchResult = $instance->run();
    $return['items'] = $this->get_datalist($searchResult['items']);
    $return['total'] = $searchResult['total'];
    $return['total_page'] = $searchResult['total_page'];
    $this->ajaxReturn(200, '获取数据成功', $return);
  }

  protected function get_datalist($list) {
    $result_data_list = $jobid_arr = $comid_arr = $cominfo_arr = $logo_id_arr = $logo_arr = $icon_id_arr = $icon_arr = [];
    foreach ($list as $key => $value) {
      $jobid_arr[] = $value['id'];
      $comid_arr[] = $value['company_id'];
    }
    if ($jobid_arr) {
      if (!empty($comid_arr)) {
        $cominfo_arr = model('Company')
          ->alias('a')
          ->join(
            config('database.prefix') . 'setmeal b',
            'a.setmeal_id=b.id',
            'LEFT'
          )
          ->where('a.id', 'in', $comid_arr)
          ->column(
            'a.id,a.companyname,a.audit,a.logo,a.nature,a.scale,a.trade,a.setmeal_id,b.icon',
            'a.id'
          );
        foreach ($cominfo_arr as $key => $value) {
          $value['logo'] > 0 && ($logo_id_arr[] = $value['logo']);
          $value['icon'] > 0 && ($icon_id_arr[] = $value['icon']);
        }
        if (!empty($logo_id_arr)) {
          $logo_arr = model('Uploadfile')->getFileUrlBatch(
            $logo_id_arr
          );
        }
        if (!empty($icon_id_arr)) {
          $icon_arr = model('Uploadfile')->getFileUrlBatch(
            $icon_id_arr
          );
        }
      }
      $jids = implode(',', $jobid_arr);
      $field =
        'id,company_id,jobname,minwage,maxwage,negotiable,education,experience,district,addtime,refreshtime';
      $joblist = model('Job')
        ->where('id', 'in', $jids)
        ->orderRaw('field(id,' . $jids . ')')
        ->field($field)
        ->select();
      $category_district_data = model('CategoryDistrict')->getCache();
      foreach ($joblist as $key => $val) {
        $tmp_arr = [];
        $tmp_arr['id'] = $val['id'];
        $tmp_arr['jobname'] = $val['jobname'];
        $tmp_arr['company_id'] = $val['company_id'];
        if (isset($cominfo_arr[$val['company_id']])) {
          $tmp_arr['companyname'] =
            $cominfo_arr[$val['company_id']]['companyname'];
          $tmp_arr['company_logo'] = isset(
            $logo_arr[$cominfo_arr[$val['company_id']]['logo']]
          )
            ? $logo_arr[$cominfo_arr[$val['company_id']]['logo']]
            : default_empty('logo');
        } else {
          $tmp_arr['companyname'] = '';
          $tmp_arr['company_logo'] = '';
        }

        if ($val['district']) {
          $tmp_arr['district_text'] = isset(
            $category_district_data[$val['district']]
          )
            ? $category_district_data[$val['district']]
            : '';
        } else {
          $tmp_arr['district_text'] = '';
        }
        $tmp_arr['wage_text'] = model('BaseModel')->handle_wage(
          $val['minwage'],
          $val['maxwage'],
          $val['negotiable']
        );
        $tmp_arr['refreshtime'] = daterange_format(
          $val['addtime'],
          $val['refreshtime']
        );
        $tmp_arr['job_link_url_web'] = url('campus/job/show', ['id' => $tmp_arr['id']]);
        $result_data_list[] = $tmp_arr;
      }
    }
    return $result_data_list;
  }

  /**  校招资讯 */
  public function campus_notice() {
    $keyword = input('get.keyword/s', '', 'trim,addslashes');
    $current_page = input('get.page/d', 1, 'intval');
    $pagesize = input('get.pagesize/d', 10, 'intval');
    $where = ['is_display' => 1];
    if ($keyword != '') {
      $where['title'] = ['like', '%' . $keyword . '%'];
    }

    $list = model('CampusNotice')
      ->field('id,title,link_url,click,addtime,holddate_start,holddate_end')
      ->where($where)
      ->page($current_page, $pagesize)
      ->order('sort_id desc,id desc')
      ->select();
    $total = model('CampusNotice')
      ->where($where)
      ->count();
    $return['items'] = $list;
    $return['total'] = $total;
    $return['current_page'] = $current_page;
    $return['pagesize'] = $pagesize;
    $return['total_page'] = $total == 0 ? 0 : ceil($total / $pagesize);
    $this->ajaxReturn(200, '获取数据成功', $return);
  }

  /**  校招资讯详情 */
  public function campus_notice_show() {
    $id = input('get.id/d', 0, 'intval');
    if (!$id) {
      $this->ajaxReturn(500, '请选择资讯');
    }
    $info = model('CampusNotice')
      ->field('id,title,content,addtime,holddate_start,holddate_end')
      ->where('id', $id)
      ->find();
    if ($info === null) {
      $this->ajaxReturn(500, '资讯不存在或已被删除！');
    }
    $info['content'] = htmlspecialchars_decode($info['content'], ENT_QUOTES);
    model('CampusNotice')->where('id', $id)->setInc('click', 1);
    $return['info'] = $info;
    $this->ajaxReturn(200, '获取数据成功', $return);
  }

  /**  首页公告列表 */
  protected function get_notice() {
    $list = model('CampusNotice')
      ->field('id,title')
      ->order('addtime desc')
      ->where('is_display', 1)
      ->select();
    return $list;
  }

  /**  首页院校列表 */
  protected function get_school_list() {
    $list = model('CampusSchool')
      ->field('id,name as school_name,logo')
      ->where('display', 1)
      ->order('addtime desc')
      ->select();
    $image_id_arr = $image_list = [];
    foreach ($list as $key => $value) {
      $value['logo'] && ($image_id_arr[] = $value['logo']);
    }
    if (!empty($image_id_arr)) {
      $image_list = model('Uploadfile')->getFileUrlBatch($image_id_arr);
    }
    $preach_count_arr = model('CampusPreach')->group('school_id')->column('school_id,count(*) as num');
    foreach ($list as $key => $value) {
      $value['logo_url'] = isset($image_list[$value['logo']])
        ? $image_list[$value['logo']]
        : '';
      $value['total'] = isset($preach_count_arr[$value['id']]) ? $preach_count_arr[$value['id']] : 0;
      $value['link_url'] = config('global_config.mobile_domain') . 'campus/school/show/' . $value['id'];
      $list[$key] = $value;
    }
    return $list;
  }

  /**  首页双选会列表 */
  protected function get_election() {
    $timestamp = time();
    $field =
      'a.id,a.school_id,a.subject,a.address,CASE 
        WHEN a.starttime<=' .
      $timestamp .
      ' AND a.endtime>' . $timestamp . ' THEN 2
        WHEN a.starttime>' .
      $timestamp .
      ' THEN 1
        ELSE 0
        END AS score,a.starttime,a.endtime,a.company_count,a.graduate_count,b.name school_name';
    $list = model('CampusElection')
      ->alias('a')
      ->join(config('database.prefix') . 'campus_school b', 'a.school_id=b.id', 'left')
      ->field($field)
      ->where('a.display', 1)
      ->order('score desc,a.starttime desc')
      ->select();
    foreach ($list as $key => $value) {
      $value['link_url'] = config('global_config.mobile_domain') . 'campus/election/show/' . $value['id'];
      $list[$key] = $value;
    }
    return $list;
  }

  /**  职位列表 */
  protected function get_job() {
    // 教育经历和工作经历为不限
    $params['education'] = -1;
    $params['experience'] = -1;
    $params['pagesize'] = 5;
    $params['sort'] = 'refreshtime';
    $instance = new \app\common\lib\JobSearchEngine($params);

    $searchResult = $instance->run();
    $list = $this->get_datalist($searchResult['items']);
    return $list;
  }

  /**  广告位 */
  public function ad_list() {
    $alias_arr = input('post.alias/a', []);
    if (empty($alias_arr)) {
      $this->ajaxReturn(500, '请选择广告位');
    }
    $category_arr = model('CampusAdCategory')->whereIn('alias', $alias_arr)->column('id,alias,ad_num', 'id');
    if (!$category_arr) {
      $this->ajaxReturn(500, '没有找到对应的广告位');
    }

    $cid_arr = [];
    foreach ($category_arr as $key => $value) {
      $cid_arr[] = $value['id'];
    }
    if (empty($cid_arr)) {
      $this->ajaxReturn(500, '没有找到对应的广告位');
    }
    $timestamp = time();
    $dataset = model('CampusAd')
      ->field('id,cid,title,imageid,imageurl,target,link_url,inner_link,inner_link_params,company_id')
      ->where('is_display', 1)
      ->whereIn('cid', $cid_arr)
      ->where('starttime', '<=', $timestamp)
      ->where(function ($query) use ($timestamp) {
        $query->where('deadline', '>=', $timestamp)->whereOr('deadline', 0);
      })
      ->order('sort_id desc,id desc')
      ->select();
    $image_id_arr = $image_arr = [];
    $list = [];
    foreach ($dataset as $key => $value) {
      $arr = $value->toArray();
      $arr['imageid'] > 0 && ($image_id_arr[] = $arr['imageid']);
      $list[] = $arr;
    }
    if (!empty($image_id_arr)) {
      $image_arr = model('Uploadfile')->getFileUrlBatch($image_id_arr);
    }

    $return['items'] = [];
    foreach ($list as $key => $value) {
      $value['image_src'] = isset($image_arr[$value['imageid']])
        ? $image_arr[$value['imageid']]
        : $value['imageurl'];
      if (isset($return['items'][$category_arr[$value['cid']]['alias']]) && count($return['items'][$category_arr[$value['cid']]['alias']]) >= $category_arr[$value['cid']]['ad_num']) {
        continue;
      }
      $arr = [];
      $arr['image_src'] = $value['image_src'];
      $arr['link_url'] = $value['link_url'];
      $arr['inner_link'] = $value['inner_link'];
      $arr['inner_link_params'] = $value['inner_link_params'];
      $arr['company_id'] = $value['company_id'];
      $arr['companyname'] = model('Company')->where('id', $value['company_id'])->value('companyname');
      $arr['bottom_text'] = $arr['companyname'] ? $arr['companyname'] : $value['title'];
      $arr['web_link_url'] = model('CampusAd')->handlerWebLink($value);
      $return['items'][$category_arr[$value['cid']]['alias']][] = $arr;
    }
    foreach ($category_arr as $key => $value) {
      if (!isset($return['items'][$value['alias']])) {
        $return['items'][$value['alias']] = [];
      }
    }
    $this->ajaxReturn(200, '获取数据成功', $return);
  }

  public function ajaxSearchLocation() {
    $alias = input('get.alias/s', 'joblist', 'trim');
    $input = [
      'keyword' => input('get.keyword/s', null, 'trim,addslashes')
    ];
    $path = 'index/campus/index';
    if ($alias == 'school') {
      $path = 'index/campus/school';
    } else if ($alias == 'election') {
      $path = 'index/campus/election';
    } else if ($alias == 'preach') {
      $path = 'index/campus/preach';
    } else if ($alias == 'job') {
      $path = 'index/campus/job';
    } else if ($alias == 'notice') {
      $path = 'index/campus/notice';
    }
    $this->ajaxReturn(200, '获取数据成功', url($path, $input));
  }
}
