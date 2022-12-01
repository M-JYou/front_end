<?php

namespace app\common\model\corpwechat;

use app\common\model\BaseModel;

class CorpwechatUseridChangeLog extends BaseModel
{
    protected $pk = 'id';

    protected $readonly = [
        'id'
    ];

    protected $type = [
        'id' => 'integer'
    ];

    // 设置返回数据集的对象名
    protected $resultSetType = 'collection';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;

    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    public function log($userID, $newUserID)
    {
        $this->data([
            'userid' => $userID,
            'new_userid' => $newUserID,
            'create_time' => time()
        ])
            ->allowField(true)
            ->isUpdate(false)
            ->save();

    }
}