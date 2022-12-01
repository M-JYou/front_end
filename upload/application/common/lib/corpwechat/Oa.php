<?php

namespace app\common\lib\corpwechat;

use app\common\lib\corpwechat\promise\Corp;

class Oa extends Corp {
  const GET_CHECKIN_OPTION = 'https://qyapi.weixin.qq.com/cgi-bin/checkin/getcheckinoption';
  const GET_CHECKIN_DATA = 'https://qyapi.weixin.qq.com/cgi-bin/checkin/getcheckindata';
  const GET_APPROVAL_INFO = 'https://qyapi.weixin.qq.com/cgi-bin/oa/getapprovalinfo';
  const  GET_APPROVAL_DATA = " https://qyapi.weixin.qq.com/cgi-bin/oa/getapprovaldetail";

  public function CheckinOptionGet($datetime, $useridlist) {
    $args = array("datetime" => $datetime, "useridlist" => $useridlist);
    return $this->callPost(self::GET_CHECKIN_OPTION, $args);
  }

  public function CheckinDataGet($opencheckindatatype, $starttime, $endtime, $useridlist) {
    $args = array(
      "opencheckindatatype" => $opencheckindatatype,
      "starttime" => $starttime,
      "endtime" => $endtime,
      "useridlist" => $useridlist,
    );
    return $this->callPost(self::GET_CHECKIN_DATA, $args);
  }

  public function ApprovalDataGet($sp_no) {
    return $this->callPost(self::GET_APPROVAL_DATA, ['sp_no' => $sp_no]);
  }

  public function ApprovalInfoGet($sp_no) {
    return $this->callPost(self::GET_APPROVAL_INFO, ['sp_no' => $sp_no]);
  }
}
