<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/4/6
 * Time: 11:58
 */

namespace app\v1_0\controller\member;


use app\common\model\BaseModel;
use app\common\model\FreelanceOrder;
use app\common\model\FreelanceProject;
use app\common\model\FreelanceResume;
use app\common\model\FreelanceSearchResume;
use app\common\model\FreelanceSearchSubject;
use app\common\model\FreelanceService;
use app\common\model\FreelanceSkill;
use app\common\model\FreelanceSkillType;
use app\common\model\FreelanceSubject;
use app\common\model\FreelanceSubjectAuditLog;
use app\common\model\FreelanceVisitHistory;
use app\common\model\FreelanceWorks;
use app\common\model\Uploadfile;
use Think\Db;
use think\Loader;

class Freelance extends \app\v1_0\controller\common\Base {
    public function _initialize() {
        parent::_initialize();
        $this->checkLogin();
    }

    public function subject_list() {
        $subject = new FreelanceSubject();
        $no_top =  input('get.no_top/d', 0, 'intval');
        $where = ['uid' => $this->userinfo->uid, 'is_published' => 1];
        $page = input('get.page/d', 1, 'intval');
        $pageSize = input('get.pageSize/d', 100, 'intval');
        if ($no_top) {
            $where['is_top'] = 0;
        }

        $this->ajaxReturn(200, '', $subject->getList2($where, $page, $pageSize));
    }


    /** 保存简历 */
    public function save_resume() {
        $resumeModel = new FreelanceResume();
        $data = [
            'age'                   =>      input('post.age/d', 0, 'intval'),
            'avatar'                =>      input('post.avatar/d', 0, 'intval'),
            'gender'                =>      input('post.gender/d', 0, 'intval'),
            'education'             =>      input('post.education/d', 0, 'intval'),
            'brief_intro'           =>      input('post.brief_intro/s', '', 'trim,badword_filter'),
            'professional_title'    =>      input('post.professional_title/s', '', 'trim,badword_filter'),
            'start_work_date'       =>      input('post.start_work_date/s', '', 'trim'),
            'living_city'           =>      input('post.living_city/s', '', 'trim'),
            'mobile'                =>      input('post.mobile/d', 0, 'intval'),
            'weixin'                =>      input('post.weixin/s', '', 'trim'),
            'name'                  =>      input('post.name/s', '', 'trim'),
            'hide_name'             =>      input('post.hide_name/d', 0, 'intval'),
            'refreshtime'           =>      time(),
        ];
        $validate = new \app\common\validate\FreelanceResume();
        if (!$validate->check($data)) {
            $this->ajaxReturn(500, $validate->getError(), $data);
        }
        $publish_resume_fee = intval(config('global_config.freelance_publish_resume_fee') * 100);

        $search = new FreelanceSearchResume();
        $old = $resumeModel->where(['uid' => $this->userinfo->uid])->find();
        if ($old && $old['is_published']) {
            $edited_resume_audit = intval(config('global_config.freelance_edited_resume_audit'));
            if ($edited_resume_audit >= 0) {
                $data['audit'] = $edited_resume_audit;
                $search->updateSearch($data + ['id' => $old['id'], 'is_published' => $old['is_published'], 'is_public' => $old['is_public']]);
            }
            $r = $resumeModel->save($data, ['id' => $old['id'], 'uid' => $this->userinfo->uid]);
            $data['id'] = $old['id'];
            $this->writeMemberActionLog($this->userinfo->uid, '编辑自由职业简历');
        } else if ($old && !$old['is_published']) {
            $data['is_published'] = $publish_resume_fee > 0 ? 0 : 1;
            $data['is_public'] = 1;
            $data['audit'] = intval(config('global_config.freelance_new_resume_audit'));
            $r = $resumeModel->save($data, ['id' => $old['id']]);
            if (!$publish_resume_fee && ($data['audit'] == 1)) {
                $r1 = $search->updateSearch($data);
            }
            $data['id'] = $old['id'];
            $this->writeMemberActionLog($this->userinfo->uid, '编辑自由职业简历');
        } else {
            $data['uid'] = $this->userinfo->uid;
            $data['is_published'] = $publish_resume_fee > 0 ? 0 : 1;
            $data['is_public'] = 1;
            $data['audit'] = intval(config('global_config.freelance_new_resume_audit'));
            $r = $resumeModel->save($data, false, true);
            $data['id'] = $resumeModel->getLastInsID();
            if (!$publish_resume_fee && ($data['audit'] == 1)) {
                $r1 = $search->updateSearch($data);
            }
            $this->writeMemberActionLog($this->userinfo->uid, '创建自由职业简历');
        }

        $this->ajaxReturn($r !== false ? 200 : 500, $r !== false ? '保存成功' : '保存失败', $data);
    }

