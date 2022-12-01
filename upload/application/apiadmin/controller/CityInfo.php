<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/5/6
 * Time: 17:29
 */

namespace app\apiadmin\controller;


use app\common\model\AdminLog;
use app\common\model\CityinfoArticle;
use app\common\model\CityinfoArticleBody;
use app\common\model\CityinfoFeedback;
use app\common\model\CityinfoOrder;
use app\common\model\CityinfoPhoneBook;
use app\common\model\CityinfoPhoneBookType;
use app\common\model\CityinfoSearchArticle;
use app\common\model\CityinfoType;
use app\common\model\Uploadfile;
use think\Request;

class Cityinfo extends \app\common\controller\Backend {
    public function article_type_list() {
        $m = new CityinfoType();
        $this->ajaxReturn(200, 'ok', $m->getNoCache());
    }

    public function article_type_all() {
        $m = new CityinfoType();
        $this->ajaxReturn(200, 'ok', $m->getAll());
    }

    public function save_pay_for_mobile() {
        $m = new CityinfoType();
        $id = input('post.id/d', 0, 'intval');
        $pay_for_mobile = input('post.pay_for_mobile/d', 0, 'intval');
        $r = $m->save(['pay_for_mobile' => $pay_for_mobile], ['id' => $id]);
        $this->ajaxReturn(200, 'ok', $r);
    }

    public function save_pay_for_create() {
        $m = new CityinfoType();
        $id = input('post.id/d', 0, 'intval');
        $pay_for_create = input('post.pay_for_create/a', []);
        $r = $m->save(['pay_for_create' => json_encode($pay_for_create)], ['id' => $id]);
        $this->ajaxReturn(200, 'ok', $r);
    }

    public function save_need_pay_for_create() {
        $m = new CityinfoType();
        $id = input('post.id/d', 0, 'intval');
        $need_pay_for_create = input('post.need_pay_for_create/b', false);
        $r = $m->save(['need_pay_for_create' => (int)$need_pay_for_create], ['id' => $id]);
        $this->ajaxReturn(200, 'ok', $r);
    }
    public function save_need_pay_for_mobile() {
        $m = new CityinfoType();
        $id = input('post.id/d', 0, 'intval');
        $need_pay_for_mobile = input('post.need_pay_for_mobile/b', false);
        $r = $m->save(['need_pay_for_mobile' => (int)$need_pay_for_mobile], ['id' => $id]);
        $this->ajaxReturn(200, 'ok', $r);
    }

    public function type_list() {
        $pid = input('get.pid/d', 0, 'intval');
        $model = new CityinfoType();
        $list = $model
            ->where('pid', $pid)
            ->order('sort_id desc,id asc')
            ->select();
        foreach ($list as $key => $value) {
            $children = $model->where(['pid' => $value['id']])->count();
            $list[$key]['hasChildren'] = $children ? true : false;
        }
        $this->ajaxReturn(200, '获取数据成功', $list);
    }

    public function type_del() {
        $id = input('post.id/d', 0, 'intval');
        if ((new CityinfoArticle())->where('type_id', $id)->count() > 0) {
            $this->ajaxReturn(500,  '本分类下已有信息');
        }
        $model = new CityinfoType();
        $r = $model->where(['id' => $id])->delete();
        $this->ajaxReturn($r ? 200 : 500, $r ? '删除成功' : '删除失败');
    }
    public function type_hide() {
        $id = input('post.id/d', 0, 'intval');
        $model = new CityinfoType();
        $r = $model->save(['is_display' => 0], ['id' => $id]);
        $this->ajaxReturn($r ? 200 : 500, $r ? '操作成功' : '操作失败');
    }
    public function type_show() {
        $id = input('post.id/d', 0, 'intval');
        $model = new CityinfoType();
        $r = $model->save(['is_display' => 1], ['id' => $id]);
        $this->ajaxReturn($r ? 200 : 500, $r ? '操作成功' : '操作失败');
    }
    public function type_save() {
        $input_data = [
            'pid' =>  input('post.pid/d', '0', 'intval'),
            'title' => input('post.title/s', '', 'trim'),
            'sort_id' => input('post.sort_id/d', 0, 'intval'),
            'logo'  =>  input('post.logo/d', '0', 'intval')
        ];
        $id = input('post.id/d', 0, 'intval');
        $model = new CityinfoType();
        if ($id) {
            $result = $model->where(['id' => $id])->update($input_data);
        } else {
            $input_data['is_sys'] = 0;
            $input_data['pay_for_create'] = json_encode([['days' => 7, 'fee' => 2, 'sys' => 1], ['days' => 15, 'fee' => 3, 'sys' => 1]]);
            $result = $model->save($input_data);
        }

        if (false === $result) {
            $this->ajaxReturn(500, $model->getError());
        }
        model('AdminLog')->record(
            ($id ? '编辑' : '添加') . '同城信息分类。分类ID【' .
                ($id ? $id : $model->getLastInsID()) .
                '】;分类名称【' .
                $input_data['title'] .
                '】',
            $this->admininfo
        );
        cache('cache_city_info_type', null);
        $this->ajaxReturn(200, '保存成功');
    }

