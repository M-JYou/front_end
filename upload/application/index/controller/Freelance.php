<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/4/25
 * Time: 15:51
 */

namespace app\index\controller;


use app\common\lib\Pager;
use app\common\model\BaseModel;
use app\common\model\FreelanceOrder;
use app\common\model\FreelanceResume;
use app\common\model\FreelanceSearchResume;
use app\common\model\FreelanceSearchSubject;
use app\common\model\FreelanceSubject;
use app\common\model\FreelanceVisitHistory;

class Freelance extends Base {
    public function index() {
        if (is_mobile_request() === true) {
            $this->redirect(config('global_config.mobile_domain') . 'freelance/index', 302);
            exit;
        }
        $cacheName = 'freelanceindex';
        $pageCache = model('Page')->getCache($cacheName);
        //如果缓存有效期为0，则不使用缓存
        if ($pageCache['expire'] > 0) {
            $return = model('Page')->getCacheByAlias($cacheName);
        } else {
            $return = false;
        }
        if (!$return) {
            $return = [
                'adlist' =>  $this->getAdList(),
                'latestSubject' => $this->latestSubject(),
                'latestResume' => $this->latestResume(),
            ];
            $this->writeShowCache($pageCache, $return);
        }
        foreach ($return as $key => $value) {
            $this->assign($key, $value);
        }
        $this->initPageSeo('freelanceindex');
        $this->assign('pageHeader', $this->pageHeader);
        $this->assign('navSelTag', __FUNCTION__);
        return view('index');
    }

    public function resume() {
        $search = new FreelanceSearchResume();
        $resume = new FreelanceResume();
        $keyword = input('get.keyword/s', '', 'trim,addslashes');
        $page = input('get.page/d', 1, 'intval');
        $pageSize = input('get.pagesize/d', 20, 'intval');

        $where = ['audit' => 1, 'is_public' => 1, 'is_published' => 1];
        if ($keyword) {
            $s = $search->search($keyword, $page, $pageSize);
            $list = $s['list'];
            $total = $s['total'];
        } else {
            $list = $resume->with('skills.skill,services')->where($where)->limit(($page - 1) * $pageSize, $pageSize)->order('is_top desc, refreshtime desc')->select();
            $total = $resume->where($where)->count();
        }

        $hot = $resume->getHotList();
        $list = $resume->processResumeData($list);
        $pager = new Pager($list, $pageSize, $page, $total);
        $seoData = ['keyword' => $keyword];
        $this->initPageSeo('freelanceresumelist', $seoData);

        $this->assign('share_url', config('global_config.mobile_domain') . 'freelance/resume');
        $this->assign('hot', $hot);
        $this->assign('pager', $pager);
        $this->assign('pageHeader', $this->pageHeader);
        $this->assign('navSelTag', __FUNCTION__);
        return view('resume');
    }

    public function subject() {
        $search = new FreelanceSearchSubject();
        $subject = new FreelanceSubject();
        $keyword = input('get.keyword/s', '', 'trim,addslashes');
        $page = input('get.page/d', 1, 'intval');
        $pageSize = input('get.pagesize/d', 20, 'intval');

        $where = ['audit' => 1, 'is_public' => 1, 'is_published' => 1];
        if ($keyword) {
            $s = $search->search($keyword, $page, $pageSize);
            $list = $s['list'];
            $total = $s['total'];
        } else {
            $list = $subject->where($where)->limit(($page - 1) * $pageSize, $pageSize)->order('is_top desc,refreshtime desc')->select();
            $total = $subject->where($where)->count();
        }

        $hot = $subject->getHotList();
        $list = $subject->processData($list);
        $pager = new Pager($list, $pageSize, $page, $total);
        $seoData = ['keyword' => $keyword];
        $this->initPageSeo('freelancesubjectlist', $seoData);

        $this->assign('share_url', config('global_config.mobile_domain') . 'freelance/project');
        $this->assign('hot', $hot);
        $this->assign('pager', $pager);
        $this->assign('pageHeader', $this->pageHeader);
        $this->assign('navSelTag', __FUNCTION__);
        return view('subject');
    }
    protected function writeFreelanceResumeShowCache($id, $pageCache) {
        $return = model('FreelanceResume')->getDetail($id);
        if (!$return) {
            return false;
        }
        if ($pageCache['expire'] > 0) {
            model('Page')->writeCacheByAlias('freelanceresumeshow', $return, $pageCache['expire'], $id);
        }
        return $return;
    }

