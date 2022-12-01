<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/4/8
 * Time: 13:54
 */

namespace app\apiadmin\controller;


use app\common\model\AdminLog;
use app\common\model\FreelanceOrder;
use app\common\model\FreelanceProject;
use app\common\model\FreelanceResume;
use app\common\model\FreelanceSkillType;
use app\common\model\FreelanceSubject;

class Freelance extends \app\common\controller\Backend {
    public function subject_list() {
        $type = input('get.key_type/d', 1, 'intval');
        $key = input('get.key/s', '', 'trim');
        $addtime = input('get.addtime/d', '0', 'trim');
        $current_page = input('get.page/d', 1, 'intval');
        $pagesize = input('get.pageSize/d', 15, 'intval');
        $is_public = input('get.is_public/d', -1, 'intval');
        $audit = input('get.audit/d', -1, 'intval');
        $endtime = input('get.endtime/d', 0, 'intval');

        $model = new FreelanceSubject();
        $data = $model->getList($addtime, $endtime, $is_public, $audit, $key, $type, $current_page, $pagesize);
        $this->ajaxReturn(200, '', $data);
    }

    public function subject_audit() {
        $id = input('post.id/a');
        $audit = input('post.audit/d', 0, 'intval');
        $reason = input('post.reason/s', '', 'trim');
        if (empty($id)) {
            $this->ajaxReturn(500, '请选择');
        }

        $model = new FreelanceSubject();
        $adminLog = new AdminLog();
        $model->setAudit($id, $audit, $reason);
        $adminLog->record(
            '将零工项目审核状态变更为【' .
                $model->map_audit[$audit] .
                '】。项目ID【' .
                implode(',', $id) .
                '】',
            $this->admininfo
        );
        $this->ajaxReturn(200, '审核成功');
    }

    public function resume_list() {
        $type = input('get.key_type/d', 1, 'intval');
        $key = input('get.key/s', '', 'trim');
        $audit = input('get.audit/d', -1, 'intval');
        $is_public = input('get.is_public/d', -1, 'intval');
        $current_page = input('get.page/d', 1, 'intval');
        $pagesize = input('get.pageSize/d', 15, 'intval');

        $model = new FreelanceResume();
        $data = $model->getList($audit, $is_public, $key, $type, $current_page, $pagesize);
        $this->ajaxReturn(200, '', $data);
    }

    public function order_list() {
        $model = new FreelanceOrder();
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
        $model = new FreelanceOrder();
        $id = input('post.id/d', 0, 'intval');
        $row = $model->where(['id' => $id])->find();
        if ($row) {
            $model->paid($row, 'wxpay', $row['amount'] / 100, md5(time()));
        }
        $this->ajaxReturn(200, '操作成功', $row);
    }
    public function order_close() {
        $model = new FreelanceOrder();
        $id = input('post.id/d', 0, 'intval');
        $row = $model->save(['status' => FreelanceOrder::STATUS_CLOSE, ['id' => $id]]);
        $this->ajaxReturn(200, '操作成功', $row);
    }
    public function subject_del() {
        $id = input('post.id/a');
        if (empty($id)) {
            $this->ajaxReturn(500, '请选择');
        }
        $model = new FreelanceSubject();
        $adminLog = new AdminLog();
        $model->delAll($id);
        $adminLog->record('删除零工项目。零工项目ID【' . implode(",", $id) . '】', $this->admininfo);
        $this->ajaxReturn(200, '删除成功');
    }

    public function subject_recommend() {
        $id = input('post.id/a');
        $recommend = input('post.recommend/d', 0, 'intval');

        if (empty($id)) {
            $this->ajaxReturn(500, '请选择');
        }
        $model = new FreelanceSubject();
        $adminLog = new AdminLog();
        $model->where('id', 'in', $id)->setField('is_recommend', $recommend ? 1 : 0);
        $adminLog->record(
            '将零工项目推荐状态变更为【' .
                $model->map_recommend[$recommend] .
                '】。项目ID【' .
                implode(',', $id) .
                '】',
            $this->admininfo
        );
        $this->ajaxReturn(200, '操作成功');
    }

    public function resume_del() {
        $id = input('post.id/a');
        if (empty($id)) {
            $this->ajaxReturn(500, '请选择');
        }
        $model = new FreelanceResume();
        $adminLog = new AdminLog();
        $model->delAll($id);
        $adminLog->record('删除零工简历。简历ID【' . implode(",", $id) . '】', $this->admininfo);
        $this->ajaxReturn(200, '删除成功');
    }