    public function type() {
        $id = input('get.id/d', 0, 'intval');
        $model = new CityinfoType();
        $return = $model->find($id);
        $return['logo_url'] = (new Uploadfile())->getFileUrl($return['logo']);
        $this->ajaxReturn(200, '获取数据成功', $return);
    }

    public function type_save_all() {
        $inputdata = input('post.');
        $model = new CityinfoType();
        if (!$inputdata) {
            $this->ajaxReturn(500, '提交数据为空');
        }
        $sqldata = [];
        foreach ($inputdata as $key => $value) {
            if (!$value['id']) {
                continue;
            }
            $arr['id'] = $value['id'];
            $arr['sort_id'] = $value['sort_id'] == '' ? 0 : $value['sort_id'];
            $arr['pid'] = $value['pid'];
            $arr['title'] = $value['title'];
            $sqldata[] = $arr;
        }
        $validate = new \app\common\validate\CityinfoType();
        foreach ($sqldata as $key => $value) {
            if (!$validate->check($value)) {
                $this->ajaxReturn(500, $validate->getError());
            }
        }
        $r = $model->isUpdate()->saveAll($sqldata);
        model('AdminLog')->record('批量保存同城信息分类', $this->admininfo);
        cache('cache_city_info_type', null);
        $this->ajaxReturn($r ? 200 : 500, $r ? '保存成功' : '保存失败');
    }

    public function phone_book_type_list() {
        $m = new CityinfoPhoneBookType();
        $up = new Uploadfile();
        $list = $m->select();
        $up->getFileUrlBatch2($list, 'logo', 'logo_url');
        $this->ajaxReturn(200, 'ok', $list);
    }
    public function phone_book_type_hide() {
        $id = input('post.id/d', 0, 'intval');
        $model = new CityinfoPhoneBookType();
        $r = $model->save(['is_display' => 0], ['id' => $id]);
        $this->ajaxReturn($r ? 200 : 500, $r ? '操作成功' : '操作失败');
    }
    public function phone_book_type_show() {
        $id = input('post.id/d', 0, 'intval');
        $model = new CityinfoPhoneBookType();
        $r = $model->save(['is_display' => 1], ['id' => $id]);
        $this->ajaxReturn($r ? 200 : 500, $r ? '操作成功' : '操作失败');
    }
    public function phone_book_type_save() {
        $input_data = [
            'logo' => input('post.logo/d', 0, 'intval'),
            'title' => input('post.title/s', '', 'trim'),
            'sort_id' => input('post.sort_id/d', 0, 'intval')
        ];
        $id = input('post.id/d', 0, 'intval');
        $model = new CityinfoPhoneBookType();
        if ($id) {
            $result = $model->save($input_data, ['id' => $id]);
        } else {
            $result = false;
        }

        if (false === $result) {
            $this->ajaxReturn(500, $model->getError());
        }
        model('AdminLog')->record(
            ($id ? '编辑' : '添加') . '同城信息分类。分类ID【' .
                ($id ? $id : $model->getLastInsID()) .
                '】;分类名称【' .
                $input_data['title'] .
                '】',
            $this->admininfo
        );
        $this->ajaxReturn(200, '保存成功');
    }
    public function phone_book_type_save_all() {
        $inputdata = input('post.');
        $model = new CityinfoPhoneBookType();
        if (!$inputdata) {
            $this->ajaxReturn(500, '提交数据为空');
        }
        $sqldata = [];
        foreach ($inputdata as $key => $value) {
            if (!$value['id']) {
                continue;
            }
            $arr['id'] = $value['id'];
            $arr['sort_id'] = $value['sort_id'] == '' ? 0 : $value['sort_id'];
            $arr['title'] = $value['title'];
            $arr['logo'] = $value['logo'];
            $sqldata[] = $arr;
        }
        $validate = new \app\common\validate\CityinfoPhoneBookType();
        foreach ($sqldata as $key => $value) {
            if (!$validate->check($value)) {
                $this->ajaxReturn(500, $validate->getError());
            }
        }
        $r = $model->isUpdate()->saveAll($sqldata);
        model('AdminLog')->record('批量保存同城电话本分类', $this->admininfo);
        $this->ajaxReturn($r ? 200 : 500, $r ? '保存成功' : '保存失败');
    }
    public function phone_book_type() {
        $id = input('get.id/d', 0, 'intval');
        $model = new CityinfoPhoneBookType();
        $return = $model->find($id);
        $return['logo_url'] = model('Uploadfile')->getFileUrl($return['logo']);
        $this->ajaxReturn(200, '获取数据成功', $return);
    }

