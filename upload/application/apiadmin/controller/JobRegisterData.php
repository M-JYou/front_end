<?php

/**
 * 职位报名信息 Controller
 * @author chenyang
 * Date Time：2022年5月7日10:10:10
 */

namespace app\apiadmin\controller;

use app\common\validate\JobRegisterDataValidate;
use app\common\logic\JobRegisterDataLogic;

class JobRegisterData extends \app\common\controller\Backend {

    /**
     * 获取报名列表
     * @access public
     * @author chenyang
     * @return array
     * Date Time：2022年5月7日10:16:47
     */
    public function getRegisterList() {
        try {
            // 获取参数
            $validate = new JobRegisterDataValidate();
            $params = $validate->getParamAll();

            $logic = new JobRegisterDataLogic();
            $result = $logic->getRegisterList($params);

            responseJson(200, '获取成功', $result);
        } catch (\Exception $e) {
            saveLog('获取失败-报错信息：' . json_encode(['Line' => $e->getLine(), 'File' => $e->getFile(), 'Message' => $e->getMessage()]));
            responseJson(400, '获取失败');
        }
    }

    /**
     * 获取报名详情
     * @access public
     * @author chenyang
     * @return array
     * Date Time：2022年4月22日14:29:58
     */
    public function getRegisterInfo() {
        try {
            // 获取参数
            $validate = new JobRegisterDataValidate();
            $params = $validate->getParamAll();

            $logic = new JobRegisterDataLogic();
            $result = $logic->getRegisterInfo($params);

            responseJson(200, '获取成功', $result);
        } catch (\Exception $e) {
            saveLog('获取失败-报错信息：' . json_encode(['Line' => $e->getLine(), 'File' => $e->getFile(), 'Message' => $e->getMessage()]));
            responseJson(400, '获取失败');
        }
    }

    /**
     * 设置处理状态
     * @access public
     * @author chenyang
     * @return array
     * Date Time：2022年5月7日18:55:22
     */
    public function setHandleStatus() {
        try {
            // 获取参数
            $validate = new JobRegisterDataValidate();
            $params = $validate->getParamAll();

            $logic = new JobRegisterDataLogic();
            $logic->setHandleStatus($params, $this->admininfo);

            responseJson(200, '保存成功');
        } catch (\Exception $e) {
            saveLog('保存失败-报错信息：' . json_encode(['Line' => $e->getLine(), 'File' => $e->getFile(), 'Message' => $e->getMessage()]));
            responseJson(400, '保存失败');
        }
    }

    /**
     * 设置备注
     * @access public
     * @author chenyang
     * @return array
     * Date Time：2022年5月9日09:14:26
     */
    public function setRemark() {
        try {
            // 获取参数
            $validate = new JobRegisterDataValidate();
            $params = $validate->getParamAll();

            $logic = new JobRegisterDataLogic();
            $logic->setRemark($params, $this->admininfo);

            responseJson(200, '保存成功');
        } catch (\Exception $e) {
            saveLog('保存失败-报错信息：' . json_encode(['Line' => $e->getLine(), 'File' => $e->getFile(), 'Message' => $e->getMessage()]));
            responseJson(400, '保存失败');
        }
    }

    /**
     * 删除报名信息
     * @access public
     * @author chenyang
     * @return array
     * Date Time：2022年5月9日09:42:37
     */
    public function delRegister() {
        try {
            // 获取参数
            $validate = new JobRegisterDataValidate();
            $params = $validate->getParamAll();

            $logic = new JobRegisterDataLogic();
            $logic->delRegister($params, $this->admininfo);

            responseJson(200, '删除成功');
        } catch (\Exception $e) {
            saveLog('删除失败-报错信息：' . json_encode(['Line' => $e->getLine(), 'File' => $e->getFile(), 'Message' => $e->getMessage()]));
            responseJson(400, '删除失败');
        }
    }

    /**
     * 导出报名信息
     * @access public
     * @author chenyang
     * @return array
     * Date Time：2022年5月9日17:54:15
     */
    public function exportRegisterData() {
        try {
            // 获取参数
            $validate = new JobRegisterDataValidate();
            $params = $validate->getParamAll();

            $logic = new JobRegisterDataLogic();
            $result = $logic->exportRegisterData($params);

            responseJson(200, '获取成功', $result);
        } catch (\Exception $e) {
            saveLog('获取失败-报错信息：' . json_encode(['Line' => $e->getLine(), 'File' => $e->getFile(), 'Message' => $e->getMessage()]));
            responseJson(400, '获取失败');
        }
    }
}
