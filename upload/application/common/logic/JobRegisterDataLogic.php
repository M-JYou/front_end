<?php

/**
 * 职位报名信息 Logic
 * @author chenyang
 * Date Time：2022年5月7日10:13:07
 */

namespace app\common\logic;

use app\common\model\JobRegister;
use app\common\model\JobRegisterContent;
use app\common\model\TemplateDefaultField;
use app\common\model\FormTemplate;
use app\common\model\FormTemplateField;

class JobRegisterDataLogic {

    /**
     * 获取报名列表
     * @access public
     * @author chenyang
     * @param  array $params [请求参数]
     * @return array
     * Date Time：2022年5月7日10:16:54
     */
    public function getRegisterList($params) {
        // 获取模板默认字段
        $defaultFieldTemp = $this->_getDefaultFieldList();
        // 获取查询条件
        $where = $this->_getSearchWhere($params);
        $field = [
            'id',
            'handle_status',
            'add_time',
            'remark',
        ];
        $jobRegisterModel = new JobRegister();
        $list = $jobRegisterModel->getPageList($where, $field, $params['per_page'], ['id' => 'desc']);
        if (!empty($list['data'])) {
            // 获取列表所需的字段内容
            $registerIdArr = array_column($list['data'], 'id');
            $defaultFieldList = [];
            $fieldIdArr = [];

            foreach ($defaultFieldTemp as $defaultFieldInfo) {
                $keys = 'field_id_' . $defaultFieldInfo['field_id'];
                $defaultFieldList[$keys] = $defaultFieldInfo;
                $fieldIdArr[] = $defaultFieldInfo['field_id'];
            }

            $where = [
                'register_id' => ['in', $registerIdArr],
                'field_id'    => ['in', $fieldIdArr],
            ];
            $contentModel = new JobRegisterContent();
            $contentTemp = $contentModel->getList($where);
            $contentList = [];
            if (!empty($contentTemp)) {
                // 处理数据将字段对应最终的值
                foreach ($contentTemp as $contentInfo) {
                    $keys = 'field_id_' . $contentInfo['field_id'];
                    if (isset($defaultFieldList[$keys]) && !empty($defaultFieldList[$keys])) {
                        $rKeys = 'register_id_' . $contentInfo['register_id'];
                        $fieldAlias = $defaultFieldList[$keys]['field_alias'];
                        $contentList[$rKeys][$fieldAlias] = $contentInfo['content'];
                        // 多选框类型字段去掉前后,
                        if ($defaultFieldList[$keys]['field_type'] == 3) {
                            $contentList[$rKeys][$fieldAlias] = trim($contentList[$rKeys][$fieldAlias], ',');
                        }
                    }
                }

                foreach ($list['data'] as &$info) {
                    $keys = 'register_id_' . $info['id'];
                    if (isset($contentList[$keys]) && !empty($contentList[$keys])) {
                        $info = array_merge($info, $contentList[$keys]);
                    }
                    $info['handle_status_name'] = '';
                    if ($info['handle_status'] == 0) $info['handle_status_name'] = '未处理';
                    if ($info['handle_status'] == 1) $info['handle_status_name'] = '已处理';
                    $info['add_time_name'] = date('Y-m-d H:i:s', $info['add_time']);
                }
            }
        }

        return $list;
    }

    /**
     * 获取模板默认字段
     * @access private
     * @author chenyang
     * @return array
     * Date Time：2022年5月7日10:55:52
     */
    private function _getDefaultFieldList() {
        // 获取模板默认字段
        $where = [
            'template_source' => 1,
        ];
        $defaultFieldModel = new TemplateDefaultField();
        $defaultFieldList = $defaultFieldModel->getList($where, 'field_id');
        if (empty($defaultFieldList)) {
            responseJson(400, '未找到对应的默认字段');
        }
        $defaultFieldIdArr = array_column($defaultFieldList, 'field_id');
        // 获取模板字段信息
        $templateFieldResult = $this->_getTemplateFieldList($defaultFieldIdArr);
        if ($templateFieldResult['status'] === false) {
            responseJson(400, $templateFieldResult['msg']);
        }
        $templateFieldList = $templateFieldResult['data'];
        return $templateFieldList;
    }

