<?php
/**
 * 渠道活码分组模型
 */

namespace app\common\model\corpwechat;

use app\common\model\BaseModel;

class CorpwechatChannelGroup extends BaseModel
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
    protected $updateTime = 'update_time';

    /**
     * @Purpose:
     * 模型初始化
     * @author  Administrator
     * @version 1.1
     * @since   2022/3/1
     */
    protected static function init()
    {
        self::event('after_write', function () {
            cache('cache_corpwechat_channel_group', null);
        });
        self::event('after_delete', function () {
            cache('cache_corpwechat_channel_group', null);
        });
        self::event('after_update', function () {
            cache('cache_corpwechat_channel_group', null);
        });
        self::event('after_insert', function () {
            cache('cache_corpwechat_channel_group', null);
        });
    }


    /**
     * @Purpose:
     * 获取渠道活码分组
     * @Method getCache()
     *
     * @param string $group_id
     *
     * @return array|false|mixed|string
     *
     * @throws null
     *
     * @link XXXXXXXXXX
     *
     * @author  Administrator
     * @version 1.1
     * @since   2022/3/11
     */
    public function getCache($group_id = '')
    {
        $data = cache('cache_corpwechat_channel_group');
        if (false === $data) {
            $data = $this->order('id DESC')
                ->field('id as group_id, name as group_name')
                ->select();
            if (null === $data) {
                cache('cache_corpwechat_channel_group', []);
            } else {
                $data = $data->toArray();
                cache('cache_corpwechat_channel_group', $data);
            }
        }
        if ($group_id != '') {
            $data = isset($data[$group_id]) ? $data[$group_id] : [];
        }
        return $data;
    }


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
     * @link XXXXXXXXXX
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
     * 获取用户列表数据
     * @param $map
     * @param $order
     * @param $page_num
     * @param $page_size
     * @param string $field
     * @return array|false
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

        $build_join_sql = model('corpwechat.CorpwechatChannel')
            ->field('group_id, count(id) as channel_total')
            ->group('group_id')
            ->buildSql();
        $data = $this->alias('cg')
            ->field($field)
            ->join([$build_join_sql => 'c'], 'c.group_id = cg.id', 'LEFT')
            ->field('IFNULL(c.channel_total,0) AS channel_total')
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