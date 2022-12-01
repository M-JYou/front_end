<?php

/**
 * 字段设置 Logic
 * @author chenyang
 * Date Time：2022年4月21日18:14:46
 */

namespace app\common\logic;

use app\common\model\FieldSetting;
use app\common\model\FieldValue;
use app\common\model\FormTemplateField;

class FieldSettingLogic {

    /**
     * 获取字段列表
     * @access public
     * @author chenyang
     * @param  array $params [请求参数]
     * @return array
     * Date Time：2022年4月22日13:20:35
     */
    public function getFieldList($params) {
        // 获取查询条件
        $where = $this->_getSearchWhere($params);
        $field = [
            'field_id',
            'field_name',
            'field_type',
            'field_remark',
            'is_display',
            'is_system',
        ];
        $fieldSettingModel = new FieldSetting();
        $list = $fieldSettingModel->getPageList($where, $field, $params['per_page'], ['add_time' => 'desc']);
        if (!empty($list['data'])) {
            foreach ($list['data'] as &$value) {
                // 转换字段类型
                $value['field_type_name'] = $fieldSettingModel->switchFieldType($value['field_type']);
                // 是否显示
                $value['is_display_name'] = '否';
                if ($value['is_display'] == 1) $value['is_display_name'] = '是';
                // 是否系统内置
                $value['is_system_name'] = '否';
                if ($value['is_system'] == 1) $value['is_system_name'] = '是';
            }
        }
        return $list;
    }

    /**
     * 获取字段详情
     * @access public
     * @author chenyang
     * @param  array $params [请求参数]
     * @return array
     * Date Time：2022年4月22日14:33:15
     */
    public function getFieldInfo($params) {
        $where = [
            'field_id' => $params['field_id'],
            'is_del'   => 0
        ];
        $field = [
            'field_id',
            'field_name',
            'field_type',
            'field_remark',
            'is_display',
            'is_system',
        ];
        $fieldSettingModel = new FieldSetting();
        $info = $fieldSettingModel->getInfo($where, $field);
        if (empty($info)) {
            responseJson(400, '未找到对应的字段');
        }
        $fieldValueList = [];
        if (in_array($info['field_type'], [2, 3, 4])) {
            // 获取字段内容
            $where = [
                'field_id' => $info['field_id']
            ];
            $fieldValueModel = new FieldValue();
            $fieldValueList = $fieldValueModel->getList($where, '*', ['sort' => 'asc']);
            if (empty($fieldValueList)) {
                responseJson(400, '未找到对应的字段内容');
            }
        }
        $info['field_value_list'] = $fieldValueList;

        return $info;
    }

