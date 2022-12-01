<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/4/6
 * Time: 11:41
 */

namespace app\common\model;

use Think\Model;

class FreelanceSkillType extends Model {
    protected $autoWriteTimestamp = true;
    protected $createTime = 'addtime';
    protected $updateTime = 'updatetime';
    protected $readonly = ['id'];
    public function getCache($pid = 'all') {
        if (false === ($data = cache('cache_skill_type'))) {
            $list = $this->order('sort_id desc,id asc')->column(
                'id,pid,title',
                'id'
            );
            $data = [];
            foreach ($list as $key => $value) {
                $data[$value['pid']][$value['id']] = $value['title'];
                $data['all'][$value['id']] = $value['title'];
            }
            cache('cache_skill_type', $data);
        }
        if ($pid !== '') {
            $data = isset($data[$pid]) ? $data[$pid] : [];
        }
        return $data;
    }
}
