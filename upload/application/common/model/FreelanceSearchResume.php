<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/4/7
 * Time: 11:46
 */

namespace app\common\model;


use Think\Model;

class FreelanceSearchResume extends Model {
    protected $autoWriteTimestamp = true;
    protected $createTime = 'addtime';
    protected $updateTime = 'updatetime';
    protected $readonly = ['resume_id'];

    public function search($key, $page, $size) {
        $ids = $this->where(sprintf("match(`content`)  AGAINST('%s' IN BOOLEAN MODE)", $key))
            ->order('refreshtime desc')
            ->limit(($page - 1) * $size, $size)
            ->column('resume_id');
        $total = $this->where(sprintf("match(`content`)  AGAINST('%s' IN BOOLEAN MODE)", $key))->count();
        if (empty($ids)) {
            return ['list' => [], 'total' => $total];
        }
        $resume = new FreelanceResume();
        $where = ['id' => ['in', $ids]];
        return [
            'list' => $resume->with('skills.skill,services')->where($where)->select(),
            'total' =>  $total
        ];
    }

    public function updateSearch($info) {
        $service = new FreelanceService();
        $skill = new FreelanceSkill();

        $id = $info['id'];
        if (!$info['is_public'] || ($info['audit'] != 1) || !$info['is_published']) {
            return $this->where(['resume_id' => $id])->delete();
        }

        $row = $this->find($id) ?: [];
        $uid = $info['uid'];
        $serv = $service->where('uid', $uid)->column('title');
        $ski = $skill->with('skill')->where('uid', $uid)->select();
        $skit = [];
        foreach ((array)$ski as $v) {
            $skit[] = $v['title'];
        }
        $data['content'] = sprintf('%s,%s,%s,%s', $info['name'], $info['professional_title'], implode(',', $serv), implode(',', $skit));

        $data['refreshtime'] = $info['refreshtime'];
        if ($row) {
            return $this->save($data, ['resume_id' => $id]);
        } else {
            $data['resume_id'] = $id;
            return $this->save($data);
        }
    }
}
