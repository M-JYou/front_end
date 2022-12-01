<?php

namespace app\common\model\corpwechat;

use app\common\model\BaseModel;

class CorpwechatStaff extends BaseModel
{
    protected $pk = 'id';

    protected $readonly = [
        'id'
    ];

    protected $type = [
        'id' => 'integer',
        'department' => 'json'
    ];

    // 设置返回数据集的对象名
    protected $resultSetType = 'collection';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;

    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';


    protected static function init()
    {
        self::event('after_write', function () {
            cache('cache_staff_all', null);
        });
        self::event('after_delete', function () {
            cache('cache_staff_all', null);
        });
        self::event('after_update', function () {
            cache('cache_staff_all', null);
        });
        self::event('after_insert', function () {
            cache('cache_staff_all', null);
        });
    }


    /**
     * @Purpose:
     * 一对多关联【admin】
     * @Method adminInfo()
     */
    public function adminInfo()
    {
        return $this->hasOne('app\common\model\Admin', 'id', 'adminid')
            ->alias('user')
            ->field('id, username, qy_userid, bind_qywx, bind_qywx_time');
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
        $total = $this->alias('s')
            ->join('corpwechat_user_all u', 'u.userid = s.userid', 'LEFT')
            ->join('admin a', 'a.qy_userid = s.userid', 'LEFT')
            ->field('s.id')
            ->where($map)
            ->count('s.id');
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
        $build_join_sql = model('corpwechat.CorpwechatExternalUser')
            ->field('userid, count(id) as total_external')
            ->group('userid')
            ->buildSql();

        $data = $this->alias('s')
            ->join('corpwechat_user_all u', 'u.userid = s.userid', 'LEFT')
            ->join('admin a', 'a.qy_userid = s.userid', 'LEFT')
            ->join([$build_join_sql => 'c'], 'c.userid = s.userid', 'LEFT')
            ->field($field)
            ->where($map)
            ->order($order)
            ->limit($limit_start, $limit_size)
// ->with('adminInfo')
            ->select();

        if ($data->isEmpty()) {
            return array();
        }
        $return_data['rows'] = $data->toArray();
        foreach ($return_data['rows'] as $key => $value) {
            $customer_num = model('corpwechat.CorpwechatExternalUser')
                ->getExternalUserByUserId($value['userid']);
            $return_data['rows'][$key]['customer_num'] = $customer_num;
        }
        $return_data['pages'] = array(
            'now_page' => $page_num,
            'total_page' => $total_page,
            'record_num' => $total);
        return $return_data;
    }


    /**
     * @Purpose:
     * 获取所有员工
     * @Method getCache()
     *
     * @param null
     *
     * @return array|mixed
     *
     * @throws null
     *
     * @link XXXXXXXXXX
     *
     * @author  Administrator
     * @version 1.1
     * @since   2022/3/1
     */
    public function getCache()
    {
        $data = cache('cache_staff_all');

        if (false === $data) {
            $data = $this->alias('s')
                ->join('corpwechat_user_all u', 'u.userid = s.userid', 'LEFT')
                ->order('s.id asc')
                ->field('s.id,s.adminid,s.userid,u.name')
// ->field('id, adminid, userid, name')
                ->select();
            cache('cache_staff_all', $data);
        }

        return $data;
    }


    public function staffIsSet($userID)
    {
        $isSet = $this->where('userid', $userID)
            ->find();

        if (null === $isSet) {
            return false;
        } else {
            return true;
        }
    }


    public function writeStaff($userID, $staffInfo)
    {
        // 判断员工是否存在
        $isSet = $this->staffIsSet($userID);
        if (false === $isSet) {
            // 不存在，写入
            $result = $this->data($staffInfo)
                ->allowField(true)
                ->isUpdate(false)
                ->save();
        } else {
            // 存在，更新
            $result = $this->allowField(true)
                ->isUpdate(true)
                ->save(
                    $staffInfo,
                    [
                        'userid' => $userID
                    ]
                );
        }

        return $result;
    }
}