<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/5/6
 * Time: 14:44
 */

namespace app\v1_0\controller\member;

use app\common\lib\Pager;
use app\common\model\CityinfoArticleBody;
use app\common\model\CityinfoOrder;
use app\common\model\CityinfoRefreshLog;
use app\common\model\CityinfoSearchArticle;
use app\common\model\CityinfoType;
use app\common\model\Uploadfile;
use app\common\model\CityinfoArticle;
use app\common\model\CityinfoViewLog;
use app\common\model\CityinfoArticleAuditLog;

class CityInfo extends \app\v1_0\controller\common\Base {
    public function _initialize() {
        parent::_initialize();
        $this->checkLogin();
    }

    public function save_info() {
        $aModel = new CityinfoArticle();
        $bModel = new CityinfoArticleBody();
        $tModel = new CityinfoType();
        $sModel = new CityinfoSearchArticle();
        $id = input('post.id/d', 0, 'intval');
        $content =  input('post.content/s', '', 'trim,badword_filter');

        $data = [
            'linkman'                   =>      input('post.linkman/s', '', 'trim'),
            'mobile'                    =>      input('post.mobile/s', '', 'trim'),
            'type_id'                   =>      input('post.type_id/d', 0, 'intval'),
            'lat'                       =>      input('post.lat/s', '0', 'trim'),
            'lon'                       =>      input('post.lon/s', '0', 'trim'),
            'title'                     =>      input('post.title/s', '', 'trim'),
            'desc'                      =>      mb_substr($content, 0, 200),
            'imgs'                      =>      input('post.imgs/s', '', 'trim'),
            'address_detail'            =>      input('post.address_detail/s', '', 'trim'),
        ];
        $validate = new \app\common\validate\CityinfoArticle();
        if (!$validate->check($data)) {
            $this->ajaxReturn(500, $validate->getError(), $data);
        }
        if (empty($data['title'])) {
            $data['title'] = mb_substr($content, 0, 20);
        }
        if ($id) {
            unset($data['type_id']); //所属类型不可修改
            $old = $aModel->where(['uid' => $this->userinfo->uid, 'id' => $id])->find();
            if (!$old) {
                $this->ajaxReturn(402, '数据不正确', $data);
            }
            $edit_audit = intval(config('global_config.cityinfo_edited_article_audit'));
            if ($edit_audit > -1) {
                $data['audit'] = $edit_audit;
            } else {
                $data['audit'] = $old['audit'];
            }
            $r = $aModel->save($data, ['id' => $id]);
            $r1 = $bModel->save(['content' => $content], ['article_id' => $id]);
            $data['is_public'] = $old['is_public'];
            $data['endtime'] = $old['endtime'];
            $data['refreshtime'] = $old['refreshtime'];
            $data['id'] = $id;
        } else {
            $tInfo = $tModel->getPInfoByTypeId($data['type_id']);
            if (!$tInfo) {
                $this->ajaxReturn(501, '类型不正确', $data);
            }
            $data['is_public'] = 1; //默认展示
            $data['endtime'] = 0;
            if (!intval($tInfo['need_pay_for_create'])) {
                $data['endtime'] = time() + 86400 * 365; //不收费情况下信息默认时长1年
            }
            $add_audit = intval(config('global_config.cityinfo_new_article_audit'));
            $data['audit'] = $add_audit;
            $data['uid'] = $this->userinfo->uid;
            $data['refreshtime'] = time();
            $r = $aModel->save($data);
            $data['id'] = $aModel->getLastInsID();
            if ($r && $data['id']) {
                $bModel->save(['content' => $content, 'article_id' => $data['id']]);
            }
        }
        $sModel->updateSearch($data);
        $this->writeMemberActionLog($this->userinfo->uid, sprintf('%s同城信息 -【%s】', $id ? '编辑' : '新增', $data['title']));
        $this->ajaxReturn(200, '保存成功', $data);
    }
    public function view_log_list() {
        $where['uid'] = $this->userinfo->uid;
        $page = input('get.page/d', 1, 'intval');
        $pageSize = input('get.pagesize/d', 20, 'intval');
        $ViewLogModel = new CityinfoViewLog();
        $list = $ViewLogModel
            ->where($where)
            ->limit(($page - 1) * $pageSize, $pageSize)
            ->order('addtime desc')
            ->select();
        $view_article_id_arr = [];
        foreach ($list as $key => $val) {
            $view_article_id_arr[] = $val['article_id'];
        }
        $aModel = new CityinfoArticle();
        $article_list = $aModel
            ->where(['id' => ['IN', $view_article_id_arr]])
            ->column('id,title,desc,imgs');
        $img = [];
        foreach ($article_list as $key => $val) {
            $arr = explode(',', $val['imgs']);
            foreach ($arr as $k => $v) {
                $img[] = $v;
            }
        }
        $UploadfileModel = new Uploadfile();
        $imgurl = $UploadfileModel->getFileUrlBatch($img);
        foreach ($article_list as $keys => $vals) {
            $imgurlarr = [];
            $brr = explode(',', $vals['imgs']);
            foreach ($brr as $k => $v) {
                if ($v) {
                    $imgurlarr[] = $imgurl[$v];
                }
            }
            $article_list[$keys]['img_arr'] = $imgurlarr;
        }
        foreach ($list as $keys => $vals) {
            $list[$keys]['viewdate'] = date('Y-m-d', strtotime($vals['addtime']));
            $list[$keys]['article'] = $article_list[$vals['article_id']];
        }
        $view_log_list['list'] = $list;
        return $this->ajaxReturn(200, '', $view_log_list);
    }

