<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/4/7
 * Time: 11:46
 */

namespace app\common\model;


use Think\Model;

class FreelanceSearchSubject extends Model {
    protected $autoWriteTimestamp = true;
    protected $createTime = 'addtime';
    protected $updateTime = 'updatetime';
    protected $readonly = ['subject_id'];

    public function search($key, $page, $size) {
        $ids = $this->where(sprintf("match(`content`)  AGAINST('%s' IN BOOLEAN MODE)", $key))
            ->order('refreshtime desc')
            ->limit(($page - 1) * $size, $size)
            ->column('subject_id');
        $total = $this->where(sprintf("match(`content`)  AGAINST('%s' IN BOOLEAN MODE)", $key))->count();
        if (empty($ids)) {
            return ['list' => [], 'total' => $total];
        }
        $m = new FreelanceSubject();
        $where = ['id' => ['in', $ids]];
        return [
            'list' => $m->where($where)->select(),
            'total' =>  $total
        ];
    }

    public function del($id) {
        $this->where(['id' => $id])->delete();
    }
    public function updateSearch($info) {
        $id = $info['id'];
        if (!$info['is_public'] || ($info['audit'] != 1) || !$info['is_published']) {
            return $this->where(['subject_id' => $id])->delete();
        }

        $row = $this->find($id) ?: [];
        $data['content'] = sprintf('%s,%s', $info['title'], $info['desc']);

        $data['refreshtime'] = $info['refreshtime'];
        if ($row) {
            return $this->save($data, ['subject_id' => $id]);
        } else {
            $data['subject_id'] = $id;
            return $this->save($data);
        }
    }
}
