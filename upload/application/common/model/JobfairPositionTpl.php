<?php

namespace app\common\model;

class JobfairPositionTpl extends \app\common\model\BaseModel {
    public function tplDelete($id, $admin) {
        !is_array($id) && $id = array($id);
        $sqlin = implode(",", $id);
        if (fieldRegex($sqlin, 'in')) {
            $list = $this->where('id', 'in', $sqlin)->column('id,title');
            $id = array_keys($list);
            if (false === $this->where('id', 'in', $id)->delete()) return array('state' => false, 'msg' => '删除失败');
            model('AdminLog')->record(
                '删除招聘会展位模板;模板ID【' . $sqlin . '】，模板标题【' . implode(',', array_values($list)) . '】',
                $admin
            );
            return array('state' => true, 'msg' => '删除成功！');
        } else {
            return array('state' => false, 'msg' => '删除失败，请正确选择展位模板');
        }
    }
    public function tplAdd($data, $admin) {
        //检测展区提交值是否有重复
        if (count($data['area']) != count(array_unique($data['area']))) return array('state' => false, 'msg' => '您提交的展区数据重复，请选择不同展区！');
        //新增的入库
        $add_sql['title'] = $data['title'];
        $add_sql['area'] = serialize($data['area']);
        $position = array();
        if (is_array($data['area']) && count($data['area']) > 0) {
            for ($i = 0; $i < count($data['area']); $i++) {
                if (!empty($data['area'][$i])) {
                    $area_word = $data['area'][$i];
                    if (!empty($data['position_start'][$i]) && !empty($data['position_end'][$i])) {
                        for ($x = $data['position_start'][$i]; $x <= $data['position_end'][$i]; $x++) {
                            $position[$area_word][] = $area_word . $x;
                        }
                    }
                }
            }
        }
        $add_sql['position'] = serialize($position);
        $add_sql['position_img'] = '';
        $add_sql['status'] = 1;
        if (false === $reg = $this->allowField(true)->validate(true)->isUpdate(false)->save($add_sql)) return array('state' => false, 'msg' => $this->getError());
        if (!$reg || !$this->id) return array('state' => false, 'msg' => '展位模板新增失败');
        model('AdminLog')->record(
            '新增招聘会展位模板。模板ID【' . $this->id . '】;模板名称【' . $add_sql['title'] . '】',
            $admin
        );
        return ['state' => true, 'msg' => '添加成功'];
    }
    public function tplPositionAdd($data, $admin) {
        if (!$info = model('JobfairPositionTpl')->find($data['id'])) return array('state' => false, 'msg' => '展位模板不存在');
        $info['area'] = unserialize($info['area']);
        $info['position'] = unserialize($info['position']);
        //检测展区提交值是否有重复
        if (count($data['area']) != count(array_unique($data['area']))) return array('state' => false, 'msg' => '您提交的展区数据重复，请选择不同展区！');
        //新增的入库
        $edit_sql['area'] = $info['area'];
        $edit_sql['position'] = $info['position'];
        if (is_array($data['area']) && count($data['area']) > 0) {
            for ($i = 0; $i < count($data['area']); $i++) {
                if (!empty($data['area'][$i])) {
                    if (!in_array($data['area'][$i], $edit_sql['area'])) {
                        $edit_sql['area'][] = $data['area'][$i];
                    }
                    $area_word = $data['area'][$i];
                    if (!empty($data['position_start'][$i]) && !empty($data['position_end'][$i])) {
                        for ($x = $data['position_start'][$i]; $x <= $data['position_end'][$i]; $x++) {
                            if (!isset($edit_sql['position'][$area_word]) || (isset($edit_sql['position'][$area_word]) && !in_array($area_word . $x, $edit_sql['position'][$area_word]))) {
                                $edit_sql['position'][$area_word][] = $area_word . $x;
                            }
                        }
                    }
                }
            }
            $edit_sql['area'] = serialize($edit_sql['area']);
            $edit_sql['position'] = serialize($edit_sql['position']);
            $edit_sql['id'] = $info['id'];
            if (false === $reg = $this->allowField(true)->isUpdate(true)->save($edit_sql)) return array('state' => false, 'msg' => $this->getError());
            model('AdminLog')->record(
                '招聘会展位模板新增展位。模板ID【' . $info['id'] . '】;模板名称【' . $info['title'] . '】',
                $admin
            );
            return ['state' => true, 'msg' => '展位添加成功'];
        }
        return ['state' => false, 'msg' => '请选择要新增的展区'];
    }
}
