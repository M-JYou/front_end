<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/4/6
 * Time: 17:18
 */

namespace app\v1_0\controller\home;


use app\common\model\BaseModel;
use app\common\model\FreelanceOrder;
use app\common\model\FreelanceResume;
use app\common\model\FreelanceSearchResume;
use app\common\model\FreelanceSearchSubject;
use app\common\model\FreelanceSkill;
use app\common\model\FreelanceSkillType;
use app\common\model\FreelanceSubject;
use app\common\model\FreelanceVisitHistory;
use app\common\model\Uploadfile;

class Freelance extends \app\v1_0\controller\common\Base {
  public function _initialize() {
    parent::_initialize();
    if ($this->userinfo) {
      (new FreelanceResume())->refresh($this->userinfo->uid);
    }
  }

  public function skill_list() {
    $list =  (new FreelanceSkillType())->field('id,pid,title')->column('id as value,pid,title as label', 'id');
    foreach ($list as $k => &$v) {
      if (!isset($v['children']) && !$v['pid']) {
        $v['children'] = [];
      }
      if ($v['pid'] > 0) {
        $list[$v['pid']]['children'][] = $v;
        unset($list[$k]);
      }
    }
    $this->ajaxReturn(200, '', array_values($list));
  }

  public function skill_level_map() {
    $this->ajaxReturn(200, '', FreelanceSkill::$levelMap);
  }
  /** 简历详情 */
  public function show_resume() {
    $resumeModel = new FreelanceResume();
    $history = new FreelanceVisitHistory();
    $oModel = new FreelanceOrder();
    $id = input('get.id/d', 0, 'intval');
    $viewFee = intval(config('global_config.freelance_view_resume_fee') * 100);

    $row = $resumeModel->getDetail($id);
    if (!$row) {
      $this->ajaxReturn(400, '简历不存在', $row);
    }

    $show = true;
    if ($viewFee > 0) { //浏览付费
      if ($this->userinfo) {
        if ($row['uid'] != $this->userinfo->uid) {
          $has = $oModel->hasPaid($this->userinfo->uid, $id, FreelanceOrder::TYPE_VIEW_RESUME);
          if (!$has) {
            $show = false;
          }
        }
      } else {
        $show = false;
      }
    }
    $history->record($this->userinfo ? $this->userinfo->uid : 0, $row['uid'], $id, FreelanceVisitHistory::TYPE_RESUME);

    if (!$show) {
      unset($row['mobile'], $row['weixin']);
    }
    $row['show'] = $show;
    $this->ajaxReturn(200, '', $row);
  }

  public function resume_list() {
    $search = new FreelanceSearchResume();
    $resume = new FreelanceResume();
    $key = input('get.key/s', '', 'trim');
    $page = input('get.page/d', 1, 'intval');
    $pageSize = input('get.pageSize/d', 20, 'intval');

    $where = ['audit' => 1, 'is_public' => 1, 'is_published' => 1];
    if ($key) {
      $s = $search->search($key, $page, $pageSize);
      $list = $s['list'];
      $total = $s['total'];
    } else {
      $list = $resume->with('skills.skill,services')->where($where)->limit(($page - 1) * $pageSize, $pageSize)->order('is_top desc,refreshtime desc')->select();
      $total = $resume->where($where)->count();
    }

    $this->ajaxReturn(200, '', ['list' => $resume->processResumeData($list), 'total' => $total]);
  }

  public function subject_promo_list() {
    $list = config('global_config.freelance_promote_subject_sets');
    foreach ($list as $k => &$v) {
      $v['index'] = $k;
      $v['fee'] = number_format($v['fee'], 2);
      $v['tip'] = sprintf('低至%.1f元1天', number_format($v['fee'] / $v['days'], 1));
    }
    $this->ajaxReturn(200, '', $list);
  }

  public function subject_list() {
    $search = new FreelanceSearchSubject();
    $subject = new FreelanceSubject();
    $key = input('get.key/s', '', 'trim');
    $page = input('get.page/d', 1, 'intval');
    $pageSize = input('get.pageSize/d', 20, 'intval');

    $recommend = [];
    // 自由职业 zch 2022.07.06
    $where = ['audit' => 1, 'is_public' => 1, 'is_published' => 1, 'endtime' => ['gt', time()]];
    if (($page == 1) && !$key) {
      $recommend = $subject->where($where + ['is_recommend' => 1])->order('refreshtime desc')->select();
      $recommend = $subject->processData($recommend);
    }

    if ($key) {
      $s = $search->search($key, $page, $pageSize);
      $list = $s['list'];
      $total = $s['total'];
    } else {
      $list = $subject->where($where)->limit(($page - 1) * $pageSize, $pageSize)->order('is_top desc,refreshtime desc')->select();
      $total = $subject->where($where)->count();
    }

    $this->ajaxReturn(200, '', ['recommend' => $recommend, 'list' => $subject->processData($list), 'total' => $total]);
  }

  public function show_subject() {
    $subjectModel = new FreelanceSubject();
    $history = new FreelanceVisitHistory();
    $oModel = new FreelanceOrder();
    $id = input('get.id/d', 0, 'intval');
    $viewFee = intval(config('global_config.freelance_view_subject_fee') * 100);
    $uid = $this->userinfo ? $this->userinfo->uid : 0;

    $row = $subjectModel->find($id);
    if (!$row) {
      $this->ajaxReturn(400, '非法参数');
    }
    if (!$row['is_public'] || !$row['is_published'] || ($row['audit'] != 1)) {
      $this->ajaxReturn(400, '项目不存在');
    }

    $row['view_fee'] = $viewFee / 100;
    $row['price'] /= 100;
    $row['endtime'] = date('Y-m-d', $row['endtime']);
    $show = true;
    if ($viewFee > 0) {
      if ($this->userinfo) {
        if ($row['uid'] != $this->userinfo->uid) {
          $has = $oModel->hasPaid($this->userinfo->uid, $id, FreelanceOrder::TYPE_VIEW_SUBJECT);
          if (!$has) {
            $show = false;
          }
        }
      } else {
        $show = false;
      }
    }
    $history->record($this->userinfo ? $this->userinfo->uid : 0, $row['uid'], $id, FreelanceVisitHistory::TYPE_SUBJECT);
    if (!$show) {
      unset($row['mobile'], $row['weixin']);
    }

    $row['show'] = $show;
    $this->ajaxReturn(200, '', $row);
  }
}