    /**
     * 获取报名详情
     * @access public
     * @author chenyang
     * @param  array $params [请求参数]
     * @return array
     * Date Time：2022年5月9日10:51:29
     */
    public function getRegisterInfo($params) {
        $where = [
            'id'     => $params['register_id'],
            'is_del' => 0,
        ];
        $field = [
            'id',
            'handle_status',
            'add_time',
            'remark',
        ];
        $jobRegisterModel = new JobRegister();
        $registerInfo = $jobRegisterModel->getInfo($where, $field);
        if (empty($registerInfo)) {
            responseJson(400, '未找到该报名信息');
        }
        $registerInfo['handle_status_name'] = '';
        if ($registerInfo['handle_status'] == 0) $registerInfo['handle_status_name'] = '未处理';
        if ($registerInfo['handle_status'] == 1) $registerInfo['handle_status_name'] = '已处理';
        $registerInfo['add_time_name'] = date('Y-m-d H:i:s', $registerInfo['add_time']);
        // 获取报名内容
        $where = [
            'register_id' => $registerInfo['id']
        ];
        $contentModel = new JobRegisterContent();
        $contentTemp = $contentModel->getList($where, ['field_id', 'content']);
        if (empty($contentTemp)) {
            responseJson(400, '未找到该报名内容');
        }
        foreach ($contentTemp as $contentInfo) {
            $keys = 'field_id_' . $contentInfo['field_id'];
            $contentList[$keys] = $contentInfo;
        }

        // 获取模板字段信息
        $templateFieldResult = $this->_getTemplateFieldList();
        if ($templateFieldResult['status'] === false) {
            responseJson(400, $templateFieldResult['msg']);
        }
        $templateFieldList = $templateFieldResult['data'];

        foreach ($templateFieldList as &$fieldInfo) {
            $keys = 'field_id_' . $fieldInfo['field_id'];
            $fieldInfo['content'] = '';
            if (isset($contentList[$keys]) && !empty($contentList[$keys])) {
                $fieldInfo['content'] = $contentList[$keys]['content'];
                // 多选框类型字段去掉前后,
                if ($fieldInfo['field_type'] == 3) {
                    $fieldInfo['content'] = trim($contentList[$keys]['content'], ',');
                }
            }
        }

        $registerInfo['field_list'] = $templateFieldList;
        return $registerInfo;
    }

