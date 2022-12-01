<?php

namespace app\common\model\corpwechat;

use app\common\model\BaseModel;

define('ADD_EXTERNAL_CONTACT', 1);
define('DEL_EXTERNAL_CONTACT', 2);
define('DEL_FOLLOW_USER', 3);

class CorpwechatExternalLog extends BaseModel
{
    // 设置返回数据集的对象名
    protected $resultSetType = 'collection';

    // 时间戳自动转换
    protected $dateFormat = false;

    public function log($content, $externalUserId, $type = 0, $userId = 0)
    {
        $data['external_user_id'] = $externalUserId;
        $data['userid'] = $userId;
        $data['content'] = $content;
        $data['type'] = $type;
        $data['create_time'] = time();
        $this->save($data);
    }
}