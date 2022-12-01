<?php

/**
 * 表单模板 Controller
 * @author chenyang
 * Date Time：2022年4月22日16:57:53
 */

namespace app\apiadmin\controller;

use app\common\validate\FormTemplateValidate;
use app\common\logic\FormTemplateLogic;

class FormTemplate extends \app\common\controller\Backend {

    /**
     * 获取模板列表
     * @access public
     * @author chenyang
     * @return array
     * Date Time：2022年4月24日08:54:04
     */
    public function getTemplateList() {
        try {
            // 获取参数
            $validate = new FormTemplateValidate();
            $params = $validate->getParamAll();

            $logic = new FormTemplateLogic();
            $result = $logic->getTemplateList($params);

            responseJson(200, '获取成功', $result);
        } catch (\Exception $e) {
            saveLog('获取失败-报错信息：' . json_encode(['Line' => $e->getLine(), 'File' => $e->getFile(), 'Message' => $e->getMessage()]));
            responseJson(400, '获取失败');
        }
    }

    /**
     * 获取模板详情
     * @access public
     * @author chenyang
     * @return array
     * Date Time：2022年4月24日09:46:19
     */
    public function getTemplateInfo() {
        try {
            // 获取参数
            $validate = new FormTemplateValidate();
            $params = $validate->getParamAll();

            $logic = new FormTemplateLogic();
            $result = $logic->getTemplateInfo($params);

            responseJson(200, '获取成功', $result);
        } catch (\Exception $e) {
            saveLog('获取失败-报错信息：' . json_encode(['Line' => $e->getLine(), 'File' => $e->getFile(), 'Message' => $e->getMessage()]));
            responseJson(400, '获取失败');
        }
    }

    /**
     * 新增模板
     * @access public
     * @author chenyang
     * @return array
     * Date Time：2022年4月22日17:04:21
     */
    public function addTemplate() {
        try {
            // 获取参数
            $validate = new FormTemplateValidate();
            $params = $validate->getParamAll();

            $logic = new FormTemplateLogic();
            $logic->saveTemplate($params, $this->admininfo);

            responseJson(200, '保存成功');
        } catch (\Exception $e) {
            saveLog('保存失败-报错信息：' . json_encode(['Line' => $e->getLine(), 'File' => $e->getFile(), 'Message' => $e->getMessage()]));
            responseJson(400, '保存失败');
        }
    }

    /**
     * 编辑模板
     * @access public
     * @author chenyang
     * @return array
     * Date Time：2022年4月22日20:14:57
     */
    public function editTemplate() {
        try {
            // 获取参数
            $validate = new FormTemplateValidate();
            $params = $validate->getParamAll();

            $logic = new FormTemplateLogic();
            $logic->saveTemplate($params, $this->admininfo);

            responseJson(200, '保存成功');
        } catch (\Exception $e) {
            saveLog('保存失败-报错信息：' . json_encode(['Line' => $e->getLine(), 'File' => $e->getFile(), 'Message' => $e->getMessage()]));
            responseJson(400, '保存失败');
        }
    }

    /**
     * 删除模板
     * @access public
     * @author chenyang
     * @return array
     * Date Time：2022年4月24日11:36:25
     */
    public function delTemplate() {
        try {
            // 获取参数
            $validate = new FormTemplateValidate();
            $params = $validate->getParamAll();

            $logic = new FormTemplateLogic();
            $logic->delTemplate($params, $this->admininfo);

            responseJson(200, '删除成功');
        } catch (\Exception $e) {
            saveLog('删除失败-报错信息：' . json_encode(['Line' => $e->getLine(), 'File' => $e->getFile(), 'Message' => $e->getMessage()]));
            responseJson(400, '删除失败');
        }
    }

    /**
     * 更改使用
     * @access public
     * @author chenyang
     * @return array
     * Date Time：2022年4月24日13:47:21
     */
    public function changeUse() {
        try {
            // 获取参数
            $validate = new FormTemplateValidate();
            $params = $validate->getParamAll();

            $logic = new FormTemplateLogic();
            $logic->changeUse($params, $this->admininfo);

            responseJson(200, '更改成功');
        } catch (\Exception $e) {
            saveLog('删除失败-报错信息：' . json_encode(['Line' => $e->getLine(), 'File' => $e->getFile(), 'Message' => $e->getMessage()]));
            responseJson(400, '更改失败');
        }
    }

    /**
     * 获取模板默认字段
     * @access public
     * @author chenyang
     * @return array
     * Date Time：2022年4月29日11:37:39
     */
    public function getDefaultField() {
        try {
            // 获取参数
            $validate = new FormTemplateValidate();
            $params = $validate->getParamAll();

            $logic = new FormTemplateLogic();
            $result = $logic->getDefaultField($params);

            responseJson(200, '获取成功', $result);
        } catch (\Exception $e) {
            saveLog('获取失败-报错信息：' . json_encode(['Line' => $e->getLine(), 'File' => $e->getFile(), 'Message' => $e->getMessage()]));
            responseJson(400, '获取失败');
        }
    }

    /**
     * 获取字段列表
     * @access public
     * @author chenyang
     * @return array
     * Date Time：2022年5月5日11:14:46
     */
    public function getFieldList() {
        try {
            // 获取参数
            $validate = new FormTemplateValidate();
            $params = $validate->getParamAll();

            $logic = new FormTemplateLogic();
            $result = $logic->getFieldList($params);

            responseJson(200, '获取成功', $result);
        } catch (\Exception $e) {
            saveLog('获取失败-报错信息：' . json_encode(['Line' => $e->getLine(), 'File' => $e->getFile(), 'Message' => $e->getMessage()]));
            responseJson(400, '获取失败');
        }
    }
}