    /**
     * 获取模板字段信息
     * @access private
     * @author chenyang
     * @param  array $fieldIdArr [字段ID]
     * Date Time：2022年5月9日15:30:36
     */
    private function _getTemplateFieldList($fieldIdArr = []) {
        // 获取模板信息
        $where = [
            'source' => 1,
            'is_use' => 1,
            'is_del' => 0,
        ];
        $templateModel = new FormTemplate();
        $templateInfo = $templateModel->getInfo($where, 'template_id');
        if (empty($templateInfo)) {
            return callBack(false, '未找到求职模板，请先创建该模板');
        }
        // 获取模板字段
        $where = [
            'a.template_id' => $templateInfo['template_id'],
            'a.is_display'  => 1,
            'b.is_display'  => 1,
            'b.is_del'      => 0,
        ];
        if (!empty($fieldIdArr)) {
            $where['a.field_id'] = ['in', $fieldIdArr];
        }
        $field = [
            'a.field_id',
            'b.field_name',
            'b.field_alias',
            'b.field_type',
        ];
        $order = ['a.sort' => 'asc'];
        $templateFieldModel = new FormTemplateField();
        $fieldList = $templateFieldModel->getJoinFieldList($where, $field, $order);
        if (empty($fieldList)) {
            return callBack(false, '未找到求职模板中的字段信息');
        }
        return callBack(true, 'success', $fieldList);
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
        $registerIdArr = [];
        $contentModel = new JobRegisterContent();

        // 关键词筛选
        if (
            (isset($params['key_type']) && !empty($params['key_type']))
            &&
            (isset($params['keyword']) && !empty($params['keyword']))
        ) {
            switch ($params['key_type']) {
                    // 报名编号
                case 1:
                    $registerIdArr[] = $params['keyword'];
                    $where['id'] = $params['keyword'];
                    break;
                    // 姓名
                case 2:
                    // 查询内容
                    $contentWhere = [
                        'field_alias' => 'full_name',
                        'content'     => ['like', '%' . $params['keyword'] . '%']
                    ];
                    $contentList = $contentModel->getList($contentWhere, 'register_id');
                    // 未找到内容时直接给不存在的值
                    $registerIdArr = !empty($contentList) ? array_column($contentList, 'register_id') : [0];
                    break;
                    // 联系方式
                case 3:
                    // 查询内容
                    $contentWhere = [
                        'field_alias' => 'contact_way',
                        'content'     => $params['keyword']
                    ];
                    $contentList = $contentModel->getList($contentWhere, 'register_id');
                    // 未找到内容时直接给不存在的值
                    $registerIdArr = !empty($contentList) ? array_column($contentList, 'register_id') : [0];
                    break;
            }
        }
        // 处理状态
        if (isset($params['handle_status']) && is_numeric($params['handle_status'])) {
            $where['handle_status'] = $params['handle_status'];
        }
        // 求职岗位
        if (isset($params['applied_position']) && !empty($params['applied_position'])) {
            // 查询内容
            $contentWhere = [
                'field_alias' => 'applied_position',
                'content'     => ['like', '%,' . $params['applied_position'] . ',%']
            ];
            if (!empty($registerIdArr)) {
                $contentWhere['register_id'] = count($registerIdArr) > 1 ? ['in', $registerIdArr] : $registerIdArr[0];
            }
            $contentList = $contentModel->getList($contentWhere, 'register_id');
            // 未找到内容时直接给不存在的值
            $registerIdArr = !empty($contentList) ? array_column($contentList, 'register_id') : [0];
        }
        // 职位类型
        if (isset($params['position_type']) && !empty($params['position_type'])) {
            // 查询内容
            $contentWhere = [
                'field_alias' => 'position_type',
                'content'     => $params['position_type']
            ];
            if (!empty($registerIdArr)) {
                $contentWhere['register_id'] = count($registerIdArr) > 1 ? ['in', $registerIdArr] : $registerIdArr[0];
            }
            $contentList = $contentModel->getList($contentWhere, 'register_id');
            // 未找到内容时直接给不存在的值
            $registerIdArr = !empty($contentList) ? array_column($contentList, 'register_id') : [0];
        }

        // 校验筛选结果
        if (
            // 判断是否带入[关键字]筛选项
            (isset($params['key_type']) && in_array($params['key_type'], [2, 3]) && isset($params['keyword']) && !empty($params['keyword']))
            ||
            // 判断是否带入[求职岗位]筛选项
            (isset($params['applied_position']) && !empty($params['applied_position']))
            ||
            // 判断是否带入[职位类型]筛选项
            (isset($params['position_type']) && !empty($params['position_type']))
        ) {
            $registerIdArr = array_unique($registerIdArr);
            $where['id'] = count($registerIdArr) > 1 ? ['in', $registerIdArr] : $registerIdArr[0];
        }

        // 报名日期
        if ((isset($params['add_start_time']) && $params['add_start_time'] && isset($params['add_end_time']) && $params['add_end_time'])) {
            if ($params['add_start_time'] > $params['add_end_time']) {
                responseJson(400, '报名日期查询时间段错误');
            } else {
                // 要查询的时间段字段
                $where['add_time'] = ['between', [$params['add_start_time'], $params['add_end_time'] + 86399]];
                unset($params['add_start_time'], $params['add_end_time']);
            }
        }

        // 报名编号
        if (isset($params['register_id']) && !empty($params['register_id'])) {
            $where['id'] = ['in', $params['register_id']];
        }

        return $where;
    }

