<?php

namespace app\common\model;

class CampusSchool extends \app\common\model\BaseModel {
    /** 院校层次 */
    public $map_level = [
        1 => '211高校',
        2 => '985高校',
        3 => '中央部属高校',
        4 => '省属本科',
        5 => '独立院校',
        6 => '民办高校',
        7 => '高职/高专',
    ];
    /** 院校类型 */
    public $map_type = [
        1 => '综合类',
        2 => '艺术类',
        3 => '体育类',
        4 => '理工类',
        5 => '师范类',
        6 => '农林类',
        7 => '政法类',
        8 => '医药类',
        9 => '财经类',
    ];
}