    public function pre_save_resume() {
        $resumeModel = new FreelanceResume();
        $eduMap = (new BaseModel)->map_education;
        $info = $resumeModel->with('skills.skill,services,projects,works')->where(['uid' => $this->userinfo->uid])->find();
        $publish_resume_fee = intval(config('global_config.freelance_publish_resume_fee') * 100) / 100;
        if (empty($info)) {
            $info = [
                'need_pay_money' => $publish_resume_fee
            ];
            $info['basic_complete'] = false;
            $skills = (new FreelanceSkill)->with('skill')->where('uid', $this->userinfo->uid)->select();
            $info['skills'] = $skills ? $resumeModel->processSkills($skills) : [];
            $info['skill_complete'] = !!$skills;
            $service = (new FreelanceService())->where('uid', $this->userinfo->uid)->select();
            $info['service_complete'] = !!$service;
            $info['services'] = $service ? $resumeModel->processServices($service) : [];
            $project = (new FreelanceProject())->where(['uid' => $this->userinfo->uid])->select();
            $info['project_complete'] = !!$project;
            $info['projects'] = $project ? $resumeModel->processProjects($project) : [];
            $works = (new FreelanceWorks())->where(['uid' => $this->userinfo->uid])->select();
            $info['work_complete'] = !!$works;
            $info['works'] = $works ? $resumeModel->processWorks($works) : [];
            $info['avatar_img'] = default_empty('photo');
        } else {
            if (intval($info['start_work_date']) == 0) {
                $info['exp_str'] = '尚未工作';
            } else {
                $info['exp_str'] =  format_date(strtotime($info['start_work_date']));
            }
            $info['basic_complete'] = true;
            $info['service_complete'] = !empty($info['services']);
            $info['project_complete'] = !empty($info['projects']);
            $info['skill_complete'] = !empty($info['skills']);
            $info['work_complete'] = !empty($info['works']);
            $info['need_pay_money'] = $info['is_published'] ? 0 : $publish_resume_fee;
            $info['education_str'] = isset($eduMap[$info['education']]) ? $eduMap[$info['education']] : 0;
            $info['skills'] =  $resumeModel->processSkills($info['skills']);
            $info['works'] = $resumeModel->processWorks($info['works']);
            $info['services'] = $resumeModel->processServices($info['services']);
            $info['projects'] = $resumeModel->processProjects($info['projects']);
            $info['avatar_img'] =  (new Uploadfile())->getFileUrl($info['avatar']) ?: default_empty('photo');
        }

        $this->ajaxReturn(200, 'ok', $info);
    }

    public function set_resume_public() {
        $model = new FreelanceResume();
        $is_public = input('post.is_public/d', 0, 'intval');

        try {
            $model->setPublic($this->userinfo->uid, $is_public);
        } catch (\Exception $e) {
            $this->ajaxReturn(500, $e->getMessage());
        }
        $this->writeMemberActionLog($this->userinfo->uid, sprintf('设置简历可见性-【%s】', $is_public ? '可见' : '不可见'));
        $this->ajaxReturn(200, '设置成功');
    }

