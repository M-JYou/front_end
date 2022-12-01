<?php

namespace app\common\model;

class Jobfair extends \app\common\model\BaseModel {
    public function jobfairAdd($data, $admin) {
        if (isset($data['tpl_id']) && $data['tpl_id']) {
            $tpl = model('JobfairPositionTpl')->where(array('status' => 1, 'id' => $data['tpl_id']))->find();
            $data['position_img'] = $tpl->position_img;
        }
        $data['addtime'] = time();
        //开启事务
        \think\Db::startTrans();
        try {
            if (false === $reg = $this->allowField(true)->validate(true)->isUpdate(false)->save($data)) throw new \Exception($this->getError());
            if (!$reg || !$this->id) throw new \Exception('发布失败，请重新操作');
            $jobfair['id'] = $this->id;
            $jobfair['title'] = $data['title'];
            if (isset($tpl)) {
                $area = unserialize($tpl->area);
                $position = unserialize($tpl->position);
                $area_insert_id = [];
                foreach ($area as $key => $value) {
                    $area_sqlarr['id'] = '';
                    $area_sqlarr['jobfair_id'] = $jobfair['id'];
                    $area_sqlarr['area'] = $value;
                    if (model('JobfairArea')->allowField(true)->isUpdate(false)->save($area_sqlarr)) {
                        $area_insert_id[$value] = model('JobfairArea')->id;
                    } else {
                        throw new \Exception('发布失败，展位保存失败');
                    }
                }
                if (!empty($area_insert_id)) {
                    foreach ($position as $key => $value) {
                        foreach ($value as $k => $v) {
                            $position_data['jobfair_id'] = $jobfair['id'];
                            $position_data['area_id'] = $area_insert_id[$key];
                            $position_data['position'] = $v;
                            $position_data['company_id'] = 0;
                            $position_data['company_uid'] = 0;
                            $position_data['company_name'] = '';
                            $position_data['status'] = 0;
                            $position_data['orderid'] = ltrim($v, $key);
                            $position_sqlarr[] = $position_data;
                        }
                    }
                    $position = model('JobfairPosition')->saveAll($position_sqlarr);
                    if (!$position || !count($position)) throw new \Exception('发布失败，展位保存失败');
                }
            }
            //提交事务
            \think\Db::commit();
        } catch (\Exception $e) {
            \think\Db::rollBack();
            return ['state' => false, 'msg' => $e->getMessage()];
        }
        model('AdminLog')->record(
            '发布招聘会。招聘会ID【' . $jobfair['id'] . '】;招聘会标题【' . $jobfair['title'] . '】',
            $admin
        );
        return ['state' => true, 'msg' => '添加成功', 'data' => $jobfair];
    }
    public function jobfairEdit($data, $admin) {
        if (false === $reg = $this->allowField(true)->validate(true)->isUpdate(true)->save($data)) return ['state' => false, 'msg' => $this->getError()];
        $jobfair = $this->find($data['id']);
        model('AdminLog')->record(
            '编辑招聘会。招聘会ID【' . $jobfair['id'] . '】;招聘会标题【' . $jobfair['title'] . '】',
            $admin
        );
        return ['state' => true, 'msg' => '保存成功'];
    }
    public function jobfairDelete($id, $admin) {
        !is_array($id) && $id = array($id);
        $sqlin = implode(",", $id);
        if (fieldRegex($sqlin, 'in')) {
            $list = $this->where('id', 'in', $sqlin)->column('id,title');
            $id = array_keys($list);
            \think\Db::startTrans();
            try {
                $this->where('id', 'in', $id)->delete();
                model('JobfairArea')->where('jobfair_id', 'in', $id)->delete();
                model('JobfairExhibitors')->where('jobfair_id', 'in', $id)->delete();
                model('JobfairPosition')->where('jobfair_id', 'in', $id)->delete();
                model('JobfairRetrospect')->where('jobfair_id', 'in', $id)->delete();
                \think\Db::commit();
            } catch (\Exception $e) {
                \think\Db::rollBack();
                return array('state' => false, 'msg' => $e->getMessage());
            }
            model('AdminLog')->record(
                '删除招聘会。招聘会ID【' . $sqlin . '】;招聘会标题【' . implode(',', array_values($list)) . '】',
                $admin
            );
            return array('state' => true, 'msg' => '删除成功！');
        } else {
            return array('state' => false, 'msg' => '删除失败,请正确选择招聘会！');
        }
    }
}