    public function phone_book_list() {
        $m = new CityinfoPhoneBook();
        $key_type = input('get.key_type/d', 1, 'intval');
        $key = input('get.key/s', '', 'trim');
        $current_page = input('get.page/d', 1, 'intval');
        $pagesize = input('get.pageSize/d', 15, 'intval');
        $audit = input('get.audit/d', -1, 'intval');
        $type_id = input('get.type_id/d', 0, 'intval');
        $data = $m->getList($type_id, $audit, $key, $key_type, $current_page, $pagesize);
        $this->ajaxReturn(200, 'ok', $data);
    }
    public function phone_book_save() {
        $input_data = [
            'logo' => input('post.logo/d', 0, 'intval'),
            'name' => input('post.name/s', '', 'trim'),
            'mobile' => input('post.mobile/s', '', 'trim'),
            'telephone' => input('post.telephone/s', '', 'trim'),
            'publish_tel' => input('post.publish_tel/s', '', 'trim'),
            'weixin' => input('post.weixin/s', '', 'trim'),
            'qrcode' => input('post.qrcode/d', '0', 'intval'),
            'type_id' => input('post.type_id/d', 0, 'intval'),
            'sort_id' => input('post.sort_id/d', 0, 'intval'),
            'address_detail' => input('post.address_detail/s', '', 'trim'),
        ];
        $id = input('post.id/d', 0, 'intval');
        $model = new CityinfoPhoneBook();
        $valid = new \app\common\validate\CityinfoPhoneBook();
        if (!$valid->check($input_data)) {
            $this->ajaxReturn(500, $valid->getError());
        }
        if ($id) {
            $result = $model->save($input_data, ['id' => $id]);
        } else {
            $input_data['is_sys'] = 1;
            $input_data['audit'] = 0;
            $result = $model->save($input_data);
        }

        if (false === $result) {
            $this->ajaxReturn(500, $model->getError());
        }
        model('AdminLog')->record(
            ($id ? '编辑' : '添加') . '同城电话本。电话本ID【' .
                ($id ? $id : $model->getLastInsID()) .
                '】;电话本名称【' .
                $input_data['name'] .
                '】',
            $this->admininfo
        );
        $this->ajaxReturn(200, '保存成功');
    }
    public function phone_book_audit() {
        $id = input('post.id/a');
        $audit = input('post.audit/d', 0, 'intval');
        $reason = input('post.reason/s', '', 'trim');
        if (empty($id)) {
            $this->ajaxReturn(500, '请选择');
        }

        $model = new CityinfoPhoneBook();
        $adminLog = new AdminLog();
        $model->setAudit($id, $audit, $reason);
        $adminLog->record(
            '将同城电话本审核状态变更为【' .
                $model->map_audit[$audit] .
                '】。电话本ID【' .
                implode(',', $id) .
                '】',
            $this->admininfo
        );
        $this->ajaxReturn(200, '审核成功');
    }
    public function phone_book_del() {
        $id = input('post.id/a');
        if (empty($id)) {
            $this->ajaxReturn(500, '请选择');
        }
        $model = new CityinfoPhoneBook();
        $adminLog = new AdminLog();
        $model->delAll($id);
        $adminLog->record('删除同城电话本。电话本ID【' . implode(",", $id) . '】', $this->admininfo);
        $this->ajaxReturn(200, '删除成功');
    }
    public function phone_book_detail() {
        $id = input('get.id/d', 0, 'intval');
        $model = new CityinfoPhoneBook();
        $up = new Uploadfile();
        $info = $model->find($id);
        $info['logo_url'] = $up->getFileUrl($info['logo']);
        $info['qrcode_url'] = $up->getFileUrl($info['qrcode']);
        $this->ajaxReturn(200, '', $info);
    }