    public function set_subject_public() {
        $model = new FreelanceSubject();
        $is_public = input('post.is_public/d', 0, 'intval');
        $subject_id = input('post.subject_id/d', 0, 'intval');

        try {
            if ($subject_id) {
                $row = $model->find($subject_id);
                if (!$row || ($row['uid'] != $this->userinfo->uid)) {
                    $this->ajaxReturn(500, '非法参数');
                }
                $model->setPublic($row->toArray(), $is_public);
            }
        } catch (\Exception $e) {
            $this->ajaxReturn(500, $e->getMessage());
        }
        $this->writeMemberActionLog($this->userinfo->uid, sprintf('设置自由职业项目可见性-【项目%d,%s】', $subject_id, ($is_public ? '可见' : '不可见')));
        $this->ajaxReturn(200, '设置成功');
    }

    public function del_subject() {
        $model = new FreelanceSubject();
        $subject_id = input('post.subject_id/d', 0, 'intval');

        try {
            if ($subject_id) {
                $row = $model->find($subject_id);
                if ($row['uid'] != $this->userinfo->uid) {
                    $this->ajaxReturn(500, '非法参数');
                }
                $model->del($subject_id);
            }
        } catch (\Exception $e) {
            $this->ajaxReturn(500, $e->getMessage());
        }
        $this->writeMemberActionLog($this->userinfo->uid, sprintf('删除自由职业项目【%d】', $subject_id));
        $this->ajaxReturn(200, '删除成功');
    }
    /** 保存技能 */
    public function save_skill() {
        $model = new FreelanceSkill();
        $skill_id = input('post.skill_id/d', 0, 'intval');
        $level = input('post.level/d', 0, 'intval');
        $custom_name = input('post.custom_name/s', '', 'trim,badword_filter');
        $id = input('post.id/d', 0, 'intval');
        $resume = new FreelanceResume();

        if (!$skill_id && !$custom_name) {
            $this->ajaxReturn(500, '请选择技能');
        }

        $msg = '保存成功';
        try {
            $r = $model->saveSkill($id, $skill_id, $level, $custom_name, $this->userinfo->uid);
            $old = (new FreelanceResume())->getValidOne($this->userinfo->uid);
            if ($old) {
                $resume->editAudit($old);
            }
            $this->writeMemberActionLog($this->userinfo->uid, sprintf('保存自由职业技能'));
        } catch (\Exception $e) {
            $r = false;
            $msg = $e->getMessage();
        }

        $this->ajaxReturn($r !== false ? 200 : 500, $msg, $r);
    }