    public function resume_audit() {
        $id = input('post.id/a');
        $audit = input('post.audit/d', 0, 'intval');
        $reason = input('post.reason/s', '', 'trim');
        if (empty($id)) {
            $this->ajaxReturn(500, '请选择');
        }

        $model = new FreelanceResume();
        $adminLog = new AdminLog();
        $model->setAudit($id, $audit, $reason);
        $adminLog->record(
            '将零工简历审核状态变更为【' .
                $model->map_audit[$audit] .
                '】。简历ID【' .
                implode(',', $id) .
                '】',
            $this->admininfo
        );
        $this->ajaxReturn(200, '审核成功');
    }

    public function skill_type_list() {
        $pid = input('get.pid/d', 0, 'intval');
        $model = new FreelanceSkillType();
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

    public function skill_type_del() {
        $id = input('post.id/d', 0, 'intval');
        $model = new FreelanceSkillType();
        $r = $model->where(['id' => $id])->delete();
        $this->ajaxReturn($r ? 200 : 500, $r ? '删除成功' : '删除失败');
    }
    public function subject_save() {
        $model = new FreelanceSubject();
        $id = input('post.id/d', 0, 'intval');

        $data = [
            'title' => input('post.title/s', '', 'trim'),
            'price' => input('post.price/s', 0, 'trim'),
            'endtime' => input('post.endtime/s', '', 'trim'),
            'period' => input('post.period/d', 0, 'intval'),
            'desc' => input('post.desc/s', '', 'trim'),
            'linkman' => input('post.linkman/s', '', 'trim'),
            'mobile' => input('post.mobile/s', '', 'trim'),
            'weixin' => input('post.weixin/s', '', 'trim'),
        ];
        $data['endtime'] = strtotime($data['endtime']);
        $data['price'] = $data['price'] * 100;
        $validate = new \app\common\validate\FreelanceSubject();
        if (!$validate->check($data)) {
            $this->ajaxReturn(500, $validate->getError(), $data);
        }

        if ($id) {
            $r = $model->save($data, ['id' => $id]);
        }
        $this->ajaxReturn($r !== false ? 200 : 500, $r !== false ? '更新成功' : '更新失败', $data);
    }
    public function subject_info() {
        $model = new FreelanceSubject();
        $id = input('get.id/d', 0, 'intval');
        if (!$id) {
            $this->ajaxReturn(500, '参数错误');
        }
        $info = $model->find($id);
        if ($info) {
            $info['endtime'] = date('Y-m-d', $info['endtime']);
            $info['price'] = number_format($info['price'] / 100, 2, '.', '');
        }
        $this->ajaxReturn(200, '', $info);
    }
    public function skill_type_save() {
        $input_data = [
            'pid' => input('post.pid/a'),
            'title' => input('post.title/s', '', 'trim'),
            'sort_id' => input('post.sort_id/d', 0, 'intval')
        ];
        $id = input('post.id/d', 0, 'intval');
        $model = new FreelanceSkillType();
        $input_data['pid'] =
            isset($input_data['pid']) && is_array($input_data['pid'])
            ? (!empty($input_data['pid'])
                ? end($input_data['pid'])
                : 0)
            : 0;
        if ($id) {
            $result = $model->where(['id' => $id])->update($input_data);
        } else {
            $result = $model->save($input_data);
        }

        if (false === $result) {
            $this->ajaxReturn(500, $model->getError());
        }
        model('AdminLog')->record(
            ($id ? '编辑' : '添加') . '技能分类。分类ID【' .
                ($id ? $id : $model->getLastInsID()) .
                '】;分类名称【' .
                $input_data['title'] .
                '】',
            $this->admininfo
        );
        $this->ajaxReturn(200, '保存成功');
    }

    public function skill_type_tree() {
        $model = new FreelanceSkillType();
        $list = $model->getCache('0');
        $return = [];
        foreach ($list as $key => $value) {
            $arr = [];
            $arr['value'] = $key;
            $arr['label'] = $value;
            $children = $model->getCache($key);
            if ($children) {
                foreach ($children as $k => $v) {
                    $arr['children'][] = [
                        'value' => $k,
                        'label' => $v,
                    ];
                }
            }
            $return[] = $arr;
        }
        $this->ajaxReturn(200, '获取数据成功', $return);
    }

    public function skill_type() {
        $id = input('get.id/d', 0, 'intval');
        $model = new FreelanceSkillType();
        $return = $model->find($id);
        $this->ajaxReturn(200, '获取数据成功', $return);
    }

    public function skill_type_save_all() {
        $inputdata = input('post.');
        $model = new FreelanceSkillType();
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
        $validate = new \app\common\validate\FreelanceSkillType();
        foreach ($sqldata as $key => $value) {
            if (!$validate->check($value)) {
                $this->ajaxReturn(500, $validate->getError());
            }
        }
        $r = $model->isUpdate()->saveAll($sqldata);
        model('AdminLog')->record('批量保存技能分类', $this->admininfo);
        $this->ajaxReturn($r ? 200 : 500, $r ? '保存成功' : '保存失败');
    }
    public function resume_save() {
        $model = new FreelanceResume();
        $id = input('post.id/d', 0, 'intval');

        $data = [
            'age' => input('post.age/d', '0', 'intval'),
            'gender' => input('post.gender/d', 0, 'intval'),
            'education' => input('post.education/d', 0, 'intval'),
            'is_public' => input('post.is_public/d', 0, 'intval'),
            'brief_intro' => input('post.brief_intro/s', '', 'trim'),
            'professional_title' => input('post.professional_title/s', '', 'trim'),
            'start_work_date' => input('post.start_work_date/s', '', 'trim'),
            'living_city' => input('post.living_city/s', '', 'trim'),
            'mobile' => input('post.mobile/s', '', 'trim'),
            'weixin' => input('post.weixin/s', '', 'trim'),
            'name' => input('post.name/s', '', 'trim'),
            'hide_name' => input('post.hide_name/d', 0, 'intval'),
        ];
        $validate = new \app\common\validate\FreelanceResume();
        if (!$validate->check($data)) {
            $this->ajaxReturn(500, $validate->getError(), $data);
        }

        if ($id) {
            $r = $model->save($data, ['id' => $id]);
        }
        $this->ajaxReturn($r !== false ? 200 : 500, $r !== false ? '更新成功' : '更新失败', $data);
    }
    public function resume_info() {
        $model = new FreelanceResume();
        $id = input('get.id/d', 0, 'intval');
        if (!$id) {
            $this->ajaxReturn(500, '参数错误');
        }
        $info = $model->find($id);
        $this->ajaxReturn(200, '', $info);
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

        $total = model('FreelanceAd')->alias('a')->join(config('database.prefix') . 'freelance_ad_category b', 'a.cid=b.id', 'LEFT')
            ->where($where)
            ->count();
        $list = model('FreelanceAd')->alias('a')->field('a.*')->join(config('database.prefix') . 'freelance_ad_category b', 'a.cid=b.id', 'LEFT')
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
        $category_arr = model('FreelanceAdCategory')->getCache();
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
            model('FreelanceAd')
            ->validate(true)
            ->allowField(true)
            ->save($input_data)
        ) {
            $this->ajaxReturn(500, model('FreelanceAd')->getError());
        }
        model('AdminLog')->record(
            '添加自由职业广告。广告ID【' .
                model('FreelanceAd')->id .
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
            $info = model('FreelanceAd')->find($id);
            if (!$info) {
                $this->ajaxReturn(500, '数据获取失败');
            }
            $info = $info->toArray();
            $ad_category = model('FreelanceAdCategory')
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
                model('FreelanceAd')
                ->validate(true)
                ->allowField(true)
                ->save($input_data, ['id' => $id])
            ) {
                $this->ajaxReturn(500, model('FreelanceAd')->getError());
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
        $list = model('FreelanceAd')
            ->where('id', 'in', $id)
            ->column('title');
        model('FreelanceAd')->destroy($id);
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
        $list = model('FreelanceAd')->innerLinks;
        $this->ajaxReturn(200, '获取数据成功', $list);
    }

    public function ad_cat_list() {
        $where = [];
        $current_page = input('get.page/d', 1, 'intval');
        $pagesize = input('get.pagesize/d', 10, 'intval');
        $total = model('FreelanceAdCategory')
            ->where($where)
            ->count();
        $list = model('FreelanceAdCategory')
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
            model('FreelanceAdCategory')
            ->validate(true)
            ->allowField(true)
            ->save($input_data)
        ) {
            $this->ajaxReturn(500, model('FreelanceAdCategory')->getError());
        }
        model('AdminLog')->record(
            '添加自由职业广告位。广告位ID【' .
                model('FreelanceAdCategory')->id .
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
            $info = model('FreelanceAdCategory')->find($id);
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
                model('FreelanceAdCategory')
                ->validate(true)
                ->allowField(true)
                ->save($input_data, ['id' => $id])
            ) {
                $this->ajaxReturn(500, model('FreelanceAdCategory')->getError());
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
        $list = model('FreelanceAdCategory')
            ->where('id', 'in', $id)
            ->column('name');
        model('FreelanceAdCategory')->destroy($id);
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
        $list = model('FreelanceAdCategory')->map_ad_platform;
        $return = [];
        foreach ($list as $key => $value) {
            $arr['id'] = $key;
            $arr['name'] = $value;
            $return[] = $arr;
        }
        $this->ajaxReturn(200, '获取数据成功', $return);
    }
    public function ad_cat_tree() {
        $return = model('FreelanceAdCategory')->getTreeCache();
        $return = json_encode($return);
        $return = str_replace('id', 'value', $return);
        $return = json_decode($return, true);
        $this->ajaxReturn(200, '获取数据成功', $return);
    }
}