    public function resume_show() {
        $resumeModel = new FreelanceResume();
        $id = request()->route('id/d', 0, 'intval');
        $cacheName = 'freelanceresumeshow';
        $viewFee = intval(config('global_config.freelance_view_resume_fee') * 100);
        $history = new FreelanceVisitHistory();
        $oModel = new FreelanceOrder();
        $pageCache = model('Page')->getCache($cacheName);
        $uid = $this->visitor ? $this->visitor['uid'] : 0;
        //如果缓存有效期为0，则不使用缓存
        if ($pageCache['expire'] > 0) {
            $row = model('Page')->getCacheByAlias($cacheName, $id);
        } else {
            $row = false;
        }
        if (!$row) {
            $row = $this->writeFreelanceResumeShowCache($id, $pageCache);
            if ($row === false) {
                abort(404, '页面不存在');
            }
        }

        $show = true;
        if ($viewFee > 0) { //浏览付费
            if ($uid) {
                if ($row['uid'] != $uid) {
                    $has = $oModel->hasPaid($uid, $id, FreelanceOrder::TYPE_VIEW_RESUME);
                    if (!$has) {
                        $show = false;
                    }
                }
            } else {
                $show = false;
            }
        }
        $history->record($uid, $row['uid'], $id, FreelanceVisitHistory::TYPE_RESUME);

        if (!$show) {
            unset($row['mobile'], $row['weixin']);
        }
        $row['show'] = $show;
        $row['share_url'] = config('global_config.mobile_domain') . 'freelance/resume/' . $id;

        $hot = $resumeModel->getHotList();
        $this->assign('hot', $hot);
        $seoData = ['name' => $row['name'], 'sex' => $row['gender'], 'education' => $row['education'], 'experience' => $row['exp_str'], 'brief_intro' => $row['brief_intro']];
        $this->initPageSeo($cacheName, $seoData);
        $this->assign('data', $row);
        $this->assign('pageHeader', $this->pageHeader);
        $this->assign('navSelTag', __FUNCTION__);
        return view('resume_show');
    }

    protected function writeFreelanceSubjectShowCache($id, $pageCache) {
        $return = model('FreelanceSubject')
            ->where('id', $id)
            ->field(true)
            ->find();
        if ($return === null) {
            return false;
        }
        if ($pageCache['expire'] > 0) {
            model('Page')->writeCacheByAlias('freelancesubjectshow', $return, $pageCache['expire'], $id);
        }
        return $return;
    }


    public function subject_show() {
        $subjectModel = new FreelanceSubject();
        $history = new FreelanceVisitHistory();
        $oModel = new FreelanceOrder();
        $id = request()->route('id/d', 0, 'intval');
        $cacheName = 'freelancesubjectshow';
        $viewFee = intval(config('global_config.freelance_view_subject_fee') * 100);
        $uid = $this->visitor ? $this->visitor['uid'] : 0;
        $pageCache = model('Page')->getCache($cacheName);

        if ($pageCache['expire'] > 0) {
            $row = model('Page')->getCacheByAlias($cacheName, $id);
        } else {
            $row = false;
        }
        if (!$row) {
            $row = $this->writeFreelanceSubjectShowCache($id, $pageCache);
            if ($row === false) {
                abort(404, '页面不存在');
            }
        }
        if (!$row['is_public'] || !$row['is_published'] || ($row['audit'] != 1)) {
            abort(404, '页面不存在');
        }

        $row['view_fee'] = $viewFee / 100;
        $row['price'] /= 100;
        $row['endtime'] = date('Y-m-d', $row['endtime']);
        $row['refreshtime'] = daterange(time(),  $row['refreshtime']);
        $show = true;
        if ($viewFee > 0) {
            if ($uid) {
                if ($row['uid'] != $uid) {
                    $has = $oModel->hasPaid($uid, $id, FreelanceOrder::TYPE_VIEW_SUBJECT);
                    if (!$has) {
                        $show = false;
                    }
                }
            } else {
                $show = false;
            }
        }
        $history->record($uid, $row['uid'], $id, FreelanceVisitHistory::TYPE_SUBJECT);
        if (!$show) {
            unset($row['mobile'], $row['weixin']);
        }
        $row['show'] = $show;
        $row['share_url'] = config('global_config.mobile_domain') . 'freelance/project/' . $id;
        $hot = $subjectModel->getHotList();
        $this->assign('hot', $hot);
        $seoData = ['title' => $row['title'], 'price' => $row['price'] . '元', 'period' => $row['period'] . '天', 'desc' => $row['desc']];
        $this->initPageSeo($cacheName, $seoData);
        $this->assign('data', $row);
        $this->assign('pageHeader', $this->pageHeader);
        $this->assign('navSelTag', __FUNCTION__);
        return view('subject_show');
    }

