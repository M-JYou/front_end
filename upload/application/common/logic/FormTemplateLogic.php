<?php

/**
 * 表单模板 Logic
 * @author chenyang
 * Date Time：2022年4月22日17:00:31
 */

namespace app\common\logic;

use app\common\model\FormTemplate;
use app\common\model\FormTemplateField;
use app\common\model\TemplateDefaultField;
use app\common\model\FieldSetting;

class FormTemplateLogic {

    /**
     * 获取模板列表
     * @access public
     * @author chenyang
     * @param  array $params [请求参数]
     * @return array
     * Date Time：2022年4月24日09:06:57
     */
    public function getTemplateList($params) {
        // 获取查询条件
        $where = $this->_getSearchWhere($params);
        $field = [
            'template_id',
            'template_name',
            'template_desc',
            'is_use',
        ];
        $templateModel = new FormTemplate();
        $list = $templateModel->getPageList($where, $field, $params['per_page'], ['add_time' => 'desc']);
        return $list;
    }

    /**
     * 获取模板详情
     * @access public
     * @author chenyang
     * @param  array $params [请求参数]
     * @return array
     * Date Time：2022年4月24日09:47:09
     */
    public function getTemplateInfo($params) {
        $where = [
            'template_id' => $params['template_id'],
            'is_del'      => 0
        ];
        $field = [
            'template_id',
            'template_name',
            'template_desc',
            'source',
        ];
        $templateModel = new FormTemplate();
        $info = $templateModel->getInfo($where, $field);
        if (empty($info)) {
            responseJson(400, '未找到对应的模板');
        }
        // 获取模板字段
        $where = [
            'a.template_id' => $info['template_id'],
            'b.is_display'  => 1,
            'b.is_del'      => 0,
        ];
        $field = [
            'a.field_id',
            'b.field_name',
            'b.field_type',
            'a.is_must',
            'a.is_display',
        ];
        $order = ['a.sort' => 'asc'];
        $templateFieldModel = new FormTemplateField();
        $fieldList = $templateFieldModel->getJoinFieldList($where, $field, $order);
        if (empty($fieldList)) {
            saveLog('未找到对应的字段-请求SQL为：' . $templateFieldModel->getLastSql());
            responseJson(400, '未找到对应的字段');
        }

        // 获取模板默认字段
        $where = [
            'template_source' => $info['source']
        ];
        $field = [
            'field_id',
            'def_close',
            'def_must',
        ];
        $defaultFieldModel = new TemplateDefaultField();
        $defaultFieldTemp = $defaultFieldModel->getList($where, $field);
        $defaultFieldList = [];
        if (!empty($defaultFieldTemp)) {
            foreach ($defaultFieldTemp as $defaultFieldInfo) {
                $keys = 'field_id_' . $defaultFieldInfo['field_id'];
                $defaultFieldList[$keys] = $defaultFieldInfo;
            }
        }

        $fieldSettingModel = new FieldSetting();
        foreach ($fieldList as &$fieldInfo) {
            // 转换字段类型
            $fieldInfo['field_type_name'] = $fieldSettingModel->switchFieldType($fieldInfo['field_type']);
            // 回填模板默认值上的字段
            $keys = 'field_id_' . $fieldInfo['field_id'];
            if (isset($defaultFieldList[$keys]) && !empty($defaultFieldList[$keys])) {
                $fieldInfo['def_close'] = $defaultFieldList[$keys]['def_close'];
                $fieldInfo['def_must'] = $defaultFieldList[$keys]['def_must'];
            } else {
                $fieldInfo['def_close'] = 1;
                $fieldInfo['def_must'] = 0;
            }
        }
        $info['field_list'] = $fieldList;
        return $info;
    }