    /**
     * 保存字段
     * @access public
     * @author chenyang
     * @param  array $params    [请求参数]
     * @param  array $adminInfo [登录信息]
     * Date Time：2022年4月22日09:40:48
     */
    public function saveField($params, $adminInfo) {
        // 非文本类型字段内容必填
        if (in_array($params['field_type'], [2, 3, 4]) && empty($params['field_value_list'])) {
            responseJson(400, '请填写字段内容');
        }

        $data = [
            'field_name'   => $params['field_name'],
            'field_type'   => $params['field_type'],
            'field_remark' => $params['field_remark'],
            'is_display'   => $params['is_display'],
        ];

        $fieldSettingModel = new FieldSetting();
        $fieldValueModel   = new FieldValue();

        // 开启事务
        $fieldSettingModel->startTrans();
        try {
            if (!isset($params['field_id']) || empty($params['field_id'])) {
                #################### 新增 ####################
                // 生成自定义字段别名
                $insertData = $data;
                $fieldAlias = $this->_generateFieldAlias($fieldSettingModel);
                $insertData['field_alias'] = $fieldAlias;
                $insertData['add_admin_id'] = $adminInfo['id'];
                $insertData['add_time'] = time();
                $fieldId = $fieldSettingModel->add($insertData);
                if (empty($fieldId)) {
                    throw new \Exception('新增字段失败-请求SQL为：' . $fieldSettingModel->getLastSql());
                }
                $logMsg = '新增';
            } else {
                #################### 编辑 ####################
                $where = [
                    'field_id' => $params['field_id'],
                    'is_del'   => 0
                ];
                $fieldInfo = $fieldSettingModel->getInfo($where);
                if (empty($fieldInfo)) {
                    throw new \Exception('未找到对应的字段');
                }
                $updateData = $data;
                // 判断是否是系统内置字段
                if ($fieldInfo['is_system'] == 1) {
                    if ($params['field_name'] != $fieldInfo['field_name']) {
                        $fieldSettingModel->rollback();
                        responseJson(400, '系统字段不可更改字段名称');
                    }
                    if ($params['field_type'] != $fieldInfo['field_type']) {
                        $fieldSettingModel->rollback();
                        responseJson(400, '系统字段不可更改字段类型');
                    }
                }
                $updateWhere = [
                    'field_id' => $fieldInfo['field_id']
                ];
                $updateData['update_admin_id'] = $adminInfo['id'];
                $updateData['update_time'] = time();
                $result = $fieldSettingModel->edit($updateWhere, $updateData);
                if ($result === false) {
                    throw new \Exception('编辑字段失败-请求SQL为：' . $fieldSettingModel->getLastSql());
                }

                // 非文本清空对应的字段内容
                if (in_array($fieldInfo['field_type'], [2, 3, 4])) {
                    // 清空字段内容
                    $result = $fieldValueModel->del(['field_id' => $fieldInfo['field_id']]);
                    if ($result === false) {
                        throw new \Exception('清空字段内容失败-请求SQL为：' . $fieldValueModel->getLastSql());
                    }
                }

                $fieldId = $fieldInfo['field_id'];
                $logMsg = '编辑';
            }

            if (in_array($params['field_type'], [2, 3, 4]) && !empty($params['field_value_list'])) {
                foreach ($params['field_value_list'] as $fieldValueInfo) {
                    $insertValueData[] = [
                        'field_id'    => $fieldId,
                        'field_value' => $fieldValueInfo['field_value'],
                        'sort'        => $fieldValueInfo['sort'],
                    ];
                }
                $result = $fieldValueModel->addAll($insertValueData);
                if (empty($result)) {
                    throw new \Exception('新增字段内容失败-请求SQL为：' . $fieldValueModel->getLastSql());
                }
            }

            model('AdminLog')->record($logMsg . '字段,ID：【 ' . $fieldId . ' 】', $adminInfo);

            // 提交事务
            $fieldSettingModel->commit();
        } catch (\Exception $e) {
            // 回滚事务
            $fieldSettingModel->rollback();
            saveLog('保存失败-报错信息：' . json_encode(['Line' => $e->getLine(), 'File' => $e->getFile(), 'Message' => $e->getMessage()]));
            responseJson(400, '保存失败');
        }
    }

    /**
     * 删除字段
     * @access public
     * @author chenyang
     * @param  array $params    [请求参数]
     * @param  array $adminInfo [登录信息]
     * Date Time：2022年4月22日15:11:26
     */
    public function delField($params, $adminInfo) {
        $fieldIdArr = explode(',', $params['field_id']);
        if (empty($fieldIdArr)) {
            responseJson(400, '请选择要删除的字段');
        }

        $fieldSettingModel  = new FieldSetting();
        $templateFieldModel = new FormTemplateField();
        foreach ($fieldIdArr as $fieldId) {
            $where = [
                'field_id' => $fieldId,
                'is_del'   => 0
            ];
            $fieldInfo = $fieldSettingModel->getInfo($where, ['field_id', 'field_name', 'is_system']);
            if (empty($fieldInfo)) {
                responseJson(400, '【' . $fieldInfo['field_name'] . '】未找到');
            }
            if ($fieldInfo['is_system'] == 1) {
                responseJson(400, '【' . $fieldInfo['field_name'] . '】为系统字段不支持删除');
            }
            // 查询该字段是否被模板引用
            $templateFieldInfo = $templateFieldModel->getInfo(['field_id' => $fieldInfo['field_id']], 'field_id');
            if (!empty($templateFieldInfo)) {
                responseJson(400, '【' . $fieldInfo['field_name'] . '】已被模板引用，不可删除');
            }
        }

        $updateWhere = [
            'field_id' => ['in', $fieldIdArr]
        ];
        $updateData = [
            'is_del' => time()
        ];
        $result = $fieldSettingModel->edit($updateWhere, $updateData);
        if ($result === false) {
            saveLog('删除字段失败-请求SQL为：' . $fieldSettingModel->getLastSql());
            responseJson(400, '删除字段失败');
        }

        model('AdminLog')->record('删除字段,ID：【 ' . $params['field_id'] . ' 】', $adminInfo);
    }