    /**
     * 设置处理状态
     * @access public
     * @author chenyang
     * @param  array $params    [请求参数]
     * @param  array $adminInfo [登录信息]
     * Date Time：2022年5月7日19:00:00
     */
    public function setHandleStatus($params, $adminInfo) {
        $registerIdArr = explode(',', $params['register_id']);
        if (empty($registerIdArr)) {
            responseJson(400, '请选择报名信息');
        }

        $jobRegisterModel = new JobRegister();
        foreach ($registerIdArr as $registerId) {
            $where = [
                'id'     => $registerId,
                'is_del' => 0,
            ];
            $registerInfo = $jobRegisterModel->getInfo($where, ['id', 'handle_status']);
            if (empty($registerInfo)) {
                responseJson(400, '报名编号为【' . $registerInfo['id'] . '】未找到');
            }
        }

        $updateWhere = [
            'id' => ['in', $registerIdArr]
        ];
        $updateData = [
            'handle_status' => $params['handle_status']
        ];
        $result = $jobRegisterModel->edit($updateWhere, $updateData);
        if ($result === false) {
            saveLog('设置处理状态失败-请求SQL为：' . $jobRegisterModel->getLastSql());
            responseJson(400, '设置处理状态失败');
        }

        model('AdminLog')->record('设置报名信息-处理状态,ID：【 ' . $params['register_id'] . ' 】', $adminInfo);
    }

    /**
     * 设置备注
     * @access public
     * @author chenyang
     * @param  array $params    [请求参数]
     * @param  array $adminInfo [登录信息]
     * Date Time：2022年5月9日09:19:57
     */
    public function setRemark($params, $adminInfo) {
        $where = [
            'id'     => $params['register_id'],
            'is_del' => 0,
        ];
        $jobRegisterModel = new JobRegister();
        $registerInfo = $jobRegisterModel->getInfo($where, 'id');
        if (empty($registerInfo)) {
            responseJson(400, '报名编号为【' . $registerInfo['id'] . '】未找到');
        }
        $updateWhere = [
            'id' => $params['register_id']
        ];
        $updateData = [
            'remark' => $params['remark']
        ];

        $result = $jobRegisterModel->edit($updateWhere, $updateData);
        if ($result === false) {
            saveLog('设置跟进备注失败-请求SQL为：' . $jobRegisterModel->getLastSql());
            responseJson(400, '设置跟进备注失败');
        }

        model('AdminLog')->record('设置报名信息-备注,ID：【 ' . $params['register_id'] . ' 】备注：【' . $params['remark'] . '】', $adminInfo);
    }

    /**
     * 删除报名信息
     * @access public
     * @author chenyang
     * @param  array $params    [请求参数]
     * @param  array $adminInfo [登录信息]
     * Date Time：2022年5月9日09:47:44
     */
    public function delRegister($params, $adminInfo) {
        $registerIdArr = explode(',', $params['register_id']);
        if (empty($registerIdArr)) {
            responseJson(400, '请选择报名信息');
        }

        $jobRegisterModel = new JobRegister();
        foreach ($registerIdArr as $registerId) {
            $where = [
                'id'     => $registerId,
                'is_del' => 0,
            ];
            $registerInfo = $jobRegisterModel->getInfo($where, ['id', 'handle_status']);
            if (empty($registerInfo)) {
                responseJson(400, '报名编号为【' . $registerInfo['id'] . '】未找到');
            }
        }

        $updateWhere = [
            'id' => ['in', $registerIdArr]
        ];
        $updateData = [
            'is_del' => time()
        ];
        $result = $jobRegisterModel->edit($updateWhere, $updateData);
        if ($result === false) {
            saveLog('删除报名信息失败-请求SQL为：' . $jobRegisterModel->getLastSql());
            responseJson(400, '删除报名信息失败');
        }

        model('AdminLog')->record('删除报名信息,ID：【 ' . $params['register_id'] . ' 】', $adminInfo);
    }

