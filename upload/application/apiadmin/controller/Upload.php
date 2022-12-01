<?php

/** 上传 */

namespace app\apiadmin\controller;

use app\common\lib\FileManager;
use app\common\lib\Qiniu;
use app\common\lib\Wechat;
use app\common\lib\Word;

class Upload extends \app\common\controller\Backend {
  public function index() {
    $file = input('file.file');
    if (!$file) {
      $this->ajaxReturn(500, '请选择文件');
    }
    $filemanager = new FileManager();
    $result = $filemanager->upload($file, $this->admininfo->id, 1, !input('?post.short'));
    if (false !== $result) {
      $this->ajaxReturn(200, '上传成功', $result);
    } else {
      $this->ajaxReturn(500, $filemanager->getError());
    }
  }

  public function checkQiniu() {
    try {
      $qiniu = new Qiniu();
      $domains = $qiniu->getDomains();

      if (empty($domains[0])) exception('七牛配置有误');
      $realDomains = is_array($domains[0]) ? $domains[0] : $domains;

      $config = config('global_config.account_qiniu');
      if (empty($config['domain'])) exception('七牛配置有误.');
      $find = false;
      foreach ($realDomains as $v) {
        if ($v == $config['domain']) {
          $find = true;
        }
      }
      if (!$find) exception('七牛域名配置有误');
      $this->ajaxReturn(200, '', $domains);
    } catch (\Exception $e) {
      $this->ajaxReturn(400, $e->getMessage());
    }
  }

  public function wechatMedia() {
    $file = input('file.file');
    if (!$file) {
      $this->ajaxReturn(500, '请选择文件');
    }
    $filemanager = new FileManager();
    $result = $filemanager->uploadReturnPath($file);
    if (false !== $result) {
      $instance = new Wechat();
      $res = $instance->uploadMedia($result['save_path']);
      if ($res !== false) {
        $this->ajaxReturn(200, '上传成功', $res);
      } else {
        $this->ajaxReturn(500, $instance->getError());
      }
    } else {
      $this->ajaxReturn(500, $filemanager->getError());
    }
  }
  public function editor() {
    $returnJson = [
      'errno' => 500,
      'data' => []
    ];
    $file = input('file.');
    do {
      if (!$file) {
        break;
      }
      $file = array_values($file);
      $file = $file[0];
      $filemanager = new FileManager();
      $result = $filemanager->upload($file, $this->admininfo->id, 1);
      if (false !== $result) {
        $returnJson = [
          'errno' => 0,
          'data' => [
            [
              "url" => $result['file_url'],
              "alt" => "",
              "href" => ""
            ]
          ]
        ];
        break;
      } else {
        break;
      }
    } while (0);
    exit(JSON_ENCODE($returnJson));
  }
  public function editorVideo() {
    $returnJson = [
      'errno' => 500,
      'data' => []
    ];
    $file = input('file.');
    do {
      if (!$file) {
        break;
      }
      $file = array_values($file);
      $file = $file[0];
      $filemanager = new FileManager();
      $result = $filemanager->uploadVideoReturnPath($file);
      if (false !== $result) {
        $returnJson = [
          'errno' => 0,
          'data' => [
            "url" => config('global_config.sitedomain') . config('global_config.sitedir') . 'upload/' . $result['save_path']
          ]
        ];
        break;
      } else {
        break;
      }
    } while (0);
    exit(JSON_ENCODE($returnJson));
  }
  public function attach() {
    $file = input('file.file');
    if (!$file) {
      $this->ajaxReturn(500, '请选择文件');
    }
    $filemanager = new FileManager();
    $result = $filemanager->uploadReturnPath($file, true);
    if (false !== $result) {
      $this->ajaxReturn(200, '上传成功', ['url' => $result['save_path'], 'name' => $file->getInfo()['name'], 'other' => $result]);
    } else {
      $this->ajaxReturn(500, $filemanager->getError());
    }
  }
  public function poster() {
    $file = input('file.file');
    if (!$file) {
      $this->ajaxReturn(500, '请选择文件');
    }
    $filemanager = new FileManager(['fileupload_type' => 'default']);
    $result = $filemanager->uploadReturnPath($file);
    if (false !== $result) {
      $this->ajaxReturn(200, '上传成功', $result['save_path']);
    } else {
      $this->ajaxReturn(500, $filemanager->getError());
    }
  }
  public function ad() {
    $file = input('file.file');
    if (!$file) {
      $this->ajaxReturn(500, '请选择文件');
    }
    $filemanager = new FileManager(['filter' => 0]);
    $result = $filemanager->upload($file, $this->admininfo->id, 1);
    if (false !== $result) {
      $this->ajaxReturn(200, '上传成功', $result);
    } else {
      $this->ajaxReturn(500, $filemanager->getError());
    }
  }
  public function word() {
    $w = new Word();
    $ret = $w->getHtml(input('file.file'), $this->admininfo->id, 1, input('post.save/b', false) && strcasecmp(input('post.save/s', 'false'), 'false'));
    // return $ret;
    $this->ajaxReturn($ret ? 200 : 500, $ret ? '上传成功' : $w->getErrorMsg(), $ret);
  }
}