    protected function initPageSeo($alias, $data = []) {
        $pageinfo = model('Page')->getCache($alias);
        $seo_title = $pageinfo['seo_title'];
        $seo_keywords = $pageinfo['seo_keywords'];
        $seo_description = $pageinfo['seo_description'];
        $seo_title = str_replace("{sitename}", config('global_config.sitename'), $seo_title);
        $seo_keywords = str_replace("{sitename}", config('global_config.sitename'), $seo_keywords);
        $seo_description = str_replace("{sitename}", config('global_config.sitename'), $seo_description);
        foreach ($data as $key => $value) {
            $seo_title = str_replace("{" . $key . "}", $value, $seo_title);
            $seo_keywords = str_replace("{" . $key . "}", $value, $seo_keywords);
            $seo_description = str_replace("{" . $key . "}", $value, $seo_description);
        }
        $this->pageHeader['title'] = $seo_title;
        $this->pageHeader['keywords'] = $seo_keywords;
        $this->pageHeader['description'] = $seo_description;
    }
    protected function writeShowCache($pageCache, $return) {
        if ($pageCache['expire'] > 0) {
            model('Page')->writeCacheByAlias($pageCache['alias'], $return, $pageCache['expire']);
        }
        return $return;
    }
    public function latestSubject() {
        $subjectM = new FreelanceSubject();
        return $subjectM->latestSubject(20);
    }
    public function latestResume() {
        $resumeM = new FreelanceResume();
        return $resumeM->latestResume(20);
    }
    public function getAdList() {
        $alias_arr = ['QS_freelance_top@web', 'QS_freelance_middle@web'];
        $category_arr = model('FreelanceAdCategory')->whereIn('alias', $alias_arr)->column('id,alias,ad_num', 'id');
        $cid_arr = array_keys($category_arr);
        $timestamp = time();
        $dataset = model('FreelanceAd')
            ->where('is_display', 1)
            ->whereIn('cid', $cid_arr)
            ->where('starttime', '<=', $timestamp)
            ->where(function ($query) use ($timestamp) {
                $query->where('deadline', '>=', $timestamp)->whereOr('deadline', 0);
            })
            ->order('sort_id desc,id desc')
            ->column('id,cid,title,imageid,imageurl,target,link_url,inner_link,inner_link_params,company_id');
        $image_id_arr = $image_arr = [];
        foreach ($dataset as $key => $value) {
            if ($value['imageid'] > 0) {
                $image_id_arr[] = $value['imageid'];
            }
        }
        if (!empty($image_id_arr)) {
            $image_arr = model('Uploadfile')->getFileUrlBatch($image_id_arr);
        }
        $list = [];
        $allCompanyId = [];
        foreach ($dataset as $key => $value) {
            $value['image_src'] = isset($image_arr[$value['imageid']]) ? $image_arr[$value['imageid']] : $value['imageurl'];
            if (isset($list[$category_arr[$value['cid']]['alias']]) && count($list[$category_arr[$value['cid']]['alias']]) >= $category_arr[$value['cid']]['ad_num']) {
                continue;
            }
            $arr = [];
            $arr['title'] = $value['title'];
            $arr['image_src'] = $value['image_src'];
            $arr['link_url'] = $value['link_url'];
            $arr['inner_link'] = $value['inner_link'];
            $arr['inner_link_params'] = $value['inner_link_params'];
            $arr['company_id'] = $value['company_id'];
            $arr['companyname'] = '';
            $arr['joblist'] = [];
            $arr['jobnum'] = 0;
            $arr['web_link_url'] = model('FreelanceAd')->handlerWebLink($value);
            $list[$category_arr[$value['cid']]['alias']][] = $arr;
            $value['company_id'] && $allCompanyId[] = $value['company_id'];
        }
        foreach ($category_arr as $key => $value) {
            if (!isset($list[$value['alias']])) {
                $list[$value['alias']] = [];
            }
        }
        return $list;
    }
}