    /**
     * 导出报名信息
     * @access public
     * @author chenyang
     * @param  array $params [请求参数]
     * Date Time：2022年5月9日17:57:37
     */
    public function exportRegisterData($params) {
        if (isset($params['register_id']) && !empty($params['register_id'])) {
            $registerIdArr = explode(',', $params['register_id']);
            if (empty($registerIdArr)) {
                responseJson(400, '请选择要导出的数据');
            }
            $params['register_id'] = $registerIdArr;
        }
        // 获取查询条件
        $where = $this->_getSearchWhere($params);
        $field = [
            'id',
            'handle_status',
            'add_time',
            'remark',
        ];
        $jobRegisterModel = new JobRegister();
        $list = $jobRegisterModel->getList($where, $field, ['id' => 'desc']);
        if (empty($list)) {
            responseJson(400, '未获取到报名信息');
        }
        // 获取报名内容
        $registerIdArr = array_column($list, 'id');
        $where = [
            'register_id' => ['in', $registerIdArr]
        ];
        $contentModel = new JobRegisterContent();
        $contentTemp = $contentModel->getList($where);
        if (empty($contentTemp)) {
            responseJson(400, '未获取到报名内容');
        }
        $contentList = [];
        foreach ($contentTemp as $contentInfo) {
            $keys = 'register_id_' . $contentInfo['register_id'];
            $fKeys = 'filed_id_' . $contentInfo['field_id'];
            $contentList[$keys][$fKeys] = $contentInfo;
        }

        // 获取模板字段信息
        $templateFieldResult = $this->_getTemplateFieldList();
        if ($templateFieldResult['status'] === false) {
            responseJson(400, $templateFieldResult['msg']);
        }
        $templateFieldList = $templateFieldResult['data'];
        $exportHeader[] = '报名编号';
        foreach ($templateFieldList as $templateFieldInfo) {
            $exportHeader[] = $templateFieldInfo['field_name'];
        }
        $exportHeader[] = '报名时间';
        $exportHeader[] = '处理状态';
        $exportHeader[] = '跟进备注';

        $exportData = [];
        foreach ($list as $info) {
            // 组装数据信息
            $exportInfo = [
                'id' => $info['id']
            ];
            foreach ($templateFieldList as $templateFieldInfo) {
                $rKeys = 'register_id_' . $info['id'];
                $fKeys = 'filed_id_' . $templateFieldInfo['field_id'];
                $exportInfo[$templateFieldInfo['field_alias']] = '';
                if (isset($contentList[$rKeys][$fKeys]) && !empty($contentList[$rKeys][$fKeys])) {
                    $exportInfo[$templateFieldInfo['field_alias']] = $contentList[$rKeys][$fKeys]['content'];
                    // 多选框类型字段去掉前后,
                    if ($templateFieldInfo['field_type'] == 3) {
                        $exportInfo[$templateFieldInfo['field_alias']] = trim($contentList[$rKeys][$fKeys]['content'], ',');
                    }
                }
            }
            // 报名时间
            $exportInfo['add_time_name'] = date('Y-m-d H:i:s', $info['add_time']);
            // 处理状态
            $exportInfo['handle_status_name'] = '';
            if ($info['handle_status'] == 0) $exportInfo['handle_status_name'] = '未处理';
            if ($info['handle_status'] == 1) $exportInfo['handle_status_name'] = '已处理';
            // 跟进备注
            $exportInfo['genjin_remark'] = $info['remark'];
            $exportData[] = array_values($exportInfo);
        }
        return [
            'export_header' => $exportHeader,
            'export_data'   => $exportData,
        ];
    }
}
