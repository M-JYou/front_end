<?php

namespace app\common\model;

class CampusElection extends \app\common\model\BaseModel {
    public function timecase_map($timecase) {
        switch (intval($timecase)) {
            case 1:  // 今天
                $s = strtotime(date('Y-m-d 00:00:00'));
                $e = strtotime(date('Y-m-d 23:59:59'));
                return array('between', array($s, $e));
            case 2:  // 明天
                $time = strtotime('+1 day');
                $s = strtotime(date('Y-m-d 00:00:00', $time));
                $e = strtotime(date('Y-m-d 23:59:59', $time));
                return array('between', array($s, $e));
            case 3:  // 一周内
                return array('between', array(time(), strtotime('+7 day')));
            case 4:  // 一月内
                return array('between', array(time(), strtotime('+30 day')));
            case 5:  // 三月内
                return array('between', array(time(), strtotime('+90 day')));
            case 6:  // 已举办
                return array('lt', time());
            case 7:  // 即将举办
                return array('gt', time());
        }
    }
}
