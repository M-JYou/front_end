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

class FreelanceSkill extends Model {
    protected $autoWriteTimestamp = true;
    protected $createTime = 'addtime';
    protected $updateTime = 'updatetime';
    protected $readonly = ['id', 'uid'];
    public static $levelMap = [
        '1' => '入门',
        '2' => '熟练',
        '3' => '精通',
    ];

    public function skill() {
        return $this->hasOne('FreelanceSkillType', 'id', 'skill_id')->bind('title');
    }

    public function saveSkill($id, $skill_id, $level, $custom_name, $uid) {
        $data = [
            'skill_id' => $skill_id,
            'level' => $level,
            'custom_name' => $custom_name,
            'uid'   => $uid
        ];
        if ($id) {
            return $this->save($data, ['id' => $id, 'uid' => $uid]);
        } else {
            return $this->save($data);
        }
    }

    public function saveData($data, $uid) {
        $ids = [];
        $res = true;
        $validate = new \app\common\validate\FreelanceSkill();

        if (empty($data)) {
            throw new \Exception('非法请求');
        }
        $resume = FreelanceResume::where('uid', $uid)->column('id');
        Db::startTrans();
        foreach ($data as $v) {
            if (!in_array($v['resume_id'], $resume)) {
                Db::rollback();
                throw new \Exception('非法数据');
            }
            if (!$validate->check($v)) {
                Db::rollback();
                throw new \Exception($validate->getError());
            }
            $v['uid'] = $uid;
            if (!isset($v['id'])) {
                $res = $this->save($v);
                if (!$res) break;
                $ids[] = $this->getLastInsID();
            } else {
                $res = $this->where(['id' => $v['id']])->save($v);
                if (!$res) break;
                $ids[] = $v['id'];
            }
        }
        if ($res) {
            $this->where('id', 'not in', $ids)->where('uid', $uid)->delete();
        }

        if ($res) {
            Db::commit();
        } else {
            Db::rollback();
        }
        return $res;
    }
}