    public function article_list() {
        $m = new CityinfoArticle();
        $type = input('get.key_type/d', 1, 'intval');
        $key = input('get.key/s', '', 'trim');
        $addtime = input('get.addtime/d', '0', 'trim');
        $current_page = input('get.page/d', 1, 'intval');
        $pagesize = input('get.pageSize/d', 15, 'intval');
        $is_public = input('get.is_public/d', -1, 'intval');
        $is_recommend = input('get.is_recommend/d', -1, 'intval');
        $audit = input('get.audit/d', -1, 'intval');
        $endtime = input('get.endtime/d', 0, 'intval');
        $type_id = input('get.type_id/a', []);

        $data = $m->getList($type_id, $addtime, $endtime, $is_public, $is_recommend, $audit, $key, $type, $current_page, $pagesize);
        $this->ajaxReturn(200, 'ok', $data);
    }
    public function article_audit() {
        $id = input('post.id/a');
        $audit = input('post.audit/d', 0, 'intval');
        $reason = input('post.reason/s', '', 'trim');
        if (empty($id)) {
            $this->ajaxReturn(500, '请选择');
        }

        $model = new CityinfoArticle();
        $adminLog = new AdminLog();
        $model->setAudit($id, $audit, $reason);
        $adminLog->record(
            '将同城信息审核状态变更为【' .
                $model->map_audit[$audit] .
                '】。信息ID【' .
                implode(',', $id) .
                '】',
            $this->admininfo
        );
        $this->ajaxReturn(200, '审核成功');
    }

    public function article_edit() {
        $aModel = new CityinfoArticle();
        $bModel = new CityinfoArticleBody();
        $tModel = new CityinfoType();
        $sModel = new CityinfoSearchArticle();
        if (Request::instance()->method() == 'GET') {
            $id = input('get.id/d', 0, 'intval');
            $old = $aModel->find($id);
            $old['content'] = $bModel->where(['article_id' => $id])->value('content');
            $old['endtime'] = $old['endtime'] > 0 ? date('Y-m-d', $old['endtime']) : null;
            $this->ajaxReturn(200, '', $old);
        }
        $id = input('post.id/d', 0, 'intval');
        $content =  input('post.content/s', '', 'trim,badword_filter');

        $data = [
            'linkman'                   =>      input('post.linkman/s', '', 'trim'),
            'mobile'                    =>      input('post.mobile/s', '', 'trim'),
            'type_id'                   =>      input('post.type_id/d', 0, 'intval'),
            'title'                     =>      input('post.title/s', '', 'trim'),
            'desc'                      =>      mb_substr($content, 0, 100),
            'is_public'                 =>      input('post.is_public/d', 0, 'intval'),
            'endtime'                   =>      input('post.endtime/d', 0, 'strtotime')
        ];
        $validate = new \app\common\validate\CityinfoArticle();
        if (!$validate->check($data)) {
            $this->ajaxReturn(500, $validate->getError(), $data);
        }
        if (empty($data['title'])) {
            $data['title'] = mb_substr($content, 0, 20);
        }
        if ($id) {
            $old = $aModel->find($id);
            if (!$old) {
                $this->ajaxReturn(402, '数据不正确', $data);
            }
            $data['audit'] = $old['audit'];
            $r = $aModel->save($data, ['id' => $id]);
            $r1 = $bModel->save(['content' => $content], ['article_id' => $id]);
            $data['refreshtime'] = $old['refreshtime'];
            $data['id'] = $id;
        }
        $sModel->updateSearch($data);
        $this->ajaxReturn(200, '保存成功', $data);
    }
    public function article_del() {
        $id = input('post.id/a');
        if (empty($id)) {
            $this->ajaxReturn(500, '请选择');
        }
        $model = new CityinfoArticle();
        $adminLog = new AdminLog();
        $model->delAll($id);
        $adminLog->record('删除同城信息。信息ID【' . implode(",", $id) . '】', $this->admininfo);
        $this->ajaxReturn(200, '删除成功');
    }

