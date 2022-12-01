<?php

namespace app\common\lib\corpwechat;

use app\common\lib\corpwechat\promise\Corp;

class Media extends Corp {
  /**
   * @Purpose
   * 上传图片
   * @var string
   */
  const UPLOAD_IMG = 'https://qyapi.weixin.qq.com/cgi-bin/media/uploadimg';


  /**
   * @Purpose:
   * 上传图片
   * @Method uploadImage()
   *
   * @param array $media 文件对象
   *
   * @return array|false|mixed
   *
   * @throws null
   *
   * @link https://qyapi.weixin.qq.com/cgi-bin/media/uploadimg?access_token=ACCESS_TOKEN
   *
   * @author  Administrator
   * @version 1.1
   * @since   2022/3/2
   */
  public function uploadImage($multipart) {
    return $this->callMultipart(self::UPLOAD_IMG, $multipart);
  }
}
