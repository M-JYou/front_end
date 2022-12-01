<?php

namespace app\common\model\corpwechat;

use app\common\model\BaseModel;

class CorpwechatMsgTemplate extends BaseModel
{
    protected $pk = 'id';

    protected $readonly = [
        'id'
    ];

    protected $type = [
        'id' => 'integer',
        'type' => 'integer',
        'external_userids' => 'json',
        'fail_list' => 'json'
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
        $total = $this->field($this->pk)
            ->where($map)
            ->count($this->pk);
        return $total;
    }


    /**
     *
     * @param $map
     * @param $order
     * @param $page_num
     * @param $page_size
     * @param string $field
     * @return array|false
     */
    /**
     * @Purpose:
     * 获取消息推送任务列表
     * @Method getList()
     *
     * @param array $map 查询条件
     * @param string $order 排序
     * @param int $page_num 页数
     * @param int $page_size 每页数据条数
     * @param string $field 查询字段
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
     * @since   2022/3/7
     */
    public function getList($map = [], $order = 'ID DESC', $page_num = 1, $page_size = 10, $field = '*')
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
            /*$page_num = $total_page;
            $total_page = $total_page;*/
        }
        if (empty($page_num) || $page_num < 1) {
            $page_num = 1;
            $start = 0;
        } else {
            $start = (int)$page_num - 1;
        }
        $limit_start = $start * $limit_size;
        //$order = array('sort' => 'DESC');
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