    public function view_log_delete() {
        $id = input('get.id/d', 0, 'intval');
        if (!$id) {
            $this->ajaxReturn(500, 'ID为空');
        }
        $ViewLogModel = new CityinfoViewLog();
        $viewLogInfo = $ViewLogModel->find($id);
        if ($viewLogInfo['uid'] != $this->userinfo->uid) {
            $this->ajaxReturn(500, '非法操作');
        } else {
            $r = $ViewLogModel->where(['id' => $id])->delete();
        }
        $this->writeMemberActionLog($this->userinfo->uid, '删除同城信息访问记录');
        $this->ajaxReturn(200, '删除成功');
    }
    public function article_delete() {
        $id = input('get.id/d', 0, 'intval');
        if (!$id) {
            $this->ajaxReturn(500, 'ID为空');
        }
        $ArticleModel = new CityinfoArticle();
        $ArticleAuditLogModel = new CityinfoArticleAuditLog();
        $ArticleBodyModel = new CityinfoArticleBody();
        $SearchArticleModel = new CityinfoSearchArticle();
        $ViewLogModel = new CityinfoViewLog();
        $aInfo = $ArticleModel->find($id);
        if ($aInfo['uid'] != $this->userinfo->uid) {
            $this->ajaxReturn(500, '非法操作');
        } else {
            $r = $ArticleModel->where(['id' => $id])->delete();
            $ArticleAuditLogModel->where(['article_id' => $id])->delete();
            $ArticleBodyModel->where(['article_id' => $id])->delete();
            $SearchArticleModel->where(['article_id' => $id])->delete();
            $ViewLogModel->where(['article_id' => $id])->delete();
        }
        $this->writeMemberActionLog($this->userinfo->uid, '删除同城信息 - 【%s】', $aInfo['title']);
        $this->ajaxReturn(200, '删除成功');
    }
    public function article_public() {
        $id = input('get.id/d', 0, 'intval');
        if (!$id) {
            $this->ajaxReturn(500, 'ID为空');
        }
        $ArticleModel = new CityinfoArticle();
        $aInfo = $ArticleModel->find($id);
        if ($aInfo['uid'] != $this->userinfo->uid) {
            $this->ajaxReturn(500, '非法操作');
        }
        $r = $ArticleModel->setPublic($aInfo, 1);
        $this->ajaxReturn(200, '开启成功');
    }
    public function article_not_public() {
        $id = input('get.id/d', 0, 'intval');
        if (!$id) {
            $this->ajaxReturn(500, 'ID为空');
        }
        $ArticleModel = new CityinfoArticle();
        $aInfo = $ArticleModel->find($id);
        if ($aInfo['uid'] != $this->userinfo->uid) {
            $this->ajaxReturn(500, '非法操作');
        }
        $r = $ArticleModel->setPublic($aInfo, 0);
        $this->ajaxReturn(200, '关闭成功');
    }
    public function my_order_list() {
        $where['status'] = 1;
        $where['uid'] = $this->userinfo->uid;
        $OrderModel = new CityinfoOrder();
        $page = input('get.page/d', 1, 'intval');
        $pageSize = input('get.pagesize/d', 20, 'intval');
        $list = $OrderModel
            ->where($where)
            ->limit(($page - 1) * $pageSize, $pageSize)
            ->order('addtime desc')
            ->select();
        foreach ($list as $key => $val) {
            $list[$key]['paytime_'] = date('Y-m-d H:i:s', $val['paytime']);
        }
        $my_order_list['list'] = $list;
        return $this->ajaxReturn(200, '', $my_order_list);
    }
    public function my_article_list() {
        $audit = input('get.audit/d', 0, 'intval');
        $all = input('get.all/d', 0, 'intval');
        $condition = [];
        if ($all == 0) {
            if ($audit == 0) {
                $where['audit'] = $audit;
                $where['is_public'] = 1;
                $where['endtime'] = array('gt', time());
            } elseif ($audit == 1) {
                $where['audit'] = $audit;
                $where['is_public'] = 1;
                $where['endtime'] = array('gt', time());
            } else {
                $condition['audit'] = $audit;
                $condition['is_public'] = 0;
                $condition['endtime'] = array('lt', time());
            }
        } elseif ($all == 1) {
            $where['is_top'] = 0;
        }
        $where['uid'] = $this->userinfo->uid;
        $page = input('get.page/d', 1, 'intval');
        $pageSize = input('get.pagesize/d', 20, 'intval');
        $ArticleModel = new CityinfoArticle();
        if ($condition) {
            $list = $ArticleModel
                ->where($where)
                ->where(function ($query) use ($condition) {
                    $query->whereOr($condition);
                })
                ->limit(($page - 1) * $pageSize, $pageSize)
                ->order('addtime desc')
                ->select();
        } else {
            $list = $ArticleModel
                ->where($where)
                ->limit(($page - 1) * $pageSize, $pageSize)
                ->order('addtime desc')
                ->select();
        }

        $ArticleAuditLogModel = new CityinfoArticleAuditLog();
        $where_audit['uid'] = $this->userinfo->uid;
        $where_audit['audit'] = 2;
        $ArticleAuditLoglist = $ArticleAuditLogModel
            ->where($where_audit)
            ->order('addtime desc')
            ->column('article_id,reason');
        $my_article_list = [];
        foreach ($list as $key => $val) {
            $list[$key]['refreshtime'] = daterange(time(), $val['refreshtime']);
            if ($val['audit'] == 2) {
                $list[$key]['audit_reason'] = $ArticleAuditLoglist[$val['id']];
            } else {
                $list[$key]['audit_reason'] = 0;
            }
            if ($val['endtime'] > time()) {
                $list[$key]['is_end'] = 0;
            } else {
                $list[$key]['is_end'] = 1;
            }
        }

        $my_article_list['list'] = $list;
        return $this->ajaxReturn(200, '', $my_article_list);
    }
    public function get_cityinfo_kefu() { //这在客户端自己取全局配置就行,无须接口
        $UploadfileModel = new Uploadfile();
        $cityinfo_kefu = config('global_config.cityinfo_kefu');
        $cityinfo_kefu_url = $UploadfileModel->getFileUrl($cityinfo_kefu);
        return $this->ajaxReturn(200, '', $cityinfo_kefu_url);
    }
    public function feedback() {
        $input_data = [
            'uid' => $this->userinfo->uid,
            'mobile' => $this->userinfo->mobile,
            'addtime' => time(),
            'status' => 0,
            'article_id' => input('post.article_id/d', '0', 'intval'),
            'content' => input('post.content/s', '', 'trim'),
        ];
        $validate = new \think\Validate([
            'content' => 'require|max:200',
        ]);
        if (!$validate->check($input_data)) {
            $this->ajaxReturn(500, $validate->getError());
        }
        model('CityinfoFeedback')->save($input_data);
        $this->ajaxReturn(200, '已提交，我们会尽快处理');
    }

