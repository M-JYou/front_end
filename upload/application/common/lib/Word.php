<?php

namespace app\common\lib;

use OSS\Core\OssException;
use OSS\OssClient;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;
use think\Config;
use think\File;

class Word {
  private $errorMsg;

  public function getHtml(File $file, int $uid, int $utype, $save = false) {
    if (!$file) {
      ext(['code' => 500, 'message' => '文件不存在']);
    }
    $info = $file->getInfo();
    if (!$file->getSaveName() && !$info) {
      $file->setSaveName($file->getFilename());
      $info = ['name' => $file->getFilename()];
      $file->setUploadInfo($info);
      $file->isTest(true);
    }
    $ret = [];

    try {
      // 获取文件路径
      // $tempDocx = ROOT_PATH . $this->savePath . $info->getSaveName();
      $tempDocx = $file->getPathname();
      IOFactory::load($tempDocx);
      $objWriter = IOFactory::createWriter(IOFactory::load($tempDocx), 'HTML');

      $tempHtml = SYS_UPLOAD_PATH .  md5(microtime(true)) . '.html';

      $objWriter->save($tempHtml);
      $html = file_get_contents($tempHtml);
      if ($save) {
        $filemanager = new \app\common\lib\FileManager(['filter' => 0]);
        $ret = $filemanager->upload($file, $uid, $utype);
      }
      // 删除临时文件
      register_shutdown_function(function () use ($tempHtml, $tempDocx) {
        try {
          unlink($tempHtml);
        } catch (\Throwable $th) {
          out([$tempHtml, $tempDocx, $th->getErrorMsg]);
        }
        try {
          unlink($tempDocx);
        } catch (\Throwable $th) {
        }
      });

      $html = $this->getHtmlBody($html);
      if ($html == '') {
        throw new \Exception('上传文件内容为空');
      }
      $html = $this->saveImage($html, $uid, $utype);
      $html = $this->clearStyle($html);
      $html = $this->clearSpan($html);
    } catch (\Exception $e) {
      $this->errorMsg = $e->getMessage();
      return false;
    }

    $ret['html'] = $html;
    $ret['name'] = $info['name'];
    return $ret;
  }

  /** 匹配出body的内容
   * @param $html
   * @return string
   */
  private function getHtmlBody($html) {
    preg_match('/<body>([\s\S]*)<\/body>/', $html, $matches);
    return isset($matches[1]) ? $matches[1] : '';
  }

  /** 图片处理
   * @param $html
   * @return mixed
   */
  private function saveImage($html, int $uid, int $utype) {
    // 匹配图片
    preg_match_all('/<img[^>]*src="([\s\S]*?)"\/>/', $html, $imageMatches);
    if (!$imageMatches[1]) {
      return $html;
    }
    $imageUrl = [];
    foreach ($imageMatches[1] as $image) {
      $imageUrl[] = $this->saveBase64Image($image, $uid, $utype);
    }
    return str_replace($imageMatches[1], $imageUrl, $html);
  }

  private function saveBase64Image($content, $uid, $utype) {
    preg_match('/^(data:\s*image\/(\w+);base64,)/', $content, $result);
    // 设置文件名称。
    $name = SYS_UPLOAD_PATH .  md5(microtime(true)) . '.' . $result[2];
    $content = base64_decode(str_replace($result[1], '', $content));

    $fp = fopen($name, 'w');
    fwrite($fp, $content);
    fclose($fp);

    $file = new File($name);
    $file->isTest(true);
    $file->setUploadInfo(['name' => $name]);
    $filemanager = new \app\common\lib\FileManager(['filter' => 0]);
    $result = $filemanager->upload($file, $uid, $utype, false);
    return $result ? $result['file_url'] : '';
  }

  /** 清除p,span标签的样式
   * @param $content
   * @return null|string|string[]
   */
  private function clearStyle($content) {
    $patterns = array(
      '/<(p|span)[\s\S]*?>/i',
    );
    return preg_replace($patterns, '<$1>', $content);
  }

  /** 清除span标签
   * @param $content
   * @return null|string|string[]
   */
  private function clearSpan($content) {
    $patterns = array(
      '/<span[\s\S]*?>/i',
      '/<\/span>/i',
    );
    return preg_replace($patterns, '', $content);
  }

  /** @return mixed */
  public function getErrorMsg() {
    return $this->errorMsg;
  }
}