    public function get_item() {
        $id = input('get.id/d', 0, 'intval');
        $type = input('get.type/d', 1, 'intval');
        if ($type == 1) {
            $model = new FreelanceSkill();
            $row = $model->where(['id' => $id, 'uid' => $this->userinfo->uid])->find();
            if ($row['skill_id']) {
                $row1 = (new FreelanceSkillType())->where(['id' => $row['skill_id']])->find();
                $row['pid'] = $row1['pid'];
                $row['title'] = $row1['title'];
            }
            if (isset($row['pid'])) {
                $p = (new FreelanceSkillType())->where(['id' => $row['pid']])->find();
                if ($p) {
                    $row['p_title'] = $p['title'];
                }
            }
        } else if ($type == 2) {
            $model = new FreelanceProject();
            $row = $model->where(['id' => $id, 'uid' => $this->userinfo->uid])->find();
            if ($row) {
                $row['startdate'] = date('Y-m-d', $row['startdate']);
                $row['enddate'] = $row['enddate'] ? date('Y-m-d', $row['enddate']) : $row['enddate'];
            }
        } else if ($type == 3) {
            $model = new FreelanceService();
            $row = $model->where(['id' => $id, 'uid' => $this->userinfo->uid])->find();
            if ($row) {
                $row['price'] /= 100;
            }
        } else {
            $model = new FreelanceWorks();
            $row = $model->where(['id' => $id, 'uid' => $this->userinfo->uid])->find();
            if ($row) {
                $row['img_str'] = (new Uploadfile())->getFileUrl($row['img']);
            }
        }

        return $this->ajaxReturn(200, '', $row);
    }
    public function del_item() {
        $id = input('post.id/d', 0, 'intval');
        $type = input('post.type/d', 1, 'intval');
        if ($type == 1) {
            $typeName = '技能';
            $model = new FreelanceSkill();
        } else if ($type == 2) {
            $typeName = '项目';
            $model = new FreelanceProject();
        } else if ($type == 3) {
            $typeName = '服务';
            $model = new FreelanceService();
        } else {
            $typeName = '作品';
            $model = new FreelanceWorks();
        }
        $r = $model->where(['id' => $id, 'uid' => $this->userinfo->uid])->delete();

        $this->writeMemberActionLog($this->userinfo->uid, sprintf('删除自由职业简历数据【%s:%d】', $typeName, $id));
        return $this->ajaxReturn(200, '删除成功', $r);
    }
    /** 保存服务 */
    public function save_service() {
        $model = new FreelanceService();
        $price = input('post.price/d', 0, 'intval');
        $title = input('post.title/s', '', 'trim,badword_filter');
        $id = input('post.id/d', 0, 'intval');
        $resume = new FreelanceResume();

        $msg = '保存成功';
        try {
            $r = $model->saveService($id, $title, $price, $this->userinfo->uid);
            $old = (new FreelanceResume())->getValidOne($this->userinfo->uid);
            if ($old) {
                $resume->editAudit($old);
            }
            $this->writeMemberActionLog($this->userinfo->uid, sprintf('编辑自由职业服务【%s】', $title));
        } catch (\Exception $e) {
            $r = false;
            $msg = $e->getMessage();
        }
        $this->ajaxReturn($r !== false ? 200 : 500, $msg, $r);
    }

    /** 保存项目 */
    public function save_project() {
        $model = new FreelanceProject();
        $startdate = input('post.startdate/s', '', 'trim');
        $enddate = input('post.enddate/s', '', 'trim');
        $name = input('post.name/s', '', 'trim,badword_filter');
        $description = input('post.description/s', '', 'trim,badword_filter');
        $id = input('post.id/d', 0, 'intval');

        $msg = '保存成功';
        try {
            $r = $model->saveService($id, $name, $description, $startdate, $enddate, $this->userinfo->uid);
            $resume = new FreelanceResume();
            $old = (new FreelanceResume())->getValidOne($this->userinfo->uid);
            if ($old) {
                $resume->editAudit($old);
            }
            $this->writeMemberActionLog($this->userinfo->uid, sprintf('编辑自由职业简历项目【%s】', $name));
        } catch (\Exception $e) {
            $r = false;
            $msg = $e->getMessage();
        }
        $this->ajaxReturn($r !== false ? 200 : 500, $msg, $r);
    }

    /** 保存作品 */
    public function save_works() {
        $model = new FreelanceWorks();
        $img = input('post.img/d', 0, 'intval');
        $title = input('post.title/s', '', 'trim,badword_filter');
        $id = input('post.id/d', 0, 'intval');

        $msg = '保存成功';
        try {
            $r = $model->saveWorks($id, $title, $img, $this->userinfo->uid);
            $resume = new FreelanceResume();
            $old = (new FreelanceResume())->getValidOne($this->userinfo->uid);
            if ($old) {
                $resume->editAudit($old);
            }
            $this->writeMemberActionLog($this->userinfo->uid, sprintf('编辑自由职业作品【%s】', $title));
        } catch (\Exception $e) {
            $r = false;
            $msg = $e->getMessage();
        }
        $this->ajaxReturn($r !== false ? 200 : 500, $msg, $r);
    }

    /** 我的足迹 */
    public function history() {
        $type = input('get.type/d', 1, 'intval');
        $page = input('get.page/d', 1, 'intval');
        $pageSize = input('get.pageSize/d', 20, 'intval');

        $history = new FreelanceVisitHistory();
        $res = $history->getList($this->userinfo->uid, $type, $page, $pageSize);
        $this->ajaxReturn(200, '', $res);
    }

