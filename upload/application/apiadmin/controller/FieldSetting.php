<?php

/**
 * 字段设置 Controller
 * @author chenyang
 * Date Time：2022年4月21日17:06:36
 */

namespace app\apiadmin\controller;

use app\common\validate\FieldSettingValidate;
use app\common\logic\FieldSettingLogic;

class FieldSetting extends \app\common\controller\Backend {

    /**
     * 获取字段列表
     * @access public
     * @author chenyang
     * @return array
     * Date Time：2022年4月22日13:20:25
     */
    public function getFieldList() {
        try {
            // 获取参数
            $validate = new FieldSettingValidate();
            $params = $validate->getParamAll();

            $logic = new FieldSettingLogic();
            $result = $logic->getFieldList($params);

            responseJson(200, '获取成功', $result);
        } catch (\Exception $e) {
            saveLog('获取失败-报错信息：' . json_encode(['Line' => $e->getLine(), 'File' => $e->getFile(), 'Message' => $e->getMessage()]));
            responseJson(400, '获取失败');
        }
    }

    /**
     * 获取字段详情
     * @access public
     * @author chenyang
     * @return array
     * Date Time：2022年4月22日14:29:58
     */
    public function getFieldInfo() {
        try {
            // 获取参数
            $validate = new FieldSettingValidate();
            $params = $validate->getParamAll();

            $logic = new FieldSettingLogic();
            $result = $logic->getFieldInfo($params);

            responseJson(200, '获取成功', $result);
        } catch (\Exception $e) {
            saveLog('获取失败-报错信息：' . json_encode(['Line' => $e->getLine(), 'File' => $e->getFile(), 'Message' => $e->getMessage()]));
            responseJson(400, '获取失败');
        }
    }

    /**
     * 新增字段
     * @access public
     * @author chenyang
     * @return array
     * Date Time：2022年4月21日17:56:25
     */
    public function addField() {
        try {
            // 获取参数
            $validate = new FieldSettingValidate();
            $params = $validate->getParamAll();

            $logic = new FieldSettingLogic();
            $logic->saveField($params, $this->admininfo);

            responseJson(200, '保存成功');
        } catch (\Exception $e) {
            saveLog('保存失败-报错信息：' . json_encode(['Line' => $e->getLine(), 'File' => $e->getFile(), 'Message' => $e->getMessage()]));
            responseJson(400, '保存失败');
        }
    }

    /**
     * 编辑字段
     * @access public
     * @author chenyang
     * @return array
     * Date Time：2022年4月22日11:10:45
     */
    public function editField() {
        try {
            // 获取参数
            $validate = new FieldSettingValidate();
            $params = $validate->getParamAll();

            $logic = new FieldSettingLogic();
            $logic->saveField($params, $this->admininfo);

            responseJson(200, '保存成功');
        } catch (\Exception $e) {
            saveLog('保存失败-报错信息：' . json_encode(['Line' => $e->getLine(), 'File' => $e->getFile(), 'Message' => $e->getMessage()]));
            responseJson(400, '保存失败');
        }
    }

    /**
     * 删除字段
     * @access public
     * @author chenyang
     * @return array
     * Date Time：2022年4月22日14:52:15
     */
    public function delField() {
        try {
            // 获取参数
            $validate = new FieldSettingValidate();
            $params = $validate->getParamAll();

            $logic = new FieldSettingLogic();
            $logic->delField($params, $this->admininfo);

            responseJson(200, '删除成功');
        } catch (\Exception $e) {
            saveLog('删除失败-报错信息：' . json_encode(['Line' => $e->getLine(), 'File' => $e->getFile(), 'Message' => $e->getMessage()]));
            responseJson(400, '删除失败');
        }
    }

    /**
     * 获取字段内容
     * @access public
     * @author chenyang
     * @return array
     * Date Time：2022年5月7日15:47:17
     */
    public function getFieldValueList() {
        try {
            // 获取参数
            $validate = new FieldSettingValidate();
            $params = $validate->getParamAll();

            $logic = new FieldSettingLogic();
            $result = $logic->getFieldValueList($params);

            responseJson(200, '获取成功', $result);
        } catch (\Exception $e) {
            saveLog('获取失败-报错信息：' . json_encode(['Line' => $e->getLine(), 'File' => $e->getFile(), 'Message' => $e->getMessage()]));
            responseJson(400, '获取失败');
        }
    }
}
