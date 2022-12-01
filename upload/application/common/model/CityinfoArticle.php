<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/5/6
 * Time: 17:41
 */

namespace app\common\model;


class CityinfoArticle extends BaseModel {
    protected $autoWriteTimestamp = true;
    protected $createTime = 'addtime';
    protected $updateTime = 'updatetime';
    protected $readonly = ['id'];

    public $map_audit = [
        0 => '未审核',
        2 => '未通过',
        1 => '已审核'
    ];

    public $map_recommend = [
        0 => '普通',
        1 => '推荐'
    ];
    public $map_top = [
        0 => '普通',
        1 => '置顶'
    ];
    public function getHostList($size) {
        $list = $this->where(['endtime' => ['gt', time()], 'audit' => 1, 'is_public' => 1])->with('type')
            ->field('title,desc,view_times,addtime,imgs,type_id')->order('view_times desc')->limit($size)->select();
        return $list;
    }
    public function setPublic($info, $isPublic) {
        $isPublic = intval($isPublic) ? 1 : 0;
        $this->save(['is_public' => $isPublic], ['id' => $info['id']]);
        $info['is_public'] = $isPublic;
        return (new CityinfoSearchArticle())->updateSearch($info);
    }
    public function refresh($id) {
        $this->save(['refreshtime' => time()], ['id' => $id]);
        (new CityinfoSearchArticle())->save(['refreshtime' => time()], ['article_id' => $id]);
    }

    public function setEndtime($aInfo, $days) {
        $end = time() + 86400 * $days;
        $this->save(['endtime' => $end], ['id' => $aInfo['id']]);
        $aInfo['endtime'] = $end;
        (new CityinfoSearchArticle())->updateSearch($aInfo);
    }

    /**
     * 是否用户可查看信息的联系方式
     * @param $aInfo   内容数组(一条article记录)
     * @param $uid 预查看联系方式的用户uid
     * @return bool
     * @throws \think\Exception
     */
    public function canViewMobile($aInfo, $uid) {
        if ($aInfo['uid'] == $uid) {
            return true;
        }
        $tModel = new CityinfoType();
        $pInfo = $tModel->getPInfoByTypeId($aInfo['type_id']);
        if (!intval($pInfo['need_pay_for_mobile'])) {
            return true;
        }
        $oModel = new CityinfoOrder();
        $c = $oModel->where(['uid' => $uid, 'item_id' => $aInfo['id'], 'type' => CityinfoOrder::TYPE_VIEW_ARTICLE, 'status' => CityinfoOrder::STATUS_PAID])->count();
        if ($c > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function promo($id, $days) {
        $this->save(['is_top' => 1], ['id' => $id]);
        (new ServiceQueue())->save([
            'utype' => 1,
            'pid' => $id,
            'type' => 'cityinfo-stick',
            'addtime' => time(),
            'deadline' => time() + $days * 86400
        ]);
    }

    /**
     * 给内容列表加图片
     * @param $list
     */
    public function processArticleMulti(&$list) {
        if (empty($list)) return;

        $rmap = [];
        foreach ($list as $k => &$v) {
            $v['img_arr'] = [];
            if (!empty($v['imgs'])) {
                $arr = explode(',', $v['imgs']);
                foreach ($arr as $vv) {
                    $vvv = intval($vv);
                    if ($vvv) $rmap[$vvv] = $k;
                }
            }
        }

        $up = new Uploadfile();
        if (empty($rmap)) return;
        $file_arr = $up->where(['id' => ['in', array_keys($rmap)]])->field('id,save_path,platform')->select();
        foreach ($file_arr as $value) {
            $id = $value['id'];
            $img = make_file_url(
                $value['save_path'],
                $value['platform']
            );
            $narr = $list[$rmap[$id]]['img_arr'];
            $narr[] = $img;
            $list[$rmap[$id]]['img_arr'] = $narr;
        }
    }

    /**
     * 给单个内容加图片,需要精确到图片id和图片url,便于修改
     * @param $list
     */
    public function processArticleDetail(&$one) {
        if (empty($one)) return;
        $one['img_arr'] = [];
        if (empty($one['imgs'])) return;

        $arr = [];
        $oarr = explode(',', $one['imgs']);
        foreach ($oarr as $v) {
            $vv = intval($v);
            if ($vv) $arr[] = $vv;
        }

        $up = new Uploadfile();
        if (empty($arr)) return;
        $file_arr = $up->where(['id' => ['in', $arr]])->field('id,save_path,platform')->select();
        $i = 0;
        foreach ($file_arr as $value) {
            $id = $value['id'];
            $img = make_file_url(
                $value['save_path'],
                $value['platform']
            );
            $narr = $one['img_arr'];
            $narr[$i]['id'] = $id;
            $narr[$i]['img'] = $img;
            $i++;
            $one['img_arr'] = $narr;
        }
    }

    public function type() {
        return $this->hasOne('CityinfoType', 'id', 'type_id')->bind(['type_name' => 'title']);
    }

    public function getList($type_id, $addtime, $endtime, $is_public, $is_recommend, $audit, $key, $type, $page, $pagesize) {
        $where = [];
        if (is_array($type_id) && count($type_id) > 0) {
            if (count($type_id) > 1) {
                $where['type_id'] = $type_id[1];
            } else if ($type_id[0] > 0) {
                $ids = (new CityinfoType())->where('pid', $type_id[0])->column('id');
                $where['type_id'] = ['in', $ids];
            }
        }
        if ($addtime) {
            $where['addtime'] = ['egt', time() - 86400 * $addtime];
        }
        if ($endtime) {
            $where['endtime'] = ['elt', time() + 86400 * $endtime];
        }
        if ($is_public > -1) {
            if ($is_public) {
                $where['endtime'] = ['gt', time()];
            } else {
                $where['endtime'] = ['lt', time()];
            }
        }
        if ($audit > -1) {
            $where['audit'] = $audit;
        }
        if ($is_recommend > -1) {
            $where['is_recommend'] = $is_recommend;
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

    public function setAudit($idarr, $audit, $reason = '') {
        $search = new CityinfoSearchArticle();
        $timestamp = time();
        $this->where('id', 'in', $idarr)->setField('audit', $audit);
        $audit_log = [];
        $list = $this->where('id', 'in', $idarr)->column('*', 'id');
        foreach ($list as $key => $value) {
            $arr['uid'] = $value['uid'];
            $arr['article_id'] = $key;
            $arr['audit'] = $audit;
            $arr['reason'] = $reason;
            $arr['addtime'] = $timestamp;
            $audit_log[] = $arr;
            $value['audit'] = $audit;
            $search->updateSearch($value);
        }
        $logModel = new CityinfoArticleAuditLog();
        $logModel->saveAll($audit_log);
        return;
    }

    public function delAll($ids) {
        $search = new CityinfoSearchArticle();
        $this->where(['id' => ['in', $ids]])->delete();
        $search->where(['article_id' => ['in', $ids]])->delete();
    }
}
