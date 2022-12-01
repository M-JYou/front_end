<?php

namespace app\common\model\corpwechat;

use app\common\model\BaseModel;

class CorpwechatChannel extends BaseModel
{
    protected $pk = 'id';

    protected $readonly = [
        'id'
    ];

    protected $type = [
        'id' => 'integer',
        'channel_tag' => 'json',
        'user_list' => 'json'
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
     * 获取数据总条数
     * @Method getDataNum()
     *
     * @param array $map 查询条件
     *
     * @return false|int|string
     *
     * @throws \think\Exception
     *
     * @author  Administrator
     * @version 1.1
     * @since   2022/3/3
     */
    public function getDataNum($map)
    {
        if (!is_array($map)) {
            return false;
        }
        $total = $this->field($this->pk)
            ->where($map)
            ->count($this->pk);
        return $total;
    }


    /**
     * @Purpose:
     * 获取数据列表
     * @Method getList()
     *
     * @param array $map
     * @param string[] $order
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
     * @author  Administrator
     * @version 1.1
     * @since   2022/3/14
     */
    public function getList($map = [], $order = ['id DESC'], $page_num = 1, $page_size = 10, $field = '*')
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
        $data = $this->field($field)
            ->where($map)
            ->order($order)
            ->limit($limit_start, $limit_size)
            ->select();
        if ($data->isEmpty()) {
            return array();
        }
        $return_data['rows'] = $data->toArray();
        $return_data['pages'] = array(
            'now_page' => $page_num,
            'total_page' => $total_page,
            'record_num' => $total);
        return $return_data;
    }
}