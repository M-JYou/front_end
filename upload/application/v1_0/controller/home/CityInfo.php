<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/5/6
 * Time: 14:46
 */

namespace app\v1_0\controller\home;

use app\common\lib\Pager;
use app\common\model\CityinfoOrder;
use app\common\model\CityinfoType;
use app\common\model\Uploadfile;
use app\common\model\CityinfoPhoneBookType;
use app\common\model\CityinfoPhoneBook;
use app\common\model\CityinfoArticle;
use app\common\model\CityinfoSearchArticle;
use app\common\model\CityinfoArticleBody;
use app\common\model\CityinfoAd;
use app\common\model\CityinfoAdCategory;
use app\common\model\CityinfoViewLog;

class CityInfo extends \app\v1_0\controller\common\Base {
    public function type_tree() {
        $m = new CityinfoType();
        $list = $m->getCache();
        foreach ($list as $k => $v) {
            if (count($v['children']) == 0) unset($list[$k]);
        }
        $this->ajaxReturn(200, 'ok', array_values($list));
    }

    public function article_list() {
        $ArticleModel = new CityinfoArticle();
        $TypeModel = new CityinfoType();
        $type_id = input('get.type_id/d', 0, 'intval');
        $keywords = input('get.keywords/s', '', 'trim');
        $search_type = input('get.search_type/s', '', 'trim');
        $pid = input('get.pid/d', 0, 'intval');
        $where['endtime'] = ['gt', time()];
        $where['audit'] = 1;
        $where['is_public'] = 1;
        if ($keywords) {
            $SearchArticleModel = new CityinfoSearchArticle();
            $article_id_arr = $SearchArticleModel
                ->where(sprintf("match(`content`)  AGAINST('%s' IN BOOLEAN MODE)", $keywords))
                ->column('article_id');
            $where['id'] = array('IN', $article_id_arr);
        } elseif ($type_id) {
            $where['type_id'] = $type_id;
        } elseif ($pid) {
            $article_type_id_arr = $TypeModel->where(array('pid' => $pid))->column('id');
            $where['type_id'] = array('IN', $article_type_id_arr);
        }
        $page = input('get.page/d', 1, 'intval');
        if ($page == 1) {
            $AdCategoryModel = new CityinfoAdCategory();
            $AdModel = new CityinfoAd();
            $where_ad_category['alias'] = 'QS_cityinfo_list_center';
            $ad_category = $AdCategoryModel
                ->where($where_ad_category)->find();
            $timestamp = time();
            $ad_list = $AdModel
                ->where('is_display', 1)
                ->where('cid', $ad_category['id'])
                ->where('starttime', '<=', $timestamp)
                ->where(function ($query) use ($timestamp) {
                    $query->where('deadline', '>=', $timestamp)->whereOr('deadline', 0);
                })
                ->limit($ad_category['ad_num'])
                ->order('sort_id desc,id desc')
                ->select();

            $list['ad_list'] = $AdModel->process($ad_list);
        } else {
            $list['ad_list'] = [];
        }
        $pageSize = input('get.pagesize/d', 10, 'intval');
        $article_list = $ArticleModel
            ->where($where)
            ->limit(($page - 1) * $pageSize, $pageSize)
            ->order('is_top desc,refreshtime desc')
            ->select();
        //dump($ArticleModel->getLastSql());die;
        $CityinfoModel = new CityinfoType();
        $type_name_arr = $CityinfoModel->column('id,title,pid');
        $ArticleModel->processArticleMulti($article_list);
        foreach ($article_list as $keys => $vals) {
            $article_list[$keys]['refreshtime'] = daterange(time(), $vals['refreshtime']);
            $article_list[$keys]['pid'] = $type_name_arr[$vals['type_id']]['pid'];
            $article_list[$keys]['type_name'] = $type_name_arr[$vals['type_id']]['title'];
        }
        $list['list'] = $article_list;
        return $this->ajaxReturn(200, '', $list);
    }
    public function article_hot_list() {
        $m = new CityinfoViewLog();
        $article_hot_list = $m->getHotList();
        foreach ($article_hot_list as $key => $val) {
            $article_hot_list[$key]['refreshtime'] = daterange(time(), $val['refreshtime']);
        }
        return $this->ajaxReturn(200, '', $article_hot_list);
    }
    public function article_new_list() {
        $ArticleModel = new CityinfoArticle();
        $where['endtime'] = ['gt', time()];
        $where['audit'] = 1;
        $where['is_public'] = 1;
        $page = input('get.page/d', 1, 'intval');
        $pageSize = input('get.pagesize/d', 20, 'intval');
        $article_new_list = $ArticleModel
            ->limit(($page - 1) * $pageSize, $pageSize)
            ->where($where)
            ->order('is_top desc,refreshtime desc')
            ->select();
        $CityinfoModel = new CityinfoType();
        $type_name_arr = $CityinfoModel->column('id,title');
        $ArticleModel->processArticleMulti($article_new_list);
        foreach ($article_new_list as $keys => $vals) {
            $article_new_list[$keys]['refreshtime'] = daterange(time(), $vals['refreshtime']);
            $article_new_list[$keys]['type_name'] = $type_name_arr[$vals['type_id']];
        }
        return $this->ajaxReturn(200, '', $article_new_list);
    }
    public function cityinfo_index() {
        $ArticleModel = new CityinfoArticle();
        if (cache('cityinfo_index')) {
            $index_list_cache = cache('cityinfo_index');
            $index_list['num'] = $index_list_cache['num'];
            $index_list['view'] = $index_list_cache['view'];
        } else {
            $index_list['num'] = $ArticleModel->count();
            $index_list['view'] = $ArticleModel->sum('view_times');
            cache('cityinfo_index', $index_list, 3600);
        }

        $AdCategoryModel = new CityinfoAdCategory();
        $AdModel = new CityinfoAd();
        $UploadfileModel = new Uploadfile();

        $where_ad_top_category['alias'] = 'QS_cityinfo_index_top';
        $ad_top_category = $AdCategoryModel->where($where_ad_top_category)->find();
        $timestamp = time();
        $top_ad_list = $AdModel
            ->where('is_display', 1)
            ->where('cid', $ad_top_category['id'])
            ->where('starttime', '<=', $timestamp)
            ->where(function ($query) use ($timestamp) {
                $query->where('deadline', '>=', $timestamp)->whereOr('deadline', 0);
            })
            ->limit($ad_top_category['ad_num'])
            ->order('sort_id desc,id desc')
            ->select();

        $where_ad_center_category['alias'] = 'QS_cityinfo_index_center';
        $ad_center_category = $AdCategoryModel->where($where_ad_center_category)->find();
        $timestamp = time();
        $center_ad_list = $AdModel
            ->where('is_display', 1)
            ->where('cid', $ad_center_category['id'])
            ->where('starttime', '<=', $timestamp)
            ->where(function ($query) use ($timestamp) {
                $query->where('deadline', '>=', $timestamp)->whereOr('deadline', 0);
            })
            ->limit($ad_center_category['ad_num'])
            ->order('sort_id desc,id desc')
            ->select();

        $index_list['top_ad_list'] = $AdModel->process($top_ad_list);
        $index_list['center_ad_list'] = $AdModel->process($center_ad_list);
        $ArticleModel = new CityinfoArticle();
        $where['is_recommend'] = 1;
        $article_list = $ArticleModel
            ->where($where)
            ->order('refreshtime desc')
            ->select();
        $index_list['article_list'] = $article_list;
        return $this->ajaxReturn(200, '', $index_list);
    }

