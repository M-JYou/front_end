<?php

namespace app\common\model;

class JobfairPosition extends \app\common\model\BaseModel {
    public function positionAdd($data, $admin) {
        //检测展区提交值是否有重复
        if (count($data['area']) != count(array_unique($data['area']))) return array('state' => false, 'msg' => '您提交的展区数据重复，请选择不同展区！');
        if (!$jobfair = model('Jobfair')->find($data['id'])) return array('state' => false, 'msg' => '招聘会不存在');
        //新增的入库
        $area_arr = model('JobfairArea')->where('jobfair_id', $jobfair['id'])->column('area,id');
        $position_arr = model('JobfairPosition')->where('jobfair_id', $jobfair['id'])->column('position,id');
        if (is_array($data['area']) && count($data['area']) > 0) {
            //开启事务
            \think\Db::startTrans();
            try {
                for ($i = 0; $i < count($data['area']); $i++) {
                    if (!empty($data['area'][$i])) {
                        //如果展区不存在
                        if (!isset($area_arr[$data['area'][$i]])) {
                            $area_sqlarr['id'] = '';
                            $area_sqlarr['jobfair_id'] = $jobfair['id'];
                            $area_sqlarr['area'] = trim($data['area'][$i]);
                            if (model('JobfairArea')->allowField(true)->isUpdate(false)->save($area_sqlarr)) {
                                $insertid = model('JobfairArea')->id;
                            } else {
                                throw new \Exception('展位新增失败');
                            }
                        } else {
                            //展区存在，则不新增展区数据
                            $insertid = $area_arr[$data['area'][$i]];
                        }
                        $area_word = $data['area'][$i];
                        if (!empty($data['position_start'][$i]) && !empty($data['position_end'][$i])) {
                            for ($x = $data['position_start'][$i]; $x <= $data['position_end'][$i]; $x++) {
                                if (isset($position_arr[$area_word . $x])) continue;
                                $position_data['id'] = '';
                                $position_data['jobfair_id'] = $jobfair['id'];
                                $position_data['area_id'] = $insertid;
                                $position_data['position'] = $area_word . $x;
                                $position_data['orderid'] = $x;
                                if (!model('JobfairPosition')->allowField(true)->isUpdate(false)->save($position_data)) {
                                    throw new \Exception('展位新增失败');
                                }
                            }
                        }
                    }
                }
                //提交事务
                \think\Db::commit();
            } catch (\Exception $e) {
                \think\Db::rollBack();
                return ['state' => false, 'msg' => $e->getMessage()];
            }
            model('AdminLog')->record(
                '招聘会新增展位。招聘会ID【' . $jobfair['id'] . '】，招聘会标题【' . $jobfair['title'] . '】',
                $admin
            );
            return ['state' => true, 'msg' => '展位添加成功'];
        }
        return ['state' => false, 'msg' => '请选择要新增的展区'];
    }
}