    /**
     * 获取查询条件
     * @access private
     * @author chenyang
     * @param  array $params [请求参数]
     * @return array
     * Date Time：2022年4月24日17:27:43
     */
    private function _getSearchWhere($params) {
        $where = [
            'is_del' => 0
        ];
        // 关键词筛选
        if (
            (isset($params['key_type']) && !empty($params['key_type']))
            &&
            (isset($params['keyword']) && !empty($params['keyword']))
        ) {
            switch ($params['key_type']) {
                case 1:
                    $where['field_name'] = ['like', '%' . $params['keyword'] . '%'];
                    break;
            }
        }
        // 字段类型
        if (isset($params['field_type']) && is_numeric($params['field_type'])) {
            $where['field_type'] = $params['field_type'];
        }
        // 是否系统字段
        if (isset($params['is_system']) && is_numeric($params['is_system'])) {
            $where['is_system'] = $params['is_system'];
        }
        // 是否显示
        if (isset($params['is_display']) && is_numeric($params['is_display'])) {
            $where['is_display'] = $params['is_display'];
        }
        return $where;
    }

    /**
     * 生成字段别名
     * @access private
     * @author chenyang
     * @param  object $fieldSettingModel
     * @return string
     * Date Time：2022年5月6日09:15:19
     */
    private function _generateFieldAlias($fieldSettingModel) {
        $where = [
            'is_system' => 0,
        ];
        $fieldInfo = $fieldSettingModel->getInfo($where, 'field_alias', ['field_id' => 'desc']);
        $fieldAlias = 'customer_field_';
        $num = 1;
        if (!empty($fieldInfo)) {
            $num = str_replace('customer_field_', '', $fieldInfo['field_alias']);
            $num++;
        }
        $fieldAlias .= $num;
        return $fieldAlias;
    }

    /**
     * 获取字段内容
     * @access public
     * @author chenyang
     * @param  array $params [请求参数]
     * @return array
     * Date Time：2022年5月7日15:51:29
     */
    public function getFieldValueList($params) {
        $fieldAliasArr = explode(',', $params['field_alias']);
        if (empty($fieldAliasArr)) {
            return [];
        }
        $where = [
            'a.field_alias' => ['in', $fieldAliasArr],
            'a.is_del'      => 0
        ];
        $field = [
            'a.field_name',
            'a.field_alias',
            'b.field_value',
        ];
        $fieldSettingModel = new FieldSetting();
        $fieldTemp = $fieldSettingModel->getJoinValueList($where, $field, ['b.sort' => 'asc']);
        $fieldList = [];
        if (!empty($fieldTemp)) {
            foreach ($fieldTemp as $fieldInfo) {
                $fieldList[$fieldInfo['field_alias']][] = $fieldInfo;
            }
        }
        // 确保最终返回的数据中要包含请求的参数，所以重新处理一遍
        $list = [];
        foreach ($fieldAliasArr as $fieldAlias) {
            if (isset($fieldList[$fieldAlias]) && !empty($fieldList[$fieldAlias])) {
                $list[$fieldAlias] = $fieldList[$fieldAlias];
            } else {
                $list[$fieldAlias] = [];
            }
        }
        return $list;
    }
}
