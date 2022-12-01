<?php

namespace app\common\model\corpwechat;

use app\common\model\BaseModel;

class CorpwechatPushLog extends BaseModel
{
    // 主键
    protected $pk = 'id';

    protected $readonly = [
        'id'
    ];

    // 设置返回数据集的对象名
    protected $resultSetType = 'collection';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;

    // 定义时间戳字段名
    protected $createTime = 'create_time';

    /**
     * @Purpose:
     * 获取数据总条数
     * @Method getDataNum()
     *
     * @param array $map 查询条件
     *
     * @return false|int|string
     *
     * @author  Administrator
     * @version 1.1
     * @since   2022/3/30
     */
    public function getDataNum($map)
    {
        if (!is_array($map)) {
            return false;
        }
        $total = $this->alias('l')
            ->join('corpwechat_user_all u', 'u.userid = l.external_user_id', 'LEFT')
            ->field('l.id')
            ->where($map)
            ->count('l.id');
        return $total;
    }


    /**
     * 获取用户列表数据
     * @param $map
     * @param $order
     * @param $page_num
     * @param $page_size
     * @param string $field
     * @return array|false
     */
    public function getList($map, $order, $page_num, $page_size, $field = '*')
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
        $data = $this->alias('l')
            ->join('corpwechat_user_all u', 'u.userid = l.external_user_id', 'LEFT')
            ->join('corpwechat_user_all s', 's.userid = l.userid', 'LEFT')
            ->join('member_bind mb', 'mb.unionid = u.unionid', 'LEFT')
            ->join('member m', 'm.uid = mb.uid', 'LEFT')
            ->field($field)
            ->where($map)
            ->order($order)
            ->limit($limit_start, $limit_size)
            ->select();

        if ($data->isEmpty()) {
            return array();
        }

        return [
            'rows' => $data->toArray(),
            'pages' => [
                'now_page' => $page_num,
                'total_page' => $total_page,
                'record_num' => $total
            ]
        ];
    }
}