    public function save_subject() {
        $model = new FreelanceSubject();
        $search = new FreelanceSearchSubject();
        $id = input('post.id/d', 0, 'intval');

        $data = [
            'title' => input('post.title/s', '', 'trim,badword_filter'),
            'price' => input('post.price/s', 0, 'trim'),
            'endtime' => input('post.endtime/s', '', 'trim'),
            'period' => input('post.period/d', 0, 'intval'),
            'desc' => input('post.desc/s', '', 'trim,badword_filter'),
            'linkman' => input('post.linkman/s', '', 'trim,badword_filter'),
            'mobile' => input('post.mobile/s', '', 'trim'),
            'weixin' => input('post.weixin/s', '', 'trim'),
        ];
        $data['endtime'] = strtotime($data['endtime']);
        $data['price'] = intval($data['price'] * 100);
        $publish_subject_fee = intval(config('global_config.freelance_publish_subject_fee') * 100);
        $validate = new \app\common\validate\FreelanceSubject();
        if (!$validate->check($data)) {
            $this->ajaxReturn(500, $validate->getError(), $data);
        }

        if ($id) {
            $edited_subject_audit = intval(config('global_config.freelance_edited_subject_audit'));
            $old = $model->find($id);
            if ($old['is_published']) {
                $data['is_published'] = 1;
            } else {
                $data['is_published'] = $publish_subject_fee > 0 ? 0 : 1;
            }
            if ($edited_subject_audit >= 0) {
                $data['audit'] = $edited_subject_audit;
                $search->updateSearch($data + ['is_public' => $old['is_public'], 'id' => $id]);
            }
            $r = $model->save($data, ['id' => $id, 'uid' => $this->userinfo->uid]);
            $this->writeMemberActionLog($this->userinfo->uid, sprintf('编辑自由职业项目【%s】', $data['title']));
        } else {
            $data['uid'] = $this->userinfo->uid;
            $data['audit'] = intval(config('global_config.freelance_new_subject_audit'));
            $data['is_published'] = $publish_subject_fee > 0 ? 0 : 1;
            $data['refreshtime'] = time();
            $data['is_public'] = 1;
            $r = $model->save($data);
            $data['id'] = $model->getLastInsID();
            if (!$publish_subject_fee && ($data['audit'] == 1)) {
                $r1 = $search->updateSearch($data);
            }
            $this->writeMemberActionLog($this->userinfo->uid, sprintf('添加自由职业项目【%s】', $data['title']));
        }
        $this->ajaxReturn($r !== false ? 200 : 500, $r !== false ? '保存成功' : '保存失败', $data);
    }

    public function pre_save_subject() {
        $model = new FreelanceSubject();
        $id = input('get.id/d', 0, 'intval');
        $publish_subject_fee = intval(config('global_config.freelance_publish_subject_fee') * 100) / 100;
        $info = [];
        if ($id) {
            $info = $model->where(['id' => $id, 'uid' => $this->userinfo->uid])->find();
        }
        if (empty($info)) {
            $info = [
                'need_pay_money' => $publish_subject_fee
            ];
        } else {
            $info['endtime'] = date('Y-m-d', $info['endtime']);
            $info['price'] /= 100;
            $info['need_pay_money'] = $info['is_published'] ? 0 : $publish_subject_fee;
        }
        $this->ajaxReturn(200, 'ok', $info);
    }