    public function article_share() {
        $id = input('get.id/d', 0, 'intval');
        if (!$id) {
            $this->ajaxReturn(500, 'ID为空');
        }
        $ArticleModel = new CityinfoArticle();
        $article_info = $ArticleModel->where(['id' => $id])->find();
        if ($article_info === null) {
            $this->ajaxReturn(500, '内容信息为空');
        }
        $ArticleModel->where(['id' => $id])->setInc('share_times', 1);
        return $this->ajaxReturn(200, '', $article_info['share_times'] + 1);
    }
    public function article_info() {
        $id = input('get.id/d', 0, 'intval');
        if (!$id) {
            $this->ajaxReturn(500, 'ID为空');
        }

        $ArticleModel = new CityinfoArticle();
        $article_info = $ArticleModel->where(['id' => $id])->find(); //文章有是否可见,是否已审核,是否过了endtime,是否是自己看自己的内容,是否展示联系方式,是否是已付费观看本内容
        if ($article_info === null) {
            $this->ajaxReturn(500, '内容信息为空');
        }

        $savedata['article_id'] = $id;
        $ViewLogModel = new CityinfoViewLog();
        if (!$this->userinfo) {
            $savedata['uid'] = 0; //用户可能未登录
            $ViewLogModel->save($savedata);
            $ArticleModel->where(['id' => $id])->setInc('view_times', 1);
        } else {
            $savedata['uid'] = $this->userinfo->uid; //用户可能未登录
            if ($article_info['uid'] != $this->userinfo->uid) {
                $ViewLogModel->save($savedata);
                $ArticleModel->where(['id' => $id])->setInc('view_times', 1);
            }
        }
        if ($this->userinfo && $article_info['uid'] == $this->userinfo->uid) {
            $article = $article_info->toArray();
        } else {
            if ($article_info['endtime'] < time() || $article_info['audit'] != 1 || $article_info['is_public'] != 1) {
                $article = [];
            } else {
                $article = $article_info->toArray();
            }
        }
        $CityinfoModel = new CityinfoType();
        $type_name = $CityinfoModel->where(['id' => $article_info['type_id']])->find();
        $article['type_name'] = $type_name['title'];
        $p_type = $CityinfoModel->getPInfoByTypeId($article_info['type_id']);
        $article['pid'] = $p_type['id'];
        $article['pay_for_mobile'] = $p_type['pay_for_mobile'];
        $article['need_pay_for_mobile'] = $p_type['need_pay_for_mobile'];

        if ($this->userinfo) {
            $order_where['item_id'] = $article_info['id'];
            $order_where['uid'] = $this->userinfo->uid;
            $order_where['status'] = 1;
            $order_where['type'] = CityinfoOrder::TYPE_VIEW_ARTICLE;
            $OrderModel = new CityinfoOrder();
            $order_res = $OrderModel->where($order_where)->find();
            if ($order_res) {
                $article['paid'] = 1;
            } else {
                $article['paid'] = 0;
            }
        } else {
            $article['paid'] = 0;
        }

        if ($article['paid'] == 0 && $article['need_pay_for_mobile'] == 1 && $article['pay_for_mobile'] != 0) {
            if ($this->userinfo) {
                if ($this->userinfo->uid === $article_info['uid']) {
                    $article['is_hide'] = 0;
                } else {
                    $article['mobile'] = substr($article_info['mobile'], 0, 4) . '****' . substr($article_info['mobile'], -4, 4);
                    $article['is_hide'] = 1;
                }
            } else {
                $article['mobile'] = substr($article_info['mobile'], 0, 4) . '****' . substr($article_info['mobile'], -4, 4);
                $article['is_hide'] = 1;
            }
        } else {
            $article['is_hide'] = 0;
        }
        if ($article_info['endtime'] > time()) {
            $article['is_end'] = 0;
        } else {
            $article['is_end'] = 1;
        }

        $ArticleBodyModel = new CityinfoArticleBody();
        $article_info_content = $ArticleBodyModel->where(['article_id' => $id])->find();
        $article['content'] = $article_info_content['content'];

        $article['refreshtime'] = daterange(time(), $article_info['refreshtime']);
        $ArticleModel->processArticleDetail($article);

        $AdCategoryModel = new CityinfoAdCategory();
        $where_ad_category['alias'] = 'QS_cityinfo_detail_bottom';
        $ad_category = $AdCategoryModel->where($where_ad_category)->find();

        $AdModel = new CityinfoAd();
        $timestamp = time();
        $ad_list = $AdModel
            ->where('is_display', 1)
            ->where('cid', $ad_category['id'])
            ->where('starttime', '<=', $timestamp)
            ->where(function ($query) use ($timestamp) {
                $query->where('deadline', '>=', $timestamp)->whereOr('deadline', 0);
            })
            ->limit($ad_category['ad_num'])
            ->order('sort_id desc,id desc')
            ->select();

        $article['ad_list'] = $AdModel->process($ad_list);
        $article['cityinfo_title'] = config('global_config.cityinfo_title');
        return $this->ajaxReturn(200, '', $article);
    }
    public function article_info_content() {
        $id = input('get.id/d', 0, 'intval');
        if (!$id) {
            $this->ajaxReturn(500, 'ID为空');
        }
        $ArticleBodyModel = new CityinfoArticleBody();
        $article_info_content = $ArticleBodyModel->where(['article_id' => $id])->find();
        return $this->ajaxReturn(200, '', $article_info_content);
    }
    public function phone_book_type() {
        $m = new CityinfoPhoneBookType();
        $this->ajaxReturn(200, 'ok', $m->getValidAll());
    }
    public function info_promo() {
        $list = config('global_config.cityinfo_promote_set');
        foreach ($list as $k => &$v) {
            $v['index'] = $k;
            if ($v['fee']) {
                $v['fee'] = number_format($v['fee'], 2);
                $v['tip'] = sprintf('低至%.1f元/天', number_format($v['fee'] / $v['days'], 1));
            } else {
                $v['tip'] = '免费';
            }
        }
        $this->ajaxReturn(200, '', $list);
    }
    public function phone_book_addsave() {
        $phoneBookModel = new CityinfoPhoneBook();
        $input_data = [
            'name' => input('post.name/s', '', 'trim,badword_filter'),
            'lat' => input('post.lat/s', '', 'trim'),
            'lon' => input('post.lon/s', '', 'trim'),
            'type_id' => input('post.type_id/d', 0, 'intval'),
            'mobile' => input('post.mobile/s', '', 'trim,badword_filter'),
            'telephone' => input('post.telephone/s', '', 'trim'),
            'weixin' => input('post.weixin/s', '', 'trim'),
            'qrcode' => input('post.qrcode/d', 0, 'intval'),
            'is_sys' => 0,
            'audit' => 0,
            'logo' => input('post.logo/d', 0, 'intval')
        ];
        $validate = new \app\common\validate\CityinfoPhoneBook();
        if (!$validate->check($input_data)) {
            $this->ajaxReturn(500, $validate->getError(), $input_data);
        }
        $r = $phoneBookModel->save($input_data);
        $input_data['id'] = $phoneBookModel->getLastInsID();
        $this->ajaxReturn($r !== false ? 200 : 500, '', $input_data);
    }
    public function phone_book_info() {
        $id = input('get.id/d', 0, 'intval');
        if (!$id) {
            $this->ajaxReturn(500, 'ID为空');
        }
        $phoneBookModel = new CityinfoPhoneBook();
        $UploadfileModel = new Uploadfile();
        $phone_book_info = $phoneBookModel->where(['id' => $id])->find();
        if ($phone_book_info === null) {
            $this->ajaxReturn(500, '内容信息为空');
        }
        $phone_book = $phone_book_info;
        if ($phone_book_info['logo']) {
            $phone_book['logo_url'] = $UploadfileModel->getFileUrl($phone_book_info['logo']);
        } else {
            $phone_book['logo_url'] = default_empty('logo');
        }
        $phone_book['qrcode_url'] = $UploadfileModel->getFileUrl($phone_book_info['qrcode']);
        return $this->ajaxReturn(200, '', $phone_book);
    }
    public function phone_book_list() {
        $type_id = input('get.type_id/d', 0, 'intval');
        $keywords = input('get.keywords/s', '', 'trim');
        $page = input('get.page/d', 1, 'intval');
        $pageSize = input('get.pagesize/d', 20, 'intval');
        $where['audit'] = 1;
        if ($type_id) {
            $where['type_id'] = $type_id;
        }
        if ($keywords) {
            $where['name|telephone'] = array('like', '%' . $keywords . '%');
        }
        $phoneBookModel = new CityinfoPhoneBook();
        $UploadfileModel = new Uploadfile();
        $list = $phoneBookModel
            ->where($where)
            ->order('sort_id desc,addtime desc')
            ->limit(($page - 1) * $pageSize, $pageSize)
            ->select();
        $img = [];
        foreach ($list as $key => $val) {
            $img[] = $val['qrcode'];
            $img[] = $val['logo'];
        }
        $imgurl = $UploadfileModel->getFileUrlBatch($img);
        $imgurl[0] = default_empty('logo');
        foreach ($list as $key => $val) {
            $list[$key]['qrcode_url'] = $imgurl[$val['qrcode']];
            $list[$key]['logo_url'] = $imgurl[$val['logo']];
        }
        $phone_book_list['list'] = $list;
        return $this->ajaxReturn(200, '', $phone_book_list);
    }
    public function authMobileCheck() {
        $phone_book_id = input('post.phone_book_id/d', '', 'intval');
        $mobile = input('post.mobile/s', '', 'trim');
        $verify = input('post.verify/s', '', 'trim');
        $cache_arr = cache('smscode_' . $mobile);
        if ($verify === $cache_arr['code']) {
            $phoneBookModel = new CityinfoPhoneBook();
            $res = $phoneBookModel
                ->where(array('id' => $phone_book_id))
                ->update(array('publish_tel' => $mobile));
            $this->ajaxReturn(200, '登记成功', $res);
        } else {
            $this->ajaxReturn(500, '验证码错误');
        }
    }
}
