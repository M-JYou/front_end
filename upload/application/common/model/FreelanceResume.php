<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/4/6
 * Time: 11:41
 */

namespace app\common\model;


use Think\Model;

class FreelanceResume extends Model {
    protected $autoWriteTimestamp = true;
    protected $createTime = 'addtime';
    protected $updateTime = 'updatetime';
    protected $readonly = ['id', 'uid'];

    public $map_audit = [
        0 => '未审核',
        2 => '审核未通过',
        1 => '已审核'
    ];

    public function projects() {
        return $this->hasMany('FreelanceProject', 'uid', 'uid')->field('id,uid,name,startdate,enddate,description');
    }
    public function services() {
        return  $this->hasMany('FreelanceService',  'uid', 'uid')->field('id,uid,title,price');
    }
    public function skills() {
        return $this->hasMany('FreelanceSkill', 'uid', 'uid')->field('id,uid,skill_id,level,custom_name');
    }
    public function works() {
        return $this->hasMany('FreelanceWorks', 'uid', 'uid')->field('id,uid,title,img');
    }

    public function latestResume($size) {
        $list = $this->with('services')->where(['audit' => 1, 'is_public' => 1, 'is_published' => 1])->order('refreshtime desc')->limit($size)->select();
        return $this->processResumeData($list);
    }

    public function getValidOne($uid) {
        return $this->where(['uid' => $uid, 'is_published' => 1])->find();
    }

    public function setPublic($uid, $isPublic) {
        $isPublic = intval($isPublic) ? 1 : 0;
        $resume = $this->where(['uid' => $uid, 'is_published' => 1])->find();
        if (!$resume) return 0;
        $this->save(['is_public' => $isPublic], ['id' => $resume['id']]);
        $info = $resume->toArray();
        $info['is_public'] = $isPublic;
        return (new FreelanceSearchResume())->updateSearch($info);
    }

    public function getHotList() {
        $cacheName = 'freelance_hot_resume';
        $res = cache($cacheName);
        if (!$res) {
            $list = $this->with('services')->where(['audit' => 1, 'is_public' => 1, 'is_published' => 1])->order('view_times desc')->limit(10)->select();
            $res =  $this->processResumeData($list);
            cache($cacheName, $res, 600);
        }
        return $res;
    }

    public function delAll($ids) {
        $models = [];
        $models[] = new FreelanceProject();
        $models[]  = new FreelanceService();
        $models[]  = new FreelanceSkill();
        $models[]  = new FreelanceWorks();
        $models2 = new FreelanceSearchResume();
        $uids = $this->where('id', 'in', $ids)->value('uid');
        $this->where('id', 'in', $ids)->delete();
        foreach ($models as $model) {
            $model->where('uid', 'in', $uids)->delete();
        }
        $models2->where('resume_id', 'in', $ids)->delete();
    }

    /**
     * 仅限于修改非基础资料的接口调用
     * @param $data
     */
    public function editAudit($data) {
        $search = new FreelanceSearchResume();
        $edited_resume_audit = intval(config('global_config.freelance_edited_resume_audit'));
        if ($edited_resume_audit >= 0) {
            $data['audit'] = $edited_resume_audit;
            $search->updateSearch($data);
            $this->save(['audit' => $edited_resume_audit], ['id' => $data['id']]);
        }
    }

    /**
     * 刷新简历
     * @param $uid
     */
    public function refresh($uid) {
        $row = $this->where('uid', $uid)->find();
        if ($row) {
            if ((time() - $row['refreshtime']) > 3600) {
                if ($row['is_published'] && ($row['audit'] == 1) && $row['is_public']) {
                    $this->save(['refreshtime' => time()], ['uid' => $uid]);
                    (new FreelanceSearchResume())->save(['refreshtime' => time()], ['resume_id' => $row['id']]);
                }
            }
        }
    }

    public function getList($audit, $is_public, $key, $type, $page, $pagesize) {
        $where = ['is_published' => 1];
        if ($audit > -1) {
            $where['audit'] = $audit;
        }
        if ($is_public > -1) {
            $where['is_public'] = $is_public;
        }
        if ($key) {
            if ($type == 1) {
                $where['name'] = ['like', '%' . $key . '%'];
            } else if ($type == 2) {
                $where['mobile'] = $key;
            }
        }
        return [
            'list' => $this->where($where)->order('id desc')->limit(($page - 1) * $pagesize, $pagesize)->select(),
            'total' => $this->where($where)->count()
        ];
    }
    public function setAudit($idarr, $audit, $reason = '') {
        $timestamp = time();
        $this->where('id', 'in', $idarr)->setField('audit', $audit);
        $uid_arr = [];
        $audit_log = [];
        $resume_list = $this->where('id', 'in', $idarr)->column('*', 'id');
        $search = new FreelanceSearchResume();
        foreach ($resume_list as $key => $value) {
            $uid_arr[] = $value['uid'];
            $arr['uid'] = $value['uid'];
            $arr['resumeid'] = $key;
            $arr['audit'] = $audit;
            $arr['reason'] = $reason;
            $arr['addtime'] = $timestamp;
            $audit_log[] = $arr;
            $value['audit'] = $audit;
            $search->updateSearch($value);
        }
        $logModel = new ResumeAuditLog();
        $logModel->saveAll($audit_log);
        return;
    }

    public function getDetail($id) {
        $viewFee = intval(config('global_config.freelance_view_resume_fee') * 100);
        $eduMap = (new BaseModel)->map_education;

        $row = $this->where(['is_public' => 1, 'is_published' => 1, 'audit' => 1, 'id' => $id])
            ->with('projects,services,skills.skill,works')->find();
        if (!$row) return false;
        $row['view_fee'] = $viewFee / 100;

        if (!empty($row['skills'])) {
            $arr = [];
            foreach ($row['skills'] as $s) {
                $s['level_str'] = FreelanceSkill::$levelMap[$s['level']];
                $arr[] = $s;
            }
            $row['skills'] = $arr;
        }
        if (intval($row['hide_name'])) {
            if ($row['gender'] == 1) {
                $row['name'] = cut_str(
                    $row['name'],
                    1,
                    0,
                    '先生'
                );
            } elseif ($row['gender'] == 2) {
                $row['name'] = cut_str(
                    $row['name'],
                    1,
                    0,
                    '女士'
                );
            } else {
                $row['name'] = cut_str(
                    $row['name'],
                    1,
                    0,
                    '**'
                );
            }
        }
        if (intval($row['start_work_date']) == 0) {
            $row['exp_str'] = '尚未工作';
        } else {
            $row['exp_str'] =  format_date(strtotime($row['start_work_date']));
        }
        $row['gender_str'] = $row['gender'] == 1 ? '男' : '女';
        $row['skills'] = $this->processSkills($row['skills']);
        $row['works'] = $this->processWorks($row['works']);
        $row['services'] = $this->processServices($row['services']);
        $row['projects'] = $this->processProjects($row['projects']);
        $row['education_str'] = $eduMap[$row['education']];
        $row['refreshtime'] = daterange(time(),  $row['refreshtime']);
        $row['avatar_img'] =  (new Uploadfile())->getFileUrl($row['avatar']) ?: default_empty('photo');
        return $row;
    }

    public function processResumeData(&$resumeList) {
        $eduMap = (new BaseModel)->map_education;
        $arr = [];
        $avatars = [];
        if (!$resumeList) return [];
        foreach ($resumeList as $v) {
            if (intval($v['hide_name'])) {
                if ($v['gender'] == 1) {
                    $v['name'] = cut_str(
                        $v['name'],
                        1,
                        0,
                        '先生'
                    );
                } elseif ($v['gender'] == 2) {
                    $v['name'] = cut_str(
                        $v['name'],
                        1,
                        0,
                        '女士'
                    );
                } else {
                    $v['name'] = cut_str(
                        $v['name'],
                        1,
                        0,
                        '**'
                    );
                }
            }
            if ($v['avatar'] > 0) {
                $avatars[] = $v['avatar'];
            }
            if (intval($v['start_work_date']) == 0) {
                $exp_str = '尚未工作';
            } else {
                $exp_str =  format_date(strtotime($v['start_work_date']));
            }
            $arr[] = [
                'exp_str' => $exp_str,
                'gender' => $v['gender'],
                'gender_str' => $v['gender'] == 1 ? '男' : '女',
                'resume_id' => $v['id'],
                'name' => $v['name'],
                'avatar' => $v['avatar'],
                'refreshtime' => daterange(time(), $v['refreshtime']),
                'age' => $v['age'],
                'professional_title' => $v['professional_title'],
                'education' => $eduMap[$v['education']],
                'is_top' => $v['is_top'],
                'skills' => $this->processSkills($v['skills']),
                'services' => $this->processServices($v['services']),
                'avatar_img' => default_empty('photo'),
            ];
        }
        if (!empty($avatars)) {
            $img_src_data = model('Uploadfile')->getFileUrlBatch($avatars);
            foreach ($arr as &$av) {
                $av['avatar_img'] = isset($img_src_data[$av['avatar']]) ?  $img_src_data[$av['avatar']] : default_empty('photo');
            }
        }

        return $arr;
    }
    public function processWorks($works) {
        $arr = [];
        if (!empty($works)) {
            $imgIds = [];
            $image_arr = [];
            foreach ($works as $w) {
                if ($w['img'] > 0) $imgIds[] = $w['img'];
            }
            if (!empty($imgIds)) {
                $image_arr = model('Uploadfile')->getFileUrlBatch($imgIds);
            }
            foreach ($works as $w) {
                $w['img_str'] = isset($image_arr[$w['img']]) ? $image_arr[$w['img']] : '';
                $arr[] = $w;
            }
        }
        return $arr;
    }
    public function processServices($services) {
        $arr = [];
        if (!empty($services)) {
            foreach ($services as $w) {
                $w['price'] = $w['price'] / 100; //价格保存的是分,输出的是元
                $arr[] = $w;
            }
        }
        return $arr;
    }
    public function processProjects($projects) {
        $arr = [];
        if (!empty($projects)) {
            foreach ($projects as $w) {
                $w['startdate'] =  date('Y-m-d', $w['startdate']);
                $w['enddate'] =  $w['enddate'] ? date('Y-m-d', $w['enddate']) : '至今';
                $arr[] = $w;
            }
        }
        return $arr;
    }
    public function processSkills($skills) {
        $arr = [];
        if (!empty($skills)) {
            foreach ($skills as $w) {
                $w['level_str'] = FreelanceSkill::$levelMap[$w['level']];
                $arr[] = $w;
            }
        }
        return $arr;
    }
}
