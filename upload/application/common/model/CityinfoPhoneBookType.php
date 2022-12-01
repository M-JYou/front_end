<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/5/6
 * Time: 14:59
 */

namespace app\common\model;


class CityinfoPhoneBookType extends BaseModel {
    protected $autoWriteTimestamp = true;
    protected $createTime = 'addtime';
    protected $updateTime = 'updatetime';
    protected $readonly = ['id'];

    public function getAll() {
        $list = $this->order('sort_id desc,id asc')->select();
        return $list;
    }

    public function getValidAll() {
        $list = $this->where(['is_display' => 1])->order('sort_id desc,id asc')->field('id,title,logo')->select();
        (new Uploadfile())->getFileUrlBatch2($list, 'logo', 'logo_url');
        return $list;
    }
}