    /**
     * 保存模板
     * @access public
     * @author chenyang
     * @param  array $params    [请求参数]
     * @param  array $adminInfo [登录信息]
     * @return array
     * Date Time：2022年4月22日18:58:39
     */
    public function saveTemplate($params, $adminInfo) {
        $data = [
            'template_name' => $params['template_name'],
            'template_desc' => $params['template_desc'],
            'source'        => $params['source'],
        ];

        $templateModel      = new FormTemplate();
        $templateFieldModel = new FormTemplateField();

        // 开启事务
        $templateModel->startTrans();
        try {
            if (!isset($params['template_id']) || empty($params['template_id'])) {
                #################### 新增 ####################
                $insertData = $data;
                $insertData['add_admin_id'] = $adminInfo['id'];
                $insertData['add_time'] = time();
                $templateId = $templateModel->add($insertData);
                if (empty($templateId)) {
                    throw new \Exception('新增模板失败-请求SQL为：' . $templateModel->getLastSql());
                }
                $logMsg = '新增';
            } else {
                #################### 编辑 ####################
                $where = [
                    'template_id' => $params['template_id'],
                    'is_del'      => 0
                ];
                $templateInfo = $templateModel->getInfo($where);
                if (empty($templateInfo)) {
                    throw new \Exception('未找到对应的模板');
                }
                $updateWhere = [
                    'template_id' => $templateInfo['template_id']
                ];
                $updateData = $data;
                $updateData['update_admin_id'] = $adminInfo['id'];
                $updateData['update_time'] = time();
                $result = $templateModel->edit($updateWhere, $updateData);
                if ($result === false) {
                    throw new \Exception('编辑模板失败-请求SQL为：' . $templateModel->getLastSql());
                }

                // 清空模板字段
                $result = $templateFieldModel->del(['template_id' => $templateInfo['template_id']]);
                if ($result === false) {
                    throw new \Exception('清空模板字段失败-请求SQL为：' . $templateFieldModel->getLastSql());
                }

                $templateId = $templateInfo['template_id'];
                $logMsg = '编辑';
            }

            if (!empty($params['field_list'])) {
                foreach ($params['field_list'] as $fieldInfo) {
                    $fieldList[] = [
                        'template_id' => $templateId,
                        'field_id'    => $fieldInfo['field_id'],
                        'is_must'     => $fieldInfo['is_must'],
                        'is_display'  => $fieldInfo['is_display'],
                        'sort'        => $fieldInfo['sort'],
                    ];
                }
                $result = $templateFieldModel->addAll($fieldList);
                if (empty($result)) {
                    throw new \Exception('新增模板字段失败-请求SQL为：' . $templateFieldModel->getLastSql());
                }
            }

            model('AdminLog')->record($logMsg . '表单模板,ID：【 ' . $templateId . ' 】', $adminInfo);

            // 提交事务
            $templateModel->commit();
        } catch (\Exception $e) {
            // 回滚事务
            $templateModel->rollback();
            saveLog('保存失败-报错信息：' . json_encode(['Line' => $e->getLine(), 'File' => $e->getFile(), 'Message' => $e->getMessage()]));
            responseJson(400, '保存失败');
        }
    }

    /**
     * 删除模板
     * @access public
     * @author chenyang
     * @param  array $params    [请求参数]
     * @param  array $adminInfo [登录信息]
     * Date Time：2022年4月24日11:48:16
     */
    public function delTemplate($params, $adminInfo) {
        $where = [
            'template_id' => $params['template_id'],
            'is_del'      => 0
        ];
        $field = [
            'template_id',
            'source',
            'is_use',
        ];
        $templateModel = new FormTemplate();
        $templateInfo = $templateModel->getInfo($where, $field);
        if (empty($templateInfo)) {
            responseJson(400, '未找到对应的模板');
        }

        // 判断如果当前只剩一个模板时不允许被删除
        $where = [
            'template_id' => ['neq', $templateInfo['template_id']],
            'is_del'      => 0
        ];
        $infos = $templateModel->getInfo($where, 'template_id');
        if (empty($infos)) {
            responseJson(400, '仅剩最后一个模板不允许被删除');
        }

        // 开启事务
        $templateModel->startTrans();
        try {
            // 判断如果当前删除的模板已被选中则重新选中一个新的模板
            if ($templateInfo['is_use'] == 1) {
                $where = [
                    'template_id' => ['neq', $templateInfo['template_id']],
                    'source'      => $templateInfo['source'],
                    'is_use'      => 0,
                    'is_del'      => 0,
                ];
                $order = [
                    'add_time' => 'asc'
                ];
                $templateInfos = $templateModel->getInfo($where, $field, $order);
                if (!empty($templateInfos)) {
                    $updateWhere = [
                        'template_id' => $templateInfos['template_id']
                    ];
                    $updateData = [
                        'is_use' => 1
                    ];
                    $result = $templateModel->edit($updateWhere, $updateData);
                    if ($result === false) {
                        throw new \Exception('更改模板使用失败-请求SQL为：' . $templateModel->getLastSql());
                    }
                }
            }

            $updateWhere = [
                'template_id' => $params['template_id']
            ];
            $updateData = [
                'is_use' => 0,
                'is_del' => time()
            ];
            $result = $templateModel->edit($updateWhere, $updateData);
            if ($result === false) {
                throw new \Exception('删除模板失败-请求SQL为：' . $templateModel->getLastSql());
            }

            model('AdminLog')->record('删除模板,ID：【 ' . $params['template_id'] . ' 】', $adminInfo);

            // 提交事务
            $templateModel->commit();
        } catch (\Exception $e) {
            // 回滚事务
            $templateModel->rollback();
            saveLog('删除失败-报错信息：' . json_encode(['Line' => $e->getLine(), 'File' => $e->getFile(), 'Message' => $e->getMessage()]));
            responseJson(400, '删除失败');
        }
    }

