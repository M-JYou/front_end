<?php
/**
 * 外部联系人标签
 */

namespace app\common\model\corpwechat;

use app\common\model\BaseModel;

class CorpwechatExternalTag extends BaseModel
{
    protected $pk = 'id';

    protected $readonly = [
        'id'
    ];

    protected $type = [
        'id' => 'integer',
    ];

    // 设置返回数据集的对象名
    protected $resultSetType = 'collection';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;

    // 定义时间戳字段名
    protected $createTime = 'create_time';
}