    public function article_recommend() {
        $id = input('post.id/a');
        $recommend = input('post.recommend/d', 0, 'intval');

        if (empty($id)) {
            $this->ajaxReturn(500, '请选择');
        }
        $model = new CityinfoArticle();
        $adminLog = new AdminLog();
        $model->where('id', 'in', $id)->setField('is_recommend', $recommend ? 1 : 0);
        $adminLog->record(
            '将同城信息推荐状态变更为【' .
                $model->map_recommend[$recommend] .
                '】。信息ID【' .
                implode(',', $id) .
                '】',
            $this->admininfo
        );
        $this->ajaxReturn(200, '操作成功');
    }
    public function article_top() {
        $id = input('post.id/a');
        $top = input('post.top/d', 0, 'intval');

        if (empty($id)) {
            $this->ajaxReturn(500, '请选择');
        }
        $model = new CityinfoArticle();
        $adminLog = new AdminLog();
        $model->where('id', 'in', $id)->setField('is_top', $top ? 1 : 0);
        $adminLog->record(
            '将同城信息置顶状态变更为【' .
                $model->map_top[$top] .
                '】。信息ID【' .
                implode(',', $id) .
                '】',
            $this->admininfo
        );
        $this->ajaxReturn(200, '操作成功');
    }
    ////
    public function ad_list() {
        $where = [];
        $platform = input('get.platform/s', '', 'trim');
        $settr = input('get.settr/s', '', 'trim');
        $is_display = input('get.is_display/s', '', 'trim');
        $cid = input('get.cid/d', 0, 'intval');
        $key_type = input('get.key_type/d', 0, 'intval');
        $keyword = input('get.keyword/s', '', 'trim');
        $current_page = input('get.page/d', 1, 'intval');
        $pagesize = input('get.pagesize/d', 10, 'intval');
        if ($keyword && $key_type) {
            switch ($key_type) {
                case 1:
                    $where['a.title'] = ['like', '%' . $keyword . '%'];
                    break;
                case 2:
                    $where['a.id'] = ['eq', intval($keyword)];
                    break;
                default:
                    break;
            }
        }
        if ($is_display != '') {
            $where['a.is_display'] = ['eq', intval($is_display)];
        }
        if ($platform != '') {
            $where['b.platform'] = ['eq', $platform];
        }
        if ($cid > 0) {
            $where['a.cid'] = ['eq', $cid];
        }
        if ($settr == '0') {
            $where['a.deadline'] = [['neq', 0], ['lt', time()]];
        } elseif ($settr > 0) {
            $where['a.deadline'] = [
                ['neq', 0],
                ['elt', strtotime('+' . $settr . ' day')],
                ['gt', time()]
            ];
        }

        $total = model('CityinfoAd')->alias('a')->join(config('database.prefix') . 'cityinfo_ad_category b', 'a.cid=b.id', 'LEFT')
            ->where($where)
            ->count();
        $list = model('CityinfoAd')->alias('a')->field('a.*')->join(config('database.prefix') . 'cityinfo_ad_category b', 'a.cid=b.id', 'LEFT')
            ->where($where)
            ->order('a.id asc')
            ->page($current_page . ',' . $pagesize)
            ->select();
        $image_id_arr = $image_list = [];
        foreach ($list as $key => $value) {
            $value['imageid'] && ($image_id_arr[] = $value['imageid']);
        }
        if (!empty($image_id_arr)) {
            $image_list = model('Uploadfile')->getFileUrlBatch($image_id_arr);
        }
        $category_arr = model('CityinfoAdCategory')->getCache();
        foreach ($list as $key => $value) {
            $value['imageurl'] = isset($image_list[$value['imageid']])
                ? $image_list[$value['imageid']]
                : $value['imageurl'];
            $value['cname'] = isset($category_arr[$value['cid']]['name'])
                ? $category_arr[$value['cid']]['name']
                : '';
            $value['platform'] =
                isset($category_arr[$value['cid']]['platform']) &&
                isset(
                    model('BaseModel')->map_ad_platform[$category_arr[$value['cid']]['platform']]
                )
                ? model('BaseModel')->map_ad_platform[$category_arr[$value['cid']]['platform']]
                : '';
            $list[$key] = $value;
        }

        $return['items'] = $list;
        $return['total'] = $total;
        $return['current_page'] = $current_page;
        $return['pagesize'] = $pagesize;
        $return['total_page'] = ceil($total / $pagesize);
        $this->ajaxReturn(200, '获取数据成功', $return);
    }
    public function ad_add() {
        $input_data = [
            'title' => input('post.title/s', '', 'trim'),
            'cid' => input('post.cid/a', []),
            'imageid' => input('post.imageid/d', 0, 'intval'),
            'imageurl' => input('post.imageurl/s', '', 'trim'),
            'explain' => input('post.explain/s', '', 'trim'),
            'starttime' => input('post.starttime/s', '', 'trim'),
            'deadline' => input('post.deadline/s', '', 'trim'),
            'sort_id' => input('post.sort_id/d', 0, 'intval'),
            'target' => input('post.target/d', 0, 'intval'),
            'link_url' => input('post.link_url/s', '', 'trim'),
            'inner_link' => input('post.inner_link/s', '', 'trim'),
            'inner_link_params' => input(
                'post.inner_link_params/d',
                0,
                'intval'
            ),
            'company_id' => input('post.company_id/d', 0, 'intval'),
            'is_display' => input('post.is_display/d', 1, 'intval')
        ];
        if ($input_data['starttime']) {
            $input_data['starttime'] = strtotime($input_data['starttime']);
        }
        if ($input_data['deadline']) {
            $input_data['deadline'] = strtotime($input_data['deadline']);
        } else {
            $input_data['deadline'] = 0;
        }
        if ($input_data['target'] == 0) {
            $input_data['inner_link'] = '';
            $input_data['inner_link_params'] = 0;
            $input_data['company_id'] = 0;
        } elseif ($input_data['target'] == 1) {
            $input_data['link_url'] = '';
            $input_data['company_id'] = 0;
        } elseif ($input_data['target'] == 2) {
            $input_data['link_url'] = '';
            $input_data['inner_link'] = '';
            $input_data['inner_link_params'] = 0;
        }
        $cid_arr = $input_data['cid'];
        $input_data['cid'] = $cid_arr[1];
        if (
            false ===
            model('CityinfoAd')
            ->validate(true)
            ->allowField(true)
            ->save($input_data)
        ) {
            $this->ajaxReturn(500, model('CityinfoAd')->getError());
        }
        model('AdminLog')->record(
            '添加自由职业广告。广告ID【' .
                model('CityinfoAd')->id .
                '】;广告标题【' .
                $input_data['title'] .
                '】',
            $this->admininfo
        );
        $this->ajaxReturn(200, '保存成功');
    }
    public function ad_edit() {
        $id = input('get.id/d', 0, 'intval');
        if ($id) {
            $info = model('CityinfoAd')->find($id);
            if (!$info) {
                $this->ajaxReturn(500, '数据获取失败');
            }
            $info = $info->toArray();
            $ad_category = model('CityinfoAdCategory')
                ->where('id', $info['cid'])
                ->find();
            $info['cid'] = [$ad_category['platform'], $info['cid']];
            $imageSrc = model('Uploadfile')->getFileUrl($info['imageid']);
            $this->ajaxReturn(200, '获取数据成功', [
                'info' => $info,
                'imageSrc' => $imageSrc
            ]);
        } else {
            $input_data = [
                'id' => input('post.id/d', 0, 'intval'),
                'title' => input('post.title/s', '', 'trim'),
                'cid' => input('post.cid/a', []),
                'imageid' => input('post.imageid/d', 0, 'intval'),
                'imageurl' => input('post.imageurl/s', '', 'trim'),
                'explain' => input('post.explain/s', '', 'trim'),
                'starttime' => input('post.starttime/s', '', 'trim'),
                'deadline' => input('post.deadline/s', '', 'trim'),
                'sort_id' => input('post.sort_id/d', 0, 'intval'),
                'target' => input('post.target/d', 0, 'intval'),
                'link_url' => input('post.link_url/s', '', 'trim'),
                'inner_link' => input('post.inner_link/s', '', 'trim'),
                'inner_link_params' => input(
                    'post.inner_link_params/d',
                    0,
                    'intval'
                ),
                'company_id' => input('post.company_id/d', 0, 'intval'),
                'is_display' => input('post.is_display/d', 1, 'intval')
            ];
            $id = intval($input_data['id']);
            if (!$id) {
                $this->ajaxReturn(500, '请选择数据');
            }
            if ($input_data['starttime']) {
                $input_data['starttime'] = strtotime($input_data['starttime']);
            }
            if ($input_data['deadline']) {
                $input_data['deadline'] = strtotime($input_data['deadline']);
            } else {
                $input_data['deadline'] = 0;
            }
            if ($input_data['target'] == 0) {
                $input_data['inner_link'] = '';
                $input_data['inner_link_params'] = 0;
                $input_data['company_id'] = 0;
            } elseif ($input_data['target'] == 1) {
                $input_data['link_url'] = '';
                $input_data['company_id'] = 0;
            } elseif ($input_data['target'] == 2) {
                $input_data['link_url'] = '';
                $input_data['inner_link'] = '';
                $input_data['inner_link_params'] = 0;
            }
            $cid_arr = $input_data['cid'];
            $input_data['cid'] = $cid_arr[1];
            if (
                false ===
                model('CityinfoAd')
                ->validate(true)
                ->allowField(true)
                ->save($input_data, ['id' => $id])
            ) {
                $this->ajaxReturn(500, model('CityinfoAd')->getError());
            }
            model('AdminLog')->record(
                '编辑自由职业广告。广告ID【' .
                    $id .
                    '】;广告标题【' .
                    $input_data['title'] .
                    '】',
                $this->admininfo
            );
            $this->ajaxReturn(200, '保存成功');
        }
    }
    public function ad_del() {
        $id = input('post.id/a');
        if (!$id) {
            $this->ajaxReturn(500, '请选择数据');
        }
        $list = model('CityinfoAd')
            ->where('id', 'in', $id)
            ->column('title');
        model('CityinfoAd')->destroy($id);
        model('AdminLog')->record(
            '删除自由职业广告。广告ID【' .
                implode(',', $id) .
                '】;广告标题【' .
                implode(',', $list) .
                '】',
            $this->admininfo
        );
        $this->ajaxReturn(200, '删除成功');
    }
    public function innerLinkOptions() {
        $list = model('CityinfoAd')->innerLinks;
        $this->ajaxReturn(200, '获取数据成功', $list);
    }

    public function ad_cat_list() {
        $where = [];
        $current_page = input('get.page/d', 1, 'intval');
        $pagesize = input('get.pagesize/d', 10, 'intval');
        $total = model('CityinfoAdCategory')
            ->where($where)
            ->count();
        $list = model('CityinfoAdCategory')
            ->where($where)
            ->order('id asc')
            ->page($current_page . ',' . $pagesize)
            ->select();
        foreach ($list as $key => $value) {
            $list[$key]['platform'] = model('BaseModel')->map_ad_platform[$value['platform']];
        }
        $return['items'] = $list;
        $return['total'] = $total;
        $return['current_page'] = $current_page;
        $return['pagesize'] = $pagesize;
        $return['total_page'] = ceil($total / $pagesize);
        $this->ajaxReturn(200, '获取数据成功', $return);
    }
    public function ad_cat_add() {
        $input_data = [
            'name' => input('post.name/s', '', 'trim'),
            'alias' => input('post.alias/s', '', 'trim'),
            'ad_num' => input('post.ad_num/d', 0, 'intval'),
            'platform' => input('post.platform/s', '', 'trim'),
            'height' => input('post.height/d', 0, 'intval'),
            'width' => input('post.width/d', 0, 'intval'),
        ];
        if (
            false ===
            model('CityinfoAdCategory')
            ->validate(true)
            ->allowField(true)
            ->save($input_data)
        ) {
            $this->ajaxReturn(500, model('CityinfoAdCategory')->getError());
        }
        model('AdminLog')->record(
            '添加自由职业广告位。广告位ID【' .
                model('CityinfoAdCategory')->id .
                '】;广告位名称【' .
                $input_data['name'] .
                '】',
            $this->admininfo
        );
        $this->ajaxReturn(200, '保存成功');
    }
    public function ad_cat_edit() {
        $id = input('get.id/d', 0, 'intval');
        if ($id) {
            $info = model('CityinfoAdCategory')->find($id);
            if (!$info) {
                $this->ajaxReturn(500, '数据获取失败');
            }
            $this->ajaxReturn(200, '获取数据成功', ['info' => $info]);
        } else {
            $input_data = [
                'id' => input('post.id/d', 0, 'intval'),
                'name' => input('post.name/s', '', 'trim'),
                'alias' => input('post.alias/s', '', 'trim'),
                'ad_num' => input('post.ad_num/d', 0, 'intval'),
                'platform' => input('post.platform/s', '', 'trim'),
                'height' => input('post.height/d', 0, 'intval'),
                'width' => input('post.width/d', 0, 'intval'),
            ];
            $id = intval($input_data['id']);
            if (!$id) {
                $this->ajaxReturn(500, '请选择数据');
            }
            if (
                false ===
                model('CityinfoAdCategory')
                ->validate(true)
                ->allowField(true)
                ->save($input_data, ['id' => $id])
            ) {
                $this->ajaxReturn(500, model('CityinfoAdCategory')->getError());
            }
            model('AdminLog')->record(
                '编辑自由职业广告位。广告位ID【' .
                    $id .
                    '】;广告位名称【' .
                    $input_data['name'] .
                    '】',
                $this->admininfo
            );
            $this->ajaxReturn(200, '保存成功');
        }
    }
    public function ad_cat_del() {
        $id = input('post.id/a');
        if (!$id) {
            $this->ajaxReturn(500, '请选择数据');
        }
        $list = model('CityinfoAdCategory')
            ->where('id', 'in', $id)
            ->column('name');
        model('CityinfoAdCategory')->destroy($id);
        model('AdminLog')->record(
            '删除自由职业广告位。广告位ID【' .
                implode(',', $id) .
                '】;广告位名称【' .
                implode(',', $list) .
                '】',
            $this->admininfo
        );
        $this->ajaxReturn(200, '删除成功');
    }
    public function ad_cat_platform() {
        $list = model('CityinfoAdCategory')->map_ad_platform;
        $return = [];
        foreach ($list as $key => $value) {
            $arr['id'] = $key;
            $arr['name'] = $value;
            $return[] = $arr;
        }
        $this->ajaxReturn(200, '获取数据成功', $return);
    }
    public function ad_cat_tree() {
        $return = model('CityinfoAdCategory')->getTreeCache();
        $return = json_encode($return);
        $return = str_replace('id', 'value', $return);
        $return = json_decode($return, true);
        $this->ajaxReturn(200, '获取数据成功', $return);
    }

    public function feedback_list() {
        $m = new CityinfoFeedback();
        $current_page = input('get.page/d', 1, 'intval');
        $pagesize = input('get.pageSize/d', 15, 'intval');
        $status = input('get.status/d', -1, 'intval');
        $data = $m->getList($status, $current_page, $pagesize);
        $this->ajaxReturn(200, '', $data);
    }

    public function feedback_del() {
        $id = input('post.id/a');
        if (empty($id)) {
            $this->ajaxReturn(500, '请选择');
        }
        $m = new CityinfoFeedback();
        $adminLog = new AdminLog();
        $m->delAll($id);
        $adminLog->record('删除同城信息举报。信息ID【' . implode(",", $id) . '】', $this->admininfo);
        $this->ajaxReturn(200, '删除成功');
    }

    public function feedback_set_status() {
        $id = input('post.id/a');
        $status = 1;
        if (empty($id)) {
            $this->ajaxReturn(500, '请选择');
        }
        $m = new CityinfoFeedback();
        $adminLog = new AdminLog();
        $m->setStatus($id, $status);
        $adminLog->record(
            '将同城举报信息处理状态变更为【' .
                $m->map_status[$status] .
                '】。信息ID【' .
                implode(',', $id) .
                '】',
            $this->admininfo
        );
        $this->ajaxReturn(200, '处理成功');
    }

