<?php

namespace app\common\model\corpwechat;

use app\common\model\BaseModel;

define('ADD_MEMBER', 1); // 成员入群
define('DEL_MEMBER', 2); // 成员退群

class CorpwechatGroupLog extends BaseModel
{
    public function record($chatId, $memChangeCnt = 0, $type = 1, $createTime = '')
    {
        $this->data([
            'chat_id' => $chatId,
            'mem_change_cnt' => $memChangeCnt,
            'type' => $type,
            'create_time' => isset($createTime) ? $createTime : time()
        ])->allowField(true)
            ->isUpdate(false)
            ->save();
    }
}