    /**
     * 更改使用
     * @access public
     * @author chenyang
     * @param  array $params    [请求参数]
     * @param  array $adminInfo [登录信息]
     * Date Time：2022年4月24日13:50:45
     */
    public function changeUse($params, $adminInfo) {
        $where = [
            'template_id' => $params['template_id'],
            'is_del'      => 0
        ];
        $templateModel = new FormTemplate();
        $templateInfo = $templateModel->getInfo($where, 'template_id,source');
        if (empty($templateInfo)) {
            responseJson(400, '未找到对应的模板');
        }
        // 将现有已使用的模板取消
        $updateWhere = [
            'source' => $templateInfo['source'],
            'is_use' => 1
        ];
        $updateData = [
            'is_use' => 0
        ];
        $result = $templateModel->edit($updateWhere, $updateData);
        if ($result === false) {
            saveLog('取消模板使用失败-请求SQL为：' . $templateModel->getLastSql());
            responseJson(400, '取消模板使用失败');
        }

        // 改为当前提交的模板
        $updateWhere = [
            'template_id' => $templateInfo['template_id'],
        ];
        $updateData = [
            'is_use' => 1
        ];
        $result = $templateModel->edit($updateWhere, $updateData);
        if ($result === false) {
            saveLog('更改使用模板失败-请求SQL为：' . $templateModel->getLastSql());
            responseJson(400, '更改使用模板失败');
        }

        model('AdminLog')->record('更改使用模板,ID：【 ' . $params['template_id'] . ' 】', $adminInfo);
    }

    /**
     * 获取查询条件
     * @access private
     * @author chenyang
     * @param  array $params [请求参数]
     * @return array
     * Date Time：2022年4月26日11:55:59
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
                    $where['template_name'] = ['like', '%' . $params['keyword'] . '%'];
                    break;
            }
        }
        // 模板来源
        if (isset($params['source']) && !empty($params['source'])) {
            $where['source'] = $params['source'];
        }
        return $where;
    }

    /**
     * 获取模板默认字段
     * @access public
     * @author chenyang
     * @param  array $params [请求参数]
     * @return array
     * Date Time：2022年4月29日11:56:00
     */
    public function getDefaultField($params) {
        $where = [
            'a.template_source' => $params['source'],
            'b.is_display'      => 1,
            'b.is_del'          => 0,
        ];
        $field = [
            'b.field_id',
            'b.field_name',
            'b.field_type',
            'a.def_close',
            'a.def_must',
        ];
        $defaultFieldModel = new TemplateDefaultField();
        $fieldList = $defaultFieldModel->getJoinFieldList($where, $field);
        if (!empty($fieldList)) {
            $fieldSettingModel = new FieldSetting();
            foreach ($fieldList as &$fieldInfo) {
                // 转换字段类型
                $fieldInfo['field_type_name'] = $fieldSettingModel->switchFieldType($fieldInfo['field_type']);
                $fieldInfo['is_display'] = 1;
                $fieldInfo['is_must'] = 0;
                if ($fieldInfo['def_must'] == 1) $fieldInfo['is_must'] = 1;
            }
        }
        return $fieldList;
    }