    public function publish_promo() {
        $tModel = new CityinfoType();
        $typeId =  input('get.type_id/d', '0', 'intval');
        $type = $tModel->getPInfoByTypeId($typeId);
        if (!$type) {
            return $this->ajaxReturn(500, '参数错误');
        }
        if (!intval($type['need_pay_for_create'])) {
            $this->ajaxReturn(200, '', []);
        }
        $arr = $type['pay_for_create'];
        foreach ($arr as $k => &$v) {
            $v['index'] = $k;
            $v['tip'] = sprintf('低至%.1f元/天', number_format($v['fee'] / $v['days'], 1));
        }
        $this->ajaxReturn(200, '', $arr);
    }

    public function order_pay() {
        $oModel = new CityinfoOrder();
        $tModel = new CityinfoType();
        $aModel = new CityinfoArticle();
        $id = input('post.id/d', 0, 'intval');
        $pay_type = input('post.pay_type/s', '', 'trim');
        $redirect = input('post.redirect_url/s', '', 'trim');
        $openId =  input('post.openid/s', '', 'trim');
        $order_type = input('post.order_type/d', 1, 'intval');
        $publish_index = input('post.publish_index/d', -1, 'intval');
        $promo_index = input('post.promo_index/d', -1, 'intval');
        $param1 = $param2 = 0;
        $fee = 0;

        $aInfo = $aModel->find($id);
        if (empty($aInfo)) {
            $this->ajaxReturn(500, '非法请求');
        }

        switch ($order_type) {
            case CityinfoOrder::TYPE_PUBLISH_ARTICLE:
                if ($aInfo['endtime'] > time()) {
                    $this->ajaxReturn(500, '当前信息在有效期内', $aInfo);
                }
                $desc =  $aInfo['title'];
                $pInfo = $tModel->getPInfoByTypeId($aInfo['type_id']);
                if ($pInfo['need_pay_for_create']) {
                    if (!isset($pInfo['pay_for_create'][$publish_index])) {
                        $this->ajaxReturn(500, '非法数据');
                    }
                    $fee = $pInfo['pay_for_create'][$publish_index]['fee'] * 100;
                    $days = $pInfo['pay_for_create'][$publish_index]['days'];
                    $title = '设置有效期 ' . $days . ' 天';
                    if (!$fee) {
                        $aModel->setEndtime($aInfo, $days);
                    }
                } else {
                    $aModel->setEndtime($aInfo, 365);
                }
                $param1 = $publish_index;
                break;
            case CityinfoOrder::TYPE_VIEW_ARTICLE:
                $title = '查看联系方式';
                $desc = sprintf('查看联系方式-[%s]', $aInfo['title']);
                $pInfo = $tModel->getPInfoByTypeId($aInfo['type_id']);
                if ($pInfo['need_pay_for_mobile']) {
                    $fee = $pInfo['pay_for_mobile'] * 100;
                }
                break;
            case CityinfoOrder::TYPE_PROMOTE:
                if ($aInfo['is_top']) {
                    return $this->ajaxReturn(500, '当前信息正在推广中');
                }
                $desc = sprintf('置顶-[%s]', $aInfo['title']);
                $set = config('global_config.cityinfo_promote_set');
                if (!isset($set[$promo_index])) {
                    $this->ajaxReturn(500, '非法数据');
                }
                $fee = $set[$promo_index]['fee'] * 100;
                $days = $set[$promo_index]['days'];
                $title = '置顶' . $days . '天';
                $param2 = $promo_index;
                if (!$fee) {
                    $aModel->promo($aInfo['id'], $days);
                }
                break;
            case CityinfoOrder::TYPE_PUBLISH_PROMOTE:
                $desc = $aInfo['title'];
                $pInfo = $tModel->getPInfoByTypeId($aInfo['type_id']);
                if ($publish_index < 0 || $promo_index < 0) {
                    $this->ajaxReturn(500, '非法数据');
                }
                if ($aInfo['is_top']) {
                    return $this->ajaxReturn(500, '当前信息正在推广中');
                }
                if ($aInfo['endtime'] > time()) {
                    return $this->ajaxReturn(500, '当前信息在有效期内');
                }
                if ($pInfo['need_pay_for_create']) {
                    $fee = $pInfo['pay_for_create'][$publish_index]['fee'] * 100;
                }
                $param1 = $publish_index;
                $set = config('global_config.cityinfo_promote_set');
                $fee += $set[$promo_index]['fee'] * 100;
                $param2 = $promo_index;
                $days = $pInfo['pay_for_create'][$publish_index]['days'];
                $days2 = $set[$promo_index]['days'];
                if (!$fee) {
                    $aModel->setEndtime($aInfo, $days);
                    $aModel->promo($aInfo['id'], $days2);
                }
                $title = sprintf('设置有效期%d天,置顶%d天', $days, $days2);
                break;
            case CityinfoOrder::TYPE_REFRESH_ARTICLE:
                $title = '刷新内容';
                $period = config('global_config.cityinfo_free_refresh_period');
                $freeTimes = intval(config('global_config.cityinfo_free_refresh_time'));
                $fee = intval(config('global_config.cityinfo_refresh_article_fee') * 100);
                if ($period) {
                    if ((time() - $aInfo['refreshtime']) < $period) {
                        return $this->ajaxReturn(500, sprintf('刷新间隔不能小于 %d 秒', $period));
                    }
                }
                if ($freeTimes && $fee > 0) {
                    $rfModel = new CityinfoRefreshLog();
                    if ($rfModel->freeTimes($this->userinfo->uid) > 0) {
                        $aModel->refresh($id);
                        $rfModel->incTimes($this->userinfo->uid);
                        $this->ajaxReturn(200, '刷新成功');
                    }
                }
                $desc = $aInfo['title'];
                if (!$fee) {
                    $aModel->refresh($id);
                    $this->ajaxReturn(200, '刷新成功');
                }
                break;
            default:
                return $this->ajaxReturn(500, '参数错误');
        }
        if (!$fee) {
            $this->ajaxReturn(200, '', ['pay_status' => 1]);
        }
        $r = (new CityinfoOrder())->newOrder($this->userinfo->uid, $this->userinfo->mobile, $order_type, $fee, $id, $title, $desc, $param1, $param2);
        if (!$r) {
            $this->ajaxReturn(500, '下单失败');
        }
        $msg = '';
        try {
            $res = $oModel->callPay($r, ['redirect_url' => $redirect, 'openid' => $openId, 'pay_type' => $pay_type]);
            if ($res === false) {
                return false;
            }
            $return  = [];
            $return['pay_status'] = 0;
            $return['parameter'] = $res;
            $return['order_amount'] = $r['amount'] / 100;
            $return['order_oid'] = $r['order_sn'];
            $res = $return;
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            $res = false;
        }
        $this->ajaxReturn($res ? 200 : 500, $msg, $res);
    }

    public function refresh() {
        $aModel = new CityinfoArticle();
        $id = input('post.id/d', 0, 'intval');
        if (!$id) {
            $this->ajaxReturn(500, 'ID为空');
        }
        $period = config('global_config.cityinfo_free_refresh_period');
        $freeTimes = intval(config('global_config.cityinfo_free_refresh_time'));
        $fee = intval(config('global_config.cityinfo_refresh_article_fee'));
        $aInfo = $aModel->find($id);

        if ($period) {
            if ((time() - $aInfo['refreshtime']) < $period) {
                return $this->ajaxReturn(500, sprintf('刷新间隔不能小于 %s', time_format($period)));
            }
        }
        if ($fee <= 0) {
            $aModel->refresh($id);
            $this->ajaxReturn(200, '刷新成功');
        }
        if ($freeTimes > 0 && $fee > 0) {
            $rfModel = new CityinfoRefreshLog();
            if ($rfModel->freeTimes($this->userinfo->uid) > 0) {
                $aModel->refresh($id);
                $rfModel->incTimes($this->userinfo->uid);
                $this->ajaxReturn(200, '刷新成功');
            }
        }
        $this->ajaxReturn(200, '', ['fee' => $fee]);
    }
}
