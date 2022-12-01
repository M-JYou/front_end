<?php

namespace app\common\behavior;

class CrossDomain {
  public function run(&$params) {
    if ($r = getLegalOrigin()) {
      //跨域访问的时候才会存在此字段
      header('Access-Control-Allow-Origin:' . $r);
      header('Access-Control-Allow-Methods:POST,OPTIONS,GET');
      header('Access-Control-Allow-Credentials:true');
      header(
        'Access-Control-Allow-Headers:x-requested-with,content-type,x-token,safecode,sessionid,admintoken,user-token,platform,subsiteid'
      );
    }
  }
}
