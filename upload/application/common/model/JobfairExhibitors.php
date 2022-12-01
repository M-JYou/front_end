<?php

namespace app\common\model;

class JobfairExhibitors extends \app\common\model\BaseModel {
    public $audit_text = [1 => '预定成功', 2 => '等待审核', 3 => '审核不通过'];
    public function exhibitorsDelete($id, $admin) {
        !is_array($id) && $id = array($id);
        $sqlin = implode(",", $id);
        if (fieldRegex($sqlin, 'in')) {
            $list = $this->where('id', 'in', $sqlin)->column('id,company_id,companyname,jobfair_id,jobfair_title,position_id');
            foreach ($list as $val) {
                if (!isset($jobfair[$val['jobfair_id']])) $jobfair[$val['jobfair_id']] = ['jobfair_title' => $val['jobfair_title'], 'company' => []];
                $jobfair[$val['jobfair_id']]['company'][$val['company_id']] = $val['companyname'];
                $ids[] = $val['id'];
                $position_id[] = $val['position_id'];
            }
            \think\Db::startTrans();
            try {
                $this->where('id', 'in', $ids)->delete();
                model('JobfairPosition')->where('id', 'in', $position_id)->setField([
                    'company_id' => 0,
                    'company_uid' => 0,
                    'company_name' => '',
                    'status' => 0
                ]);
                \think\Db::commit();
            } catch (\Exception $e) {
                \think\Db::rollBack();
                return array('state' => false, 'msg' => $e->getMessage());
            }
            if (isset($jobfair)) {
                foreach ($jobfair as $val) {
                    model('AdminLog')->record(
                        '删除招聘会【' . $val['jobfair_title'] . '】参会企业。企业ID【' . implode(',', array_keys($val['company'])) . '】;企业名称【' . implode(',', array_values($val['company'])) . '】',
                        $admin
                    );
                }
            }
            return array('state' => true, 'msg' => '删除成功！');
        } else {
            return array('state' => false, 'msg' => '删除失败，请正确选择参会企业！');
        }
    }
    public function exhibitorsAudit($id, $audit, $note, $admin) {
        if (!isset($this->audit_text[$audit])) return array('state' => false, 'msg' => '请正确选择审核状态');
        !is_array($id) && $id = array($id);
        $sqlin = implode(",", $id);
        if (fieldRegex($sqlin, 'in')) {
            $list = $this->where('id', 'in', $sqlin)->column('id,company_id,uid,companyname,jobfair_id,jobfair_title,position_id');
            $position_id = [];
            foreach ($list as $val) {
                if (!isset($jobfair[$val['jobfair_id']])) $jobfair[$val['jobfair_id']] = ['jobfair_title' => $val['jobfair_title'], 'company' => []];
                $jobfair[$val['jobfair_id']]['company'][$val['company_id']] = $val['companyname'];
                $ids[] = $val['id'];
                $position_id[] = $val['position_id'];
            }

            // 当状态改为 通过 或 待审核的时候查询当前展位是否已被其它企业占用 chenyang 2022年4月8日17:57:44
            if (in_array($audit, [1, 2])) {
                $condition = [
                    'id'          => ['not in', $ids],
                    'audit'       => ['in', [1, 2]],
                    'position_id' => ['in', $position_id],
                ];
                $exhibitorsInfo = $this->field('id,companyname,position')->where($condition)->find();
                if (!empty($exhibitorsInfo)) {
                    return array('state' => false, 'msg' => '当前【' . $exhibitorsInfo['position'] . '】展位，已被【' . $exhibitorsInfo['companyname'] . '】占用');
                }
            }

            \think\Db::startTrans();
            try {
                $this->where('id', 'in', $ids)->update(['audit' => $audit]);
                $poStutas = $audit;
                if ($audit == 3) {
                    $poStutas = 0;
                }
                if (!empty($position_id)) {
                    model('JobfairPosition')->where(['id' => ['in', $position_id]])->update(['status' => $poStutas]);
                }

                \think\Db::commit();
            } catch (\Exception $e) {
                \think\Db::rollBack();
                return array('state' => false, 'msg' => $e->getMessage());
            }
            if ($audit == 1) {
                foreach ($list as $val) {
                    model('NotifyRule')->notify(
                        $val['uid'],
                        1,
                        'jobfair_exhibitors_audit_succe',
                        [
                            'jobfair_title' => $val['jobfair_title'],
                        ]
                    );
                }
            } elseif ($audit == 3) {
                foreach ($list as $val) {
                    model('NotifyRule')->notify(
                        $val['uid'],
                        1,
                        'jobfair_exhibitors_audit_fail',
                        [
                            'jobfair_title' => $val['jobfair_title'],
                            'reason' => $note
                        ]
                    );
                }
            }
            if (isset($jobfair)) {
                foreach ($jobfair as $val) {
                    model('AdminLog')->record(
                        '将招聘会【' . $val['jobfair_title'] . '】的参会企业预定状态变更为【' . $this->audit_text[$audit] . '】。企业ID【' . implode(',', array_keys($val['company'])) . '】;企业名称【' . implode(',', array_values($val['company'])) . '】',
                        $admin
                    );
                }
            }
            return array('state' => true, 'msg' => '设置成功！');
        } else {
            return array('state' => false, 'msg' => '设置失败，请正确选择参会企业！');
        }
    }
    public function exhibitorsRecommend($id, $recommend, $admin) {
        if (!in_array($recommend, [0, 1])) return array('state' => false, 'msg' => '请正确选择名企推荐状态');
        !is_array($id) && $id = array($id);
        $sqlin = implode(",", $id);
        if (fieldRegex($sqlin, 'in')) {
            $list = $this->where('id', 'in', $sqlin)->column('id,company_id,companyname,jobfair_id,jobfair_title');
            foreach ($list as $val) {
                if (!isset($jobfair[$val['jobfair_id']])) $jobfair[$val['jobfair_id']] = ['jobfair_title' => $val['jobfair_title'], 'company' => []];
                $jobfair[$val['jobfair_id']]['company'][$val['company_id']] = $val['companyname'];
                $ids[] = $val['id'];
            }
            if (!$this->where('id', 'in', $ids)->setField('recommend', $recommend)) return array('state' => false, 'msg' => '参会企业推荐失败！');
            if (isset($jobfair)) {
                foreach ($jobfair as $val) {
                    model('AdminLog')->record(
                        '将招聘会【' . $val['jobfair_title'] . '】的参会企业变更为名企推荐。企业ID【' . implode(',', array_keys($val['company'])) . '】;企业名称【' . implode(',', array_values($val['company'])) . '】',
                        $admin
                    );
                }
            }
            return array('state' => true, 'msg' => '设置成功！');
        } else {
            return array('state' => false, 'msg' => '设置失败，请正确选择参会企业！');
        }
    }
    public function exhibitorsEdit($data, $admin) {
        if (!isset($data['id'])) return ['state' => false, 'msg' => '请选择参会企业'];
        $exhibitors = $this->find($data['id']);
        if (false === $reg = $this->allowField(true)->validate('JobfairExhibitors.edit')->isUpdate(true)->save($data)) return ['state' => false, 'msg' => $this->getError()];
        model('AdminLog')->record(
            '编辑招聘会【' . $exhibitors['jobfair_title'] . '】参会企业，企业ID【' . $exhibitors['company_id'] . '】，企业名称【' . $exhibitors['companyname'] . '】',
            $admin
        );
        return ['state' => true, 'msg' => '保存成功'];
    }
    public function exhibitorsAdd($data, $admin) {
        $data['jobfair_id'] = input('post.jobfair_id/d', 0, 'intval');
        $data['position_id'] = input('post.position_id/d', 0, 'intval');
        $data['comid'] = input('post.comid/d', 0, 'intval');
        if (!$data['jobfair_id']) return ['state' => false, 'msg' => '请选择招聘会'];
        if (!$data['position_id']) return ['state' => false, 'msg' => '请选择展位'];
        if (!$data['comid']) return ['state' => false, 'msg' => '请选择企业'];
        if (!$company = model('Company')->find($data['comid'])) return ['state' => false, 'msg' => '企业不存在'];
        if (!$jobfair = model('Jobfair')->find($data['jobfair_id'])) return ['state' => false, 'msg' => '招聘会不存在'];
        $exhibitors = $this->where(['jobfair_id' => $jobfair['id'], 'uid' => $company['uid'], 'audit' => ['in', '1,2']])->find();
        if ($exhibitors) return ['state' => false, 'msg' => '该企业已经预订过此招聘会'];
        if (!$position = model('JobfairPosition')->find($data['position_id'])) return ['state' => false, 'msg' => '展位不存在'];
        if ($position['status'] > 0) return ['state' => false, 'msg' => '该展位已被预订，请重新选择展位'];
        $company_contact = model('CompanyContact')->field('contact,mobile')->where('comid', $company['id'])->find();
        $setsqlarr = [
            'uid' => $company['uid'],
            'contact' => $company_contact['contact'],
            'mobile' => $company_contact['mobile'],
            'company_id' => $company['id'],
            'companyname' => $company['companyname'],
            'company_addtime' => $company['addtime'],
            'eaddtime' => time(),
            'jobfair_id' => $jobfair['id'],
            'jobfair_title' => $jobfair['title'],
            'jobfair_addtime' => $jobfair['addtime'],
            'position_id' => $position['id'],
            'position' => $position['position'],
            'recommend' => $data['recommend'],
            'audit' => $data['audit'],
            'etype' => $data['etype'],
            'note' => $data['note']
        ];
        \think\Db::startTrans();
        try {
            if (!$this->isUpdate(false)->allowField(true)->save($setsqlarr)) throw new \Exception($this->getError());
            $position_save['id'] = $position['id'];
            $position_save['jobfair_id'] = $jobfair['id'];
            $position_save['company_id'] = $company['id'];
            $position_save['company_uid'] = $company['uid'];
            $position_save['company_name'] = $company['companyname'];
            $position_save['status'] = $setsqlarr['audit'];
            if (!model('JobfairPosition')->allowField(true)->isUpdate(true)->save($position_save)) throw new \Exception('添加失败');
            \think\Db::commit();
        } catch (\Exception $e) {
            \think\Db::rollBack();
            return array('state' => false, 'msg' => $e->getMessage());
        }
        model('AdminLog')->record(
            '招聘会【' . $jobfair['title'] . '】添加参会企业，企业ID【' . $company['id'] . '】，企业名称【' . $company['companyname'] . '】',
            $admin
        );
        return ['state' => true, 'msg' => '添加成功！'];
    }
}
