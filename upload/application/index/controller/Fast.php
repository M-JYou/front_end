<?php

namespace app\index\controller;

use app\common\lib\Pager;

class Fast extends \app\index\controller\Base {
    public function _initialize() {
        parent::_initialize();
        $this->assign('navSelTag', 'fast');
    }
    public function job() {
        if (is_mobile_request() === true) {
            $this->redirect(config('global_config.mobile_domain') . 'fast/joblist', 302);
            exit;
        }
        $keywords = input('get.keywords/s', '', 'trim');
        $current_page = input('get.page/d', 1, 'intval');
        $pagesize = input('get.pagesize/d', 20, 'intval');
        $where['audit'] = 1;
        if ($keywords) {
            $where['jobname|comname|content'] = array('like', '%' . $keywords . '%');
        }
        $timestamp = time();
        //$where['endtime'] = array('gt',time());
        $total = model('fastJob')
            ->where($where)
            ->where(function ($query) use ($timestamp) {
                $query->where('endtime', '>=', $timestamp)->whereOr('endtime', 0);
            })
            ->count();
        $list = model('fastJob')
            ->where($where)
            ->where(function ($query) use ($timestamp) {
                $query->where('endtime', '>=', $timestamp)->whereOr('endtime', 0);
            })
            ->order('is_top desc,refreshtime desc')
            ->paginate(['list_rows' => $pagesize, 'page' => $current_page, 'type' => '\\app\\common\\lib\\Pager'], $total);
        $pagerHtml = $list->render();
        foreach ($list as $key => $val) {
            $list[$key]['refreshtime_cn'] = daterange(time(), $val['refreshtime']);
            $list[$key]['addtime_cn'] = daterange(time(), $val['addtime']);
        }
        $seoData = ['keywords' => $keywords];
        $this->initPageSeo('fastjoblist', $seoData);
        $this->assign('list', $list);
        $this->assign('pagerHtml', $pagerHtml);
        $this->assign('pageHeader', $this->pageHeader);
        return $this->fetch('job');
    }
    public function resume() {
        if (is_mobile_request() === true) {
            $this->redirect(config('global_config.mobile_domain') . 'fast/resumelist', 302);
            exit;
        }
        $keywords = input('get.keywords/s', '', 'trim');
        $current_page = input('get.page/d', 1, 'intval');
        $pagesize = input('get.pagesize/d', 20, 'intval');
        $where['audit'] = 1;
        if ($keywords) {
            $where['fullname|wantjob|content'] = array('like', '%' . $keywords . '%');
        }
        $timestamp = time();
        //$where['endtime'] = array('gt',time());
        $total = model('fastResume')
            ->where($where)
            ->where(function ($query) use ($timestamp) {
                $query->where('endtime', '>=', $timestamp)->whereOr('endtime', 0);
            })
            ->count();
        $list = model('fastResume')
            ->where($where)
            ->where(function ($query) use ($timestamp) {
                $query->where('endtime', '>=', $timestamp)->whereOr('endtime', 0);
            })
            ->order('is_top desc,refreshtime desc')
            ->paginate(['list_rows' => $pagesize, 'page' => $current_page, 'type' => '\\app\\common\\lib\\Pager'], $total);
        $pagerHtml = $list->render();
        foreach ($list as $key => $val) {
            $list[$key]['sex_text'] = isset(model('FastResume')->map_sex[$val['sex']])
                ? model('FastResume')->map_sex[$val['sex']]
                : '';
            $list[$key]['experience_text'] = isset(
                model('BaseModel')->map_experience[$val['experience']]
            )
                ? model('BaseModel')->map_experience[$val['experience']]
                : '';
            $list[$key]['refreshtime_cn'] = daterange(time(), $val['refreshtime']);
            $list[$key]['addtime_cn'] = daterange(time(), $val['addtime']);
        }
        $seoData = ['keywords' => $keywords];
        $this->initPageSeo('fastresumelist', $seoData);
        $this->assign('list', $list);
        $this->assign('pagerHtml', $pagerHtml);
        $this->assign('pageHeader', $this->pageHeader);
        return $this->fetch('resume');
    }
}