    public function order_list() {
        $model = new CityinfoOrder();
        $type = input('get.key_type/d', 1, 'intval');
        $key = input('get.key/s', '', 'trim');
        $pay_type = input('get.pay_type/s', 0, 'trim');
        $order_type = input('get.order_type/d', 0, 'intval');
        $status = input('get.status/d', -1, 'intval');
        $addtime = input('get.addtime/d', 0, 'intval');
        $paytime = input('get.paytime/d', 0, 'intval');
        $current_page = input('get.page/d', 1, 'intval');
        $pagesize = input('get.pageSize/d', 15, 'intval');
        $data = $model->getList($status, $type, $key, $pay_type, $order_type, $addtime, $paytime, $current_page, $pagesize);
        $this->ajaxReturn(200, '', $data);
    }

    public function order_pay() {
        $model = new CityinfoOrder();
        $id = input('post.id/d', 0, 'intval');
        $row = $model->where(['id' => $id])->find();
        if ($row) {
            $model->paid($row, 'wxpay', $row['amount'] / 100, md5(time()));
        }
        $this->ajaxReturn(200, '操作成功', $row);
    }
    public function order_close() {
        $model = new CityinfoOrder();
        $id = input('post.id/d', 0, 'intval');
        $row = $model->save(['status' => CityinfoOrder::STATUS_CLOSE], ['id' => $id]);
        $this->ajaxReturn(200, '操作成功', $row);
    }
}
