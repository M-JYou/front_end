<?php

namespace app;

use Exception;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\ErrorException;
use think\exception\PDOException;
// use think\facade\Log;
use think\Log;

class ExceptionHandler extends Handle {
  public function render1(Exception $e) {
    $ret = [];
    $ret = ['code' => $e->getCode(), 'message' => $e->getMessage()];
    if (!$ret['code'] || $ret['code'] == 200) {
      $ret['code'] = 400;
    }
    return json($ret);

    // if (config('app_debug')) {
    // return parent::render($e);
    // } else {
    // $log['apiError'] = $this->getApiError($e);
    // $log['getData'] = $_GET;
    // $log['postData'] = $_POST;
    // $log['headerData'] = $_SERVER;
    // $re = $this->recordErrorLog($log);

    // if ($e instanceof HttpException) {
    // return json(array('msg' => '请求错误', 'code' => 400));
    // }
    // if ($e instanceof ErrorException) {
    // return json(array('msg' => '返回异常', 'code' => 500));
    // }

    // if ($e instanceof PDOException) {
    // return json(array('msg' => "SQL异常", 'code' =>  600));
    // }
    // }
  }

  // private function getApiError($e) {
  // $data = [];
  // if ($e instanceof HttpException) {
  // $data['msg'] = $e->getMessage();
  // }
  // if ($e instanceof ErrorException) {
  // $data['msg'] = $e->getMessage();
  // $data['file'] = $e->getFile();
  // $data['line'] = $e->getLine();
  // }

  // if ($e instanceof PDOException) {

  // $data['msg'] = $e->getData('Database Status');
  // }

  // return $data;
  // }

  // private function recordErrorLog($data) {
  // Log::record($data, 'error');
  // }
}