    public function order_pay() {
        $oModel = new FreelanceOrder();
        $id = input('post.id/d', 0, 'intval');
        $pay_type = input('post.pay_type/s', '', 'trim');
        $redirect = input('post.redirect_url/s', '', 'trim');
        $openId =  input('post.openid/s', '', 'trim');
        $order_type = input('post.order_type/d', 1, 'intval');

        switch ($order_type) {
            case FreelanceOrder::TYPE_PUBLISH_RESUME:
                $title = '发布简历';
                $desc = '发布简历';
                $fee = intval(config('global_config.freelance_publish_resume_fee') * 100);
                break;
            case FreelanceOrder::TYPE_PUBLISH_SUBJECT:
                $title = '发布项目';
                $one = (new FreelanceSubject())->find($id);
                $desc = sprintf('发布项目-[%s]', $one['title']);
                $fee = intval(config('global_config.freelance_publish_subject_fee') * 100);
                break;
            case FreelanceOrder::TYPE_VIEW_RESUME:
                $title = '查看简历';
                $desc = '查看简历';
                $fee = intval(config('global_config.freelance_view_resume_fee') * 100);
                break;
            case FreelanceOrder::TYPE_VIEW_SUBJECT:
                $title = '查看项目';
                $one = (new FreelanceSubject())->find($id);
                $desc = sprintf('查看项目-[%s]', $one['title']);
                $fee = intval(config('global_config.freelance_view_subject_fee') * 100);
                break;
            case FreelanceOrder::TYPE_REFRESH_SUBJECT:
                $title = '刷新项目';
                $one = (new FreelanceSubject())->find($id);
                $desc = sprintf('刷新项目-[%s]', $one['title']);
                $fee = intval(config('global_config.freelance_refresh_subject_fee') * 100);
                if (!$fee) {
                    (new FreelanceSubject())->refresh($id);
                    $this->ajaxReturn(200, '刷新成功');
                }
                break;
            default:
                return $this->ajaxReturn(500, '参数错误');
        }
        if (!$fee) {
            $this->ajaxReturn(200, ['pay_status' => 1]);
        }
        $r = (new FreelanceOrder())->newOrder($this->userinfo->uid, $this->userinfo->mobile, $order_type, $fee, $id, $title, $desc);
        if (!$r) {
            $this->ajaxReturn(200, '下单失败');
        }
        $msg = '';
        try {
            $res = $oModel->callPay($r, ['redirect_url' => $redirect, 'openid' => $openId, 'pay_type' => $pay_type]);
            if ($res === false) {
                return false;
            }
            $return  = [];
            $return['pay_status'] = 0;
            $return['parameter'] = $res;
            $return['order_amount'] = $r['amount'] / 100;
            $return['order_oid'] = $r['order_sn'];
            $res = $return;
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            $res = false;
        }
        $this->ajaxReturn($res ? 200 : 500, $msg, $res);
    }
    public function order_service() {
        $oModel = new FreelanceOrder();
        $index = input('post.index/d', 1, 'intval');
        $subject_id = input('post.subject_id/d', 1, 'intval');
        $pay_type = input('post.pay_type/s', '', 'trim');
        $redirect = input('post.redirect_url/s', '', 'trim');
        $services = config('global_config.freelance_promote_subject_sets');
        $openId =  input('post.openid/s', '', 'trim');

        $service = $services[$index];
        if (!$service || !$subject_id || !in_array($pay_type, ['wxpay', 'alipay'])) {
            $this->ajaxReturn(200, '参数有误');
        }
        $fee = intval($service['fee'] * 100);
        $r = $oModel->newOrder($this->userinfo->uid, $this->userinfo->mobile, FreelanceOrder::TYPE_SERVICE, $fee, $subject_id, '购买服务', sprintf('购买服务-[%s]', $service['name']), $service['days']);
        if (!$r) {
            $this->ajaxReturn(200, '下单失败');
        }

        $return = [];
        try {
            $res = $oModel->callPay($r,   ['redirect_url' => $redirect, 'openid' => $openId,  'pay_type' => $pay_type]);
            $return['pay_status'] = 0;
            $return['parameter'] = $res;
            $return['order_amount'] = $r['amount'] / 100;
            $return['order_oid'] = $r['order_sn'];
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            $res = false;
        }

        $this->ajaxReturn($res ? 200 : 500, '', $return);
    }
}
