<?php

namespace app\common\model\corpwechat;

use app\common\model\BaseModel;

class CorpwechatUserAll extends BaseModel
{
    protected $pk = 'id';

    protected $readonly = [
        'id'
    ];

    protected $type = [

    ];

    // 设置返回数据集的对象名
    protected $resultSetType = 'collection';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;

    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';


    /**
     * @Purpose:
     * 判断企业微信UserID是否存在
     * @Method isSetUserID()
     *
     * @param string $userID 企业微信UserID
     * @param integer $userType 成员类型[1:企业成员;2:外部联系人;]
     *
     * @return bool
     *
     * @author  Administrator
     * @version 1.1
     * @since   2022/3/22
     */
    public function isSetUserID($userID, $userType)
    {
        $isSet = $this->where('userid', $userID)
            ->where('user_type', $userType)
            ->find();

        if (null === $isSet) {
            return false;
        } else {
            return true;
        }
    }

    public function write($userID, $userType, $userInfo)
    {
        // 1.判断UserID是否存在
        $isSet = $this->isSetUserID($userID, $userType);

        if (false === $isSet) {
            // 2.不存在，写入
            $result = $this->allowField(true)
                ->data($userInfo)
                ->allowField(true)
                ->isUpdate(false)
                ->save();
        } else {
            return true;
        }

        return $result;
    }

    public function writeUserAll($userID, $userType, $userInfo)
    {
        // 1.判断UserID是否存在
        $isSet = $this->isSetUserID($userID, $userType);

        if (false === $isSet) {
            // 2.不存在，写入
            $result = $this->allowField(true)
                ->isUpdate(false)
                ->save($userInfo);
        } else {
            $result = $this->allowField(true)
                ->isUpdate(true)
                ->save(
                    $userInfo,
                    [
                        'userid' => $userID,
                        'user_type' => $userType
                    ]
                );
        }

        return $result;
    }
}