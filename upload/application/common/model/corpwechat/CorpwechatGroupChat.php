<?php

namespace app\common\model\corpwechat;

use app\common\model\BaseModel;

class CorpwechatGroupChat extends BaseModel
{
    protected $pk = 'id';

    protected $readonly = [
        'id',
        'chat_id'
    ];

    protected $type = [
        'id' => 'integer',
        'notice' => 'string'
    ];

    // 设置返回数据集的对象名
    protected $resultSetType = 'collection';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;

    // 定义时间戳字段名
    protected $createTime = 'input_time';
    protected $updateTime = 'update_time';

    /**
     * @Purpose:
     * 获取数据总条数
     * @Method getDataNum()
     *
     * @param $map
     *
     * @return false|int|string
     *
     * @throws \think\Exception
     *
     * @link XXXXXXXXXX
     *
     * @author  Administrator
     * @version 1.1
     * @since   2022/3/7
     */
    public function getDataNum($map)
    {
        if (!is_array($map)) {
            return false;
        }
        $total = $this->alias('g')
            ->join('corpwechat_user_all u', 'u.userid = g.owner', 'LEFT')
            ->field('g.id')
            ->where($map)
            ->count('g.id');
        return $total;
    }


    /**
     * @Purpose:
     * 获取客户群分页
     * @Method getList()
     *
     * @param array $map
     * @param string $order
     * @param int $page_num
     * @param int $page_size
     * @param string $field
     *
     * @return array|false
     *
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     *
     * @link XXXXXXXXXX
     *
     * @author  Administrator
     * @version 1.1
     * @since   2022/3/22
     */
    public function getList($map = [], $order = 'g.id DESC', $page_num = 1, $page_size = 10, $field = '*')
    {
        if (!is_array($map)) {
            return false;
        }
        $total = $this->getDataNum($map);
        if (empty($total)) {
            return array();
        }
        if (empty($page_size) || $page_size > 100 || $page_size < 1) {
            $page_size = 10;
            $limit_size = 10;
        } else {
            $limit_size = (int)$page_size;
        }
        $total_page = ceil($total / $page_size);
        if ($page_num > $total_page) {
            return array();
        }
        if (empty($page_num) || $page_num < 1) {
            $page_num = 1;
            $start = 0;
        } else {
            $start = (int)$page_num - 1;
        }
        $limit_start = $start * $limit_size;
        $data = $this->alias('g')
            ->join('corpwechat_user_all u', 'u.userid = g.owner', 'LEFT')
            ->field($field)
            ->where($map)
            ->order($order)
            ->limit($limit_start, $limit_size)
            ->select();
        if ($data->isEmpty()) {
            return array();
        } else {
            $rows = $data->toArray();
        }

        foreach ($rows as $key => $data) {
            $rows[$key]['add_change_cnt'] = $this->getTodayMemChangeCnt($data['chat_id'], 1);
            $rows[$key]['del_change_cnt'] = $this->getTodayMemChangeCnt($data['chat_id'], 2);
            $rows[$key]['register_num'] = $this->getRegisterNum($data['chat_id']);
        }

        $return_data['rows'] = $rows;
        $return_data['pages'] = array(
            'now_page' => $page_num,
            'total_page' => $total_page,
            'record_num' => $total);
        return $return_data;
    }


    /**
     * @Purpose:
     * 获取客户群当日入群或退群成员变更数量
     * @Method getTodayMemChangeCnt()
     *
     * @param string $chatId 客群群ID
     * @param int $type [1:成员入群;2:成员退群]
     *
     * @return int
     *
     * @author  Administrator
     * @version 1.1
     * @since   2022/3/22
     */
    public function getTodayMemChangeCnt($chatId, $type = 1)
    {
        $mem_change_cnt = model('corpwechat.CorpwechatGroupLog')
            ->where('chat_id', $chatId)
            ->where('type', $type)
            ->whereTime('create_time', 'today')
            ->count('mem_change_cnt');

        return intval($mem_change_cnt);
    }


    /**
     * @Purpose:
     * 获取客户群注册用户数
     * @Method getRegisterNum()
     *
     * @param string $chatId 客户群ID
     *
     * @return int
     *
     * @author  Administrator
     * @version 1.1
     * @since   2022/3/22
     */
    public function getRegisterNum($chatId)
    {
        $member_list = model('corpwechat.CorpwechatGroupUser')
            ->where('chat_id', $chatId)
            ->where('type', 2)
            ->column('userid');

        $register_num = model('corpwechat.CorpwechatUserAll')
            ->alias('u')
            ->join('member_bind mb', 'mb.unionid = u.unionid', 'LEFT')
            ->join('member m', 'm.uid = mb.uid', 'LEFT')
            ->where('mb.type', '=', 'weixin')
            ->where('m.utype', 'IN', [1, 2])
            ->where('u.user_type', 2)
            ->where('u.userid', 'IN', $member_list)
            ->count('u.id');

        return intval($register_num);
    }

    /**
     * @Purpose:
     * 获取客户群总数
     * @Method getTotalGroupChart()
     *
     * @param null
     *
     * @author  Administrator
     * @version 1.1
     * @since   2022/3/22
     */
    public function getTotalGroupChart()
    {
        $total = $this->field($this->pk)
            ->count($this->pk);

        return intval($total);
    }


    /**
     * @Purpose:
     * 获取群好友数
     * @Method getExternalUserTotal()
     *
     * @param string $chatId 空:所有群|非空：当前群
     *
     * @return int
     *
     * @author  Administrator
     * @version 1.1
     * @since   2022/3/22
     */
    public function getExternalUserTotal($chatId = '')
    {
        $groupUserModel = model('corpwechat.CorpwechatGroupUser')
            ->where('type', 2);
        if (isset($chatId) && !empty($chatId)) {
            $member_list = $groupUserModel->where('chat_id', $chatId)
                ->column('userid');
        } else {
            $member_list = $groupUserModel->column('userid');
        }

        $external_user_total = model('corpwechat.CorpwechatExternalUser')
            ->where('external_user_id', 'IN', $member_list)
            ->group('external_user_id')
            ->count('id');

        return intval($external_user_total);

    }


    public function write($chatId, $groupChart)
    {
        // 1.判断客户群是否存在
        $isSet = $this->isSetGroupChart($chatId);

        if (false === $isSet) {
            // 2.不存在，写入
            $result = $this->allowField(true)
                ->isUpdate(false)
                ->save($groupChart);
        } else {
            // 3.存在，更新
            $result = $this->allowField(true)
                ->isUpdate(true)
                ->save(
                    $groupChart,
                    [
                        'chat_id' => $chatId
                    ]
                );
        }

        return $result;
    }


    /**
     * @Purpose:
     * 判断客户群是否存在
     * @Method isSetGroupChart()
     *
     * @param string $chatId 客户群ID
     *
     * @return bool
     *
     * @author  Administrator
     * @version 1.1
     * @since   2022/3/22
     */
    public function isSetGroupChart($chatId)
    {
        $isSet = $this->where('chat_id', $chatId)
            ->find();

        if (null === $isSet) {
            return false;
        } else {
            return true;
        }
    }
}