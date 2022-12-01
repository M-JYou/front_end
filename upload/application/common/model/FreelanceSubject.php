<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/4/6
 * Time: 11:41
 */

namespace app\common\model;


use Think\Db;
use Think\Model;

class FreelanceSubject extends Model {
    protected $autoWriteTimestamp = true;
    protected $createTime = 'addtime';
    protected $updateTime = 'updatetime';
    protected $readonly = ['id', 'uid'];

    public $map_audit = [
        0 => '未审核',
        2 => '审核未通过',
        1 => '已审核'
    ];

    public $map_recommend = [
        0 => '普通',
        1 => '推荐'
    ];

    public function setPublic($info, $isPublic) {
        $isPublic = intval($isPublic) ? 1 : 0;
        $this->save(['is_public' => $isPublic], ['id' => $info['id']]);
        $info['is_public'] = $isPublic;
        return (new FreelanceSearchSubject())->updateSearch($info);
    }

    public function del($subjectId) {
        $this->where(['id' => $subjectId])->delete();
        (new FreelanceSearchSubject())->where(['subject_id' => $subjectId])->delete();
    }

    public function delAll($ids) {
        $this->where(['id' => ['in', $ids]])->delete();
        (new FreelanceSearchSubject())->where(['subject_id' => ['in', $ids]])->delete();
    }

    public function getList2($where, $page, $size) {
        $list = $this->where($where)->order('updatetime desc')->limit(($page - 1) * $size, $size)->select();
        $total =  $this->where($where)->count();

        if (!empty($list)) {
            $audits = $this->map_audit;
            $res = [];
            foreach ($list as $v) {
                $reason = '';
                if ($v['audit'] == 2) {
                    $log = (new FreelanceSubjectAuditLog())->where(['subjectid' => $v['id'], 'audit' => 2])->order('id desc')->find();
                    if ($log) {
                        $reason = $log['reason'];
                    }
                }
                $res[] = [
                    'id' => $v['id'],
                    'title' => $v['title'],
                    'refreshtime' => daterange(time(),  $v['refreshtime']),
                    'period' => $v['period'],
                    'is_top' => $v['is_top'],
                    'is_public' => $v['is_public'],
                    'audit' => $v['audit'],
                    'price' => $v['price'] / 100,
                    'reason' => $reason,
                    'audit_str' => $audits[$v['audit']],
                    'view_times' => $v['view_times'],
                    'desc' => $v['desc'],
                ];
            }
            $list = $res;
        }
        return ['list' => $list, 'total' => $total];
    }

    public function getList($addtime, $endtime, $is_public, $audit, $key, $type, $page, $pagesize) {
        $where = ['is_published' => 1];
        if ($addtime) {
            $where['addtime'] = ['egt', time() - 86400 * $addtime];
        }
        if ($endtime) {
            $where['endtime'] = ['elt', time() + 86400 * $endtime];
        }
        if ($is_public > -1) {
            $where['is_public'] = $is_public;
        }
        if ($audit > -1) {
            $where['audit'] = $audit;
        }
        if ($key) {
            if ($type == 1) {
                $where['title'] = ['like', '%' . $key . '%'];
            } else if ($type == 2) {
                $where['mobile'] = $key;
            } else if ($type == 3) {
                $where['linkman'] = $key;
            }
        }
        return [
            'list' => $this->where($where)->order('updatetime desc')->limit(($page - 1) * $pagesize, $pagesize)->select(),
            'total' => $this->where($where)->count()
        ];
    }

    public function refresh($id) {
        $this->save(['refreshtime' => time()], ['id' => $id]);
        (new FreelanceSearchSubject())->save(['refreshtime' => time()], ['subject_id' => $id]);
    }

    public function setAudit($idarr, $audit, $reason = '') {
        $search = new FreelanceSearchSubject();
        $timestamp = time();
        $this->where('id', 'in', $idarr)->setField('audit', $audit);
        $uid_arr = [];
        $audit_log = [];
        $resume_list = $this->where('id', 'in', $idarr)->column('*', 'id');
        foreach ($resume_list as $key => $value) {
            $uid_arr[] = $value;
            $arr['uid'] = $value['uid'];
            $arr['subjectid'] = $key;
            $arr['audit'] = $audit;
            $arr['reason'] = $reason;
            $arr['addtime'] = $timestamp;
            $audit_log[] = $arr;
            $value['audit'] = $audit;
            $search->updateSearch($value);
        }
        $logModel = new FreelanceSubjectAuditLog();
        $logModel->saveAll($audit_log);
        return;
    }

    public function latestSubject($size) {
        $list = $this->where(['is_public' => 1, 'is_published' => 1, 'audit' => 1])->order('refreshtime desc')->limit($size)->select();
        return $this->processData($list);
    }

    public function getHotList() {
        $cacheName = 'freelance_hot_subject';
        $res = cache($cacheName);
        if (!$res) {
            $list = $this->where(['audit' => 1, 'is_public' => 1, 'is_published' => 1])->order('view_times desc')->limit(10)->select();
            $res =  $this->processData($list);
            cache($cacheName, $res, 600);
        }
        return $res;
    }

    public function processData(&$list) {
        $res = [];
        if (!empty($list)) {
            foreach ($list as $v) {
                $res[] = [
                    'id' => $v['id'],
                    'title' => $v['title'],
                    'price' => $v['price'] / 100,
                    'endtime' => date('Y-m-d', $v['endtime']),
                    'refreshtime' => daterange(time(),  $v['refreshtime']),
                    'period' => $v['period'],
                    'desc' => $v['desc'],
                    'is_top' => !!$v['is_top'],
                    'is_recommend' => !!$v['is_recommend']
                ];
            }
        }
        return $res;
    }
}
