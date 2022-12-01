<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/5/11
 * Time: 16:56
 */

namespace app\common\model;


class CityinfoRefreshLog extends BaseModel {
    protected $autoWriteTimestamp = true;
    protected $createTime = 'addtime';
    protected $updateTime = 'updatetime';
    protected $readonly = ['id'];

    public function freeTimes($uid) {
        $curDate = date('Y-m-d');
        $freeTimes = intval(config('global_config.cityinfo_free_refresh_time'));

        if ($freeTimes <= 0) return 0;

        $one = $this->where(['date' => $curDate, 'uid' => $uid])->find();
        if (!$one) {
            return $freeTimes;
        }
        return $freeTimes - $one['refresh_times'];
    }

    public function incTimes($uid) {
        $curDate = date('Y-m-d');
        $where = ['date' => $curDate, 'uid' => $uid];
        $one = $this->where($where)->find();
        if (!$one) {
            $this->save([
                'date' => $curDate,
                'refresh_times' => 1,
                'uid' => $uid
            ]);
        } else {
            $this->where($where)->setInc('refresh_times');
        }
    }
}
