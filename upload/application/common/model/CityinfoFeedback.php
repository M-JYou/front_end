<?php

namespace app\common\model;

class CityinfoFeedback extends \app\common\model\BaseModel {
    protected $readonly = ['id'];

    public $map_status = [
        0 => '未处理',
        1 => '已处理'
    ];

    public function article() {
        return $this->hasOne('CityinfoArticle', 'id', 'article_id')->bind('title');
    }

    public function getList($status, $page, $pagesize) {
        $where = [];
        if ($status > -1) {
            $where['status'] = $status;
        }
        return [
            'list' => $this->with('article')->where($where)->order('id desc')->limit(($page - 1) * $pagesize, $pagesize)->select(),
            'total' => $this->where($where)->count()
        ];
    }

    public function delAll($ids) {
        $this->where(['id' => ['in', $ids]])->delete();
    }

    public function setStatus($idarr, $status) {
        $this->where('id', 'in', $idarr)->setField('status', $status);
    }
}