    /**
     * 获取字段列表
     * @access public
     * @author chenyang
     * @param  array $params [请求参数]
     * @return array
     * Date Time：2022年5月5日11:23:42
     */
    public function getFieldList($params) {
        // 获取模板默认字段
        $where = [
            'template_source' => $params['source']
        ];
        $field = [
            'field_id',
            'def_close',
            'def_must',
        ];
        $defaultFieldModel = new TemplateDefaultField();
        $defaultFieldTemp = $defaultFieldModel->getList($where, $field);
        $defaultFieldList = [];
        if (!empty($defaultFieldTemp)) {
            foreach ($defaultFieldTemp as $defaultFieldInfo) {
                $keys = 'field_id_' . $defaultFieldInfo['field_id'];
                $defaultFieldList[$keys] = $defaultFieldInfo;
            }
        }

        // 获取全部字段
        $where = [
            'is_display' => 1,
            'is_del'     => 0,
        ];
        $field = [
            'field_id',
            'field_name',
            'field_type',
        ];
        $fieldSettingModel = new FieldSetting();
        $fieldList = $fieldSettingModel->getList($where, $field);
        if (!empty($fieldList)) {
            foreach ($fieldList as &$fieldInfo) {
                // 转换字段类型
                $fieldInfo['field_type_name'] = $fieldSettingModel->switchFieldType($fieldInfo['field_type']);
                // 回填模板默认值上的字段
                $keys = 'field_id_' . $fieldInfo['field_id'];
                $fieldInfo['is_display'] = 1;
                $fieldInfo['is_must'] = 0;
                if (isset($defaultFieldList[$keys]) && !empty($defaultFieldList[$keys])) {
                    $fieldInfo['def_close'] = $defaultFieldList[$keys]['def_close'];
                    $fieldInfo['def_must'] = $defaultFieldList[$keys]['def_must'];
                    if ($fieldInfo['def_must'] == 1) $fieldInfo['is_must'] = 1;
                } else {
                    $fieldInfo['def_close'] = 1;
                    $fieldInfo['def_must'] = 0;
                }
            }
        }
        return $fieldList;
    }

    /**
     * 新增默认模板
     * @access public
     * @author chenyang
     * @return array
     * Date Time：2022年5月31日14:15:18
     */
    public function insertDefaultTemplate() {
        $templateModel = new FormTemplate();
        // 开启事务
        $templateModel->startTrans();
        try {
            // 新增模板
            $insertData = [
                'template_name' => '默认',
                'template_desc' => '系统默认模板',
                'source'        => 1,
                'is_use'        => 1,
            ];
            $templateId = $templateModel->add($insertData);
            if (empty($templateId)) {
                throw new \Exception('新增默认模板失败-请求SQL为：' . $templateModel->getLastSql());
            }
            // 获取模板默认字段
            $defaultFieldModel = new TemplateDefaultField();
            $defaultFieldList = $defaultFieldModel->getList(['template_source' => 1]);
            if (empty($defaultFieldList)) {
                throw new \Exception('未找到模板默认字段-请求SQL为：' . $defaultFieldModel->getLastSql());
            }

            $sort = 1;
            $fieldList = [];
            foreach ($defaultFieldList as $defaultFieldInfo) {
                $fieldList[] = [
                    'template_id' => $templateId,
                    'field_id'    => $defaultFieldInfo['field_id'],
                    'is_must'     => $defaultFieldInfo['def_must'],
                    'is_display'  => $defaultFieldInfo['def_close'] == 0 ? 1 : 1,
                    'sort'        => $sort++,
                ];
            }
            $templateFieldModel = new FormTemplateField();
            $result = $templateFieldModel->addAll($fieldList);
            if (empty($result)) {
                throw new \Exception('新增模板字段失败-请求SQL为：' . $templateFieldModel->getLastSql());
            }

            // 提交事务
            $templateModel->commit();
        } catch (\Exception $e) {
            // 回滚事务
            $templateModel->rollback();
            saveLog('新增默认模板失败-报错信息：' . json_encode(['Line' => $e->getLine(), 'File' => $e->getFile(), 'Message' => $e->getMessage()]));
            return callBack(false, '新增默认模板失败');
        }

        return callBack(true, 'success', $templateId);
    }
}
