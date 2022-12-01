<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/5/6
 * Time: 17:41
 */

namespace app\common\model;


class CityinfoSearchArticle extends BaseModel {
    protected $autoWriteTimestamp = true;
    protected $createTime = 'addtime';
    protected $updateTime = 'updatetime';
    protected $readonly = ['article_id'];

    public function search($key, $page, $size) {
        $curtime = time();
        $ids = $this->where(sprintf("match(`content`)  AGAINST('%s' IN BOOLEAN MODE)", $key))
            ->where('endtime > ' . $curtime)
            ->order('refreshtime desc')
            ->limit(($page - 1) * $size, $size)
            ->column('resume_id');

        if (empty($ids)) {
            return [];
        }
        $aModel = new CityinfoArticle();
        $where = ['id' => ['in', $ids]];
        return $aModel->where($where)->select();
    }

    public function updateSearch($info) {
        $curtime = time();

        $id = $info['id'];
        if (!$info['is_public'] || ($info['audit'] != 1) || $info['endtime'] < $curtime) {
            return $this->where(['article_id' => $id])->delete();
        }

        $row = $this->find($id) ?: [];
        if (!isset($info['content'])) {
            $info['content'] = (new CityinfoArticleBody())->where(['article_id' => $info['id']])->value('content');
        }
        $data['content'] = sprintf('%s,%s,%s', $info['title'], $info['content'], $info['linkman']);

        $data['refreshtime'] = $info['refreshtime'];
        $data['endtime'] = $info['endtime'];
        if ($row) {
            return $this->save($data, ['article_id' => $id]);
        } else {
            $data['article_id'] = $id;
            return $this->save($data);
        }
    }
}
