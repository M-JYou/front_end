<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/4/6
 * Time: 11:41
 */

namespace app\common\model;


use Think\Model;

class FreelanceVisitHistory extends Model {
    protected $autoWriteTimestamp = true;
    protected $createTime = 'addtime';
    protected $updateTime = 'updatetime';
    protected $readonly = ['id', 'uid'];

    const TYPE_RESUME = 1;
    const TYPE_SUBJECT = 2;
    public function record($uid, $rowUid, $itemId, $type) {
        if ($type == self::TYPE_SUBJECT) {
            (new FreelanceSubject())->where(['id' => $itemId])->setInc('view_times');
        } else {
            (new FreelanceResume())->where(['id' => $itemId])->setInc('view_times');
        }
        if (!$uid) return;
        if ($uid == $rowUid) return;
        $where = ['uid' => $uid, 'item_id' => $itemId, 'type' => $type];
        $row = $this->where($where)->find();

        if ($row) {
            return $this->save(['updatetime' => time()], $where);
        } else {
            return $this->save($where);
        }
    }

    public function hasVisited($uid, $itemId, $type) {
        $where = ['uid' => $uid, 'item_id' => $itemId, 'type' => $type];
        return $this->where($where)->count() > 0;
    }

    public function getList($uid, $type, $page, $pageSize) {
        $where = ['uid' => $uid, 'type' => $type];
        $total = $this->where($where)->count();
        if ($type == self::TYPE_RESUME) {
            $list = $this->with('resume.skills,resume.services,resume.skills.skill')->where($where)->order('updatetime desc')->limit(($page - 1) * $pageSize, $pageSize)->select();
        } else {
            $list = $this->with('subject')->where($where)->order('updatetime desc')->limit(($page - 1) * $pageSize, $pageSize)->select();
        }
        return ['list' => $this->processData($list), 'total' => $total];
    }

    protected function processData(&$list) {
        $eduMap = (new BaseModel)->map_education;
        $arr = [];
        $imgArr = [];
        if (!empty($list)) {
            foreach ($list as $v) {
                if ($v['type'] == self::TYPE_RESUME) {
                    $resume = $v['resume'];
                    if (intval($resume['hide_name'])) {
                        $len = mb_strlen($resume['name']);
                        $resume['name'] = mb_substr($resume['name'], 0, 1) . str_pad('*', $len - 1);
                    }
                    if ($resume['avatar'] > 0) {
                        $imgArr[] = $resume['avatar'];
                    }
                    if (intval($resume['start_work_date']) == 0) {
                        $exp_str = '尚未工作';
                    } else {
                        $exp_str =  format_date(strtotime($resume['start_work_date']));
                    }
                    $arr[] = [
                        'exp_str' => $exp_str,
                        'resume_id' => $v['item_id'],
                        'name' => $resume['name'],
                        'age' => $resume['age'],
                        'refreshtime' => date('Y-m-d H:i:s', $resume['refreshtime']),
                        'education' => isset($eduMap[$resume['education']]) ? $eduMap[$resume['education']] : $resume['education'],
                        'professional_title' => $resume['professional_title'],
                        'is_public' => $resume['is_public'],
                        'services' => $resume['services'],
                        'skills' => $resume['skills'],
                        'avatar' => $resume['avatar'],
                        'is_top' => $resume['is_top'],
                    ];
                }
                if ($v['type'] == self::TYPE_SUBJECT) {
                    $subject = $v['subject'];
                    $arr[] = [
                        'subject_id' => $v['item_id'],
                        'title' => $subject['title'],
                        'price' => $subject['price'] / 100,
                        'endtime' => date('Y-m-d', $subject['endtime']),
                        'period' => $subject['period'],
                        'is_top' => $subject['is_top'],
                        'desc' => $subject['desc'],
                        'refreshtime' => date('Y-m-d H:i:s', $subject['refreshtime'])
                    ];
                }
            }
        }
        if (!empty($imgArr)) {
            $img_src_data = model('Uploadfile')->getFileUrlBatch($imgArr);
            foreach ($arr as &$av) {
                $av['avatar_img'] = isset($img_src_data[$av['avatar']]) ?  $img_src_data[$av['avatar']] : '';
            }
        }
        return $arr;
    }

    public function resume() {
        return $this->hasOne('FreelanceResume', 'id', 'item_id');
    }

    public function subject() {
        return $this->hasOne('FreelanceSubject', 'id', 'item_id');
    }
}
