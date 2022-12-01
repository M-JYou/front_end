<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/5/6
 * Time: 14:46
 */

namespace app\v1_0\controller\home;

use app\common\lib\Pager;
use app\common\model\FastResume;
use app\common\model\FastJob;

class Fast extends \app\v1_0\controller\common\Base {
  public function fast_resume_addsave() {
    $fastResumeModel = new FastResume();
    $id = input('post.id/d', 0, 'intval');
    if ($id) {
      $info = $this->getResumeDetail($id);
      if ($info === false) {
        $this->ajaxReturn(500, '简历信息为空');
      }
    }
    $input_data = [
      'fullname' => input('post.fullname/s', '', 'trim,badword_filter'),
      'sex' => input('post.sex/d', 0, 'intval'),
      'experience' => input('post.experience/d', 0, 'intval'),
      'wantjob' => input('post.wantjob/s', '', 'trim,badword_filter'),
      'telephone' => input('post.telephone/s', '', 'trim,badword_filter'),
      'code' => input('post.code/s', '', 'trim'),
      'content' => input('post.content/s', '', 'trim,badword_filter'),
      'valid' => input('post.valid/d', 0, 'intval'),
      'adminpwd' => input('post.adminpwd/s', '', 'trim,badword_filter')
    ];
    if ($id) {
      $input_data['adminpwd'] = md5($input_data['adminpwd']);
      $input_data['refreshtime'] = time();
      if (intval(config('global_config.fast_resume_edit_audit') > 0)) {
        $input_data['audit'] = 0;
      }
      if ($input_data['valid']) {
        if ($info['endtime']) {
          $input_data['endtime'] = $info['endtime'] + $input_data['valid'] * 60 * 60 * 24;
        } else {
          $input_data['endtime'] = $info['refreshtime'] + $input_data['valid'] * 60 * 60 * 24;
        }
      } else {
        $input_data['endtime'] = 0;
      }
      unset($input_data['code']);
      $r = $fastResumeModel->where(array('id' => $id))->update($input_data);
    } else {
      $auth_result = cache('smscode_' . $input_data['telephone']);
      if (
        $auth_result === false ||
        $auth_result['code'] != $input_data['code'] ||
        $auth_result['mobile'] != $input_data['telephone']
      ) {
        \think\Cache::inc('smscode_error_num_' . $input_data['telephone']);
        $this->ajaxReturn(500, '验证码错误');
      }
      $error_num = \think\Cache::get(
        'smscode_error_num_' . $input_data['telephone']
      );
      if ($error_num !== false && $error_num >= 5) {
        $this->ajaxReturn(500, '验证码失效，请重新获取');
      }

      $input_data['adminpwd'] = md5($input_data['adminpwd']);
      $input_data['addtime'] = $input_data['refreshtime'] = time();
      $input_data['audit'] = intval(config('global_config.fast_resume_add_audit'));
      if ($input_data['valid']) {
        $input_data['endtime'] = $input_data['addtime'] + $input_data['valid'] * 60 * 60 * 24;
      } else {
        $input_data['endtime'] = 0;
      }
      $input_data['is_top'] = 0;
      $input_data['is_recommend'] = 0;
      $validate = new \app\common\validate\FastResume();
      if (!$validate->check($input_data)) {
        $this->ajaxReturn(500, $validate->getError(), $input_data);
      }
      $time1 = strtotime(date('Y-m-d', time()));
      $time2 = strtotime(date('Y-m-d', time())) + 24 * 60 * 60;
      $today_where['telephone'] = $input_data['telephone'];
      $today_where['addtime'] = array('between', $time1 . ',' . $time2);
      $today_pub_num = $fastResumeModel->where($today_where)->count();
      if ($today_pub_num >= intval(config('global_config.fast_resume_num'))) {
        $this->ajaxReturn(500, '今天快速求职发布次数已用完，请明天再发！');
      }
      $r = $fastResumeModel->allowField(true)->save($input_data);
    }

    $input_data['id'] = $fastResumeModel->getLastInsID();
    $this->ajaxReturn($r !== false ? 200 : 500, '发布成功', $input_data);
  }
  public function fast_job_addsave() {
    $fastJobModel = new FastJob();
    $id = input('post.id/d', 0, 'intval');
    if ($id) {
      $info = $this->getJobDetail($id);
      if ($info === false) {
        $this->ajaxReturn(500, '职位信息为空');
      }
    }
    $input_data = [
      'jobname' => input('post.jobname/s', '', 'trim,badword_filter'),
      'comname' => input('post.comname/s', '', 'trim,badword_filter'),
      'contact' => input('post.contact/s', '', 'trim,badword_filter'),
      'telephone' => input('post.telephone/s', '', 'trim,badword_filter'),
      'code' => input('post.code/s', '', 'trim'),
      'address' => input('post.address/s', '', 'trim,badword_filter'),
      'content' => input('post.content/s', '', 'trim,badword_filter'),
      'valid' => input('post.valid/d', 0, 'intval'),
      'adminpwd' => input('post.adminpwd/s', '', 'trim,badword_filter')
    ];
    if ($id) {
      $input_data['adminpwd'] = md5($input_data['adminpwd']);
      $input_data['refreshtime'] = time();
      if (intval(config('global_config.fast_job_edit_audit') > 0)) {
        $input_data['audit'] = 0;
      }
      if ($input_data['valid']) {
        if ($info['endtime']) {
          $input_data['endtime'] = $info['endtime'] + $input_data['valid'] * 60 * 60 * 24;
        } else {
          $input_data['endtime'] = $info['refreshtime'] + $input_data['valid'] * 60 * 60 * 24;
        }
      } else {
        $input_data['endtime'] = 0;
      }
      $r = $fastJobModel->where(array('id' => $id))->update($input_data);
    } else {
      $auth_result = cache('smscode_' . $input_data['telephone']);
      if (
        $auth_result === false ||
        $auth_result['code'] != $input_data['code'] ||
        $auth_result['mobile'] != $input_data['telephone']
      ) {
        \think\Cache::inc('smscode_error_num_' . $input_data['telephone']);
        $this->ajaxReturn(500, '验证码错误');
      }
      $error_num = \think\Cache::get(
        'smscode_error_num_' . $input_data['telephone']
      );
      if ($error_num !== false && $error_num >= 5) {
        $this->ajaxReturn(500, '验证码失效，请重新获取');
      }
      $input_data['adminpwd'] = md5($input_data['adminpwd']);
      $input_data['addtime'] = $input_data['refreshtime'] = time();
      $input_data['audit'] = intval(config('global_config.fast_resume_add_audit'));
      if ($input_data['valid']) {
        $input_data['endtime'] = $input_data['addtime'] + $input_data['valid'] * 60 * 60 * 24;
      } else {
        $input_data['endtime'] = 0;
      }
      $input_data['is_top'] = 0;
      $input_data['is_recommend'] = 0;
      $validate = new \app\common\validate\FastJob();
      if (!$validate->check($input_data)) {
        $this->ajaxReturn(500, $validate->getError(), $input_data);
      }
      $time1 = strtotime(date('Y-m-d', time()));
      $time2 = strtotime(date('Y-m-d', time())) + 24 * 60 * 60;
      $today_where['telephone'] = $input_data['telephone'];
      $today_where['addtime'] = array('between', $time1 . ',' . $time2);
      $today_pub_num = $fastJobModel->where($today_where)->count();
      if ($today_pub_num >= intval(config('global_config.fast_job_num'))) {
        $this->ajaxReturn(500, '今天快速招聘发布次数已用完，请明天再发！');
      }
      $r = $fastJobModel->allowField(true)->save($input_data);
    }

    $input_data['id'] = $fastJobModel->getLastInsID();
    $this->ajaxReturn($r !== false ? 200 : 500, '发布成功', $input_data);
  }
  public function fast_resume_pwd() {
    $fastResumeModel = new FastResume();
    $id = input('post.id/d', 0, 'intval');
    $info = array();
    if ($id) {
      $info = $this->getResumeDetail($id);
      if ($info === false) {
        $this->ajaxReturn(500, '简历信息为空');
      }
    }
    $input_data = [
      'adminpwd' => input('post.adminpwd/s', '', 'trim,badword_filter')
    ];
    if (md5($input_data['adminpwd']) == $info['adminpwd']) {
      $this->ajaxReturn(200, '密码正确', $info);
    } else {
      $this->ajaxReturn(500, '密码错误', $info);
    }
  }
  public function fast_job_pwd() {
    $fastJobModel = new FastJob();
    $id = input('post.id/d', 0, 'intval');
    $info = array();
    if ($id) {
      $info = $this->getJobDetail($id);
      if ($info === false) {
        $this->ajaxReturn(500, '职位信息为空');
      }
    }
    $input_data = [
      'adminpwd' => input('post.adminpwd/s', '', 'trim,badword_filter')
    ];
    if (md5($input_data['adminpwd']) == $info['adminpwd']) {
      $this->ajaxReturn(200, '密码正确', $info);
    } else {
      $this->ajaxReturn(500, '密码错误', $info);
    }
  }
  public function fast_resume_list() {
    $keywords = input('get.keywords/s', '', 'trim');
    $page = input('get.page/d', 1, 'intval');
    $pageSize = input('get.pagesize/d', 20, 'intval');
    $where['audit'] = 1;
    //$where['endtime'] = array('gt',time());
    $timestamp = time();
    if ($keywords) {
      $where['fullname|wantjob|content'] = array('like', '%' . $keywords . '%');
    }
    $fastResumeModel = new fastResume();
    $list = $fastResumeModel
      ->where($where)
      ->where(function ($query) use ($timestamp) {
        $query->where('endtime', '>=', $timestamp)->whereOr('endtime', 0);
      })
      ->order('is_top desc,refreshtime desc')
      ->limit(($page - 1) * $pageSize, $pageSize)
      ->select();
    $fast = new FastResume();
    foreach ($list as $key => $val) {
      $list[$key]['refreshtime_cn'] = daterange(time(), $val['refreshtime']);
      $list[$key]['addtime_cn'] = daterange(time(), $val['addtime']);
      $fast_experience = $fast->experience;
      $list[$key]['experience'] = isset($fast_experience[$val['experience']]) ? $fast_experience[$val['experience']] : '';
    }
    $fast_resume_list['list'] = $list;
    return $this->ajaxReturn(200, '', $fast_resume_list);
  }
  public function fast_job_list() {
    $keywords = input('get.keywords/s', '', 'trim');
    $page = input('get.page/d', 1, 'intval');
    $pageSize = input('get.pagesize/d', 20, 'intval');
    $where['audit'] = 1;
    //$where['endtime'] = array('gt',time());
    $timestamp = time();
    if ($keywords) {
      $where['jobname|comname|content'] = array('like', '%' . $keywords . '%');
    }
    $fastJobModel = new FastJob();
    $list = $fastJobModel
      ->where($where)
      ->where(function ($query) use ($timestamp) {
        $query->where('endtime', '>=', $timestamp)->whereOr('endtime', 0);
      })
      ->order('is_top desc,refreshtime desc')
      ->limit(($page - 1) * $pageSize, $pageSize)
      ->select();
    foreach ($list as $key => $val) {
      $list[$key]['refreshtime_cn'] = daterange(time(), $val['refreshtime']);
      $list[$key]['addtime_cn'] = daterange(time(), $val['addtime']);
    }
    $fast_job_list['list'] = $list;
    return $this->ajaxReturn(200, '', $fast_job_list);
  }
  public function fast_resume_info() {
    $id = input('get.id/d', 0, 'intval');
    $info = $this->getResumeDetail($id);
    $info['content'] = htmlspecialchars_decode(strip_tags($info['content']), ENT_QUOTES);
    if ($info === false) {
      $this->ajaxReturn(500, '简历信息为空');
    }
    $fastResumeModel = new FastResume();
    $fastResumeModel->where(array('id' => $id))->setInc('click');
    $info['telephone'] = substr_replace($info['telephone'], '****', 3, 4);
    $this->ajaxReturn(200, '获取数据成功', $info);
  }
  public function fast_job_info() {
    $id = input('get.id/d', 0, 'intval');
    $info = $this->getJobDetail($id);
    $info['content'] = htmlspecialchars_decode(strip_tags($info['content']), ENT_QUOTES);
    if ($info === false) {
      $this->ajaxReturn(500, '职位信息为空');
    }
    $fastJobModel = new FastJob();
    $fastJobModel->where(array('id' => $id))->setInc('click');
    // 【ID1000228】【bug】后台设置登录后可见，登录后，列表页联系方式可见，弹框详情页不可见
    // $info['telephone'] = substr_replace($info['telephone'], '****', 3, 4);
    $this->ajaxReturn(200, '获取数据成功', $info);
  }
  public function fast_resume_refresh() {
    $fastResumeModel = new FastResume();
    $id = input('get.id/d', 0, 'intval');
    $info = $this->getResumeDetail($id);
    if ($info === false) {
      $this->ajaxReturn(500, '简历信息为空');
    } else {
      $input_data['refreshtime'] = time();
      $fastResumeModel->where(array('id' => $id))->update($input_data);
      $r = $fastResumeModel->getLastInsID();
      $this->ajaxReturn($r !== false ? 200 : 500, '刷新成功', $input_data);
    }
  }
  public function fast_job_refresh() {
    $fastJobModel = new FastJob();
    $id = input('get.id/d', 0, 'intval');
    $info = $this->getJobDetail($id);
    if ($info === false) {
      $this->ajaxReturn(500, '职位信息为空');
    } else {
      $input_data['refreshtime'] = time();
      $fastJobModel->where(array('id' => $id))->update($input_data);
      $r = $fastJobModel->getLastInsID();
      $this->ajaxReturn($r !== false ? 200 : 500, '刷新成功', $input_data);
    }
  }
  public function fast_resume_del() {
    $fastResumeModel = new FastResume();
    $id = input('get.id/d', 0, 'intval');
    $info = $this->getResumeDetail($id);
    if ($info === false) {
      $this->ajaxReturn(500, '简历信息为空');
    } else {
      if (false === $fastResumeModel->where(array('id' => $id))->delete()) {
        $this->ajaxReturn(500, $fastResumeModel->getError());
      } else {
        $this->ajaxReturn(200, '删除成功');
      }
    }
  }
  public function fast_job_del() {
    $fastJobModel = new FastJob();
    $id = input('get.id/d', 0, 'intval');
    $info = $this->getJobDetail($id);
    if ($info === false) {
      $this->ajaxReturn(500, '职位信息为空');
    } else {
      if (false === $fastJobModel->where(array('id' => $id))->delete()) {
        $this->ajaxReturn(500, $fastJobModel->getError());
      } else {
        $this->ajaxReturn(200, '删除成功');
      }
    }
  }
  public function getResumeDetail($id) {
    $id = intval($id);
    $where['id'] = $id;
    $basic = model('FastResume')
      ->where($where)
      ->find();
    if ($basic === null) {
      return false;
    }
    $basic['sex_text'] = isset(model('FastResume')->map_sex[$basic['sex']])
      ? model('FastResume')->map_sex[$basic['sex']]
      : '';
    $basic['experience_text'] = isset(
      model('BaseModel')->map_experience[$basic['experience']]
    )
      ? model('BaseModel')->map_experience[$basic['experience']]
      : '';
    $basic['refreshtime_cn'] = daterange(time(), $basic['refreshtime']);
    $basic['addtime_cn'] = daterange(time(), $basic['addtime']);
    if ($basic['endtime'] == 0) {
      $basic['endtime_cn'] = "长期有效";
    } else {
      $basic['endtime_cn'] = date('Y-m-d H:i', $basic['endtime']);
    }
    return $basic;
  }

  public function getJobDetail($id) {
    $id = intval($id);
    $where['id'] = $id;
    $basic = model('FastJob')
      ->where($where)
      ->find();
    if ($basic === null) {
      return false;
    }
    $basic['refreshtime_cn'] = daterange(time(), $basic['refreshtime']);
    $basic['addtime_cn'] = daterange(time(), $basic['addtime']);
    if ($basic['endtime'] == 0) {
      $basic['endtime_cn'] = "长期有效";
    } else {
      $basic['endtime_cn'] = date('Y-m-d H:i', $basic['endtime']);
    }
    return $basic;
  }
}
