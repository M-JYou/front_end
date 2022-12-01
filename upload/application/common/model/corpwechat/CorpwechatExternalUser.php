<?php

namespace app\common\model\corpwechat;

use app\common\model\BaseModel;

class CorpwechatExternalUser extends BaseModel
{
    protected $pk = 'id';

    protected $readonly = [
        'id'
    ];

    protected $type = [
        'id' => 'integer',
        'tags' => 'json',
        'tag_group' => 'json'
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
     * 一对多关联【Admin】
     * @Method userList()
     */
    public function tagsList()
    {
        return $this->hasMany('CorpwechatExternalTag',
            'external_user_id',
            'external_user_id');
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
        return $this->alias('e')
            ->field('e.id')
            ->join('corpwechat_user_all u', 'u.userid = e.external_user_id', 'LEFT')
            ->join('corpwechat_user_all s', 's.userid = e.userid', 'LEFT')
            ->join('member_bind mb', 'mb.unionid = u.unionid', 'LEFT')
            ->join('member m', 'm.uid = mb.uid', 'LEFT')
            ->where($map)
            ->count('e.id');
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
    public function getList($map = [], $order = ['u.id DESC'], $page_num = 1, $page_size = 10, $field = '*')
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
        $data = $this->alias('e')
            ->join('corpwechat_user_all u', 'u.userid = e.external_user_id', 'LEFT')
            ->join('corpwechat_user_all s', 's.userid = e.userid', 'LEFT')
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
        $return_data['rows'] = $data->toArray();
        $return_data['pages'] = array(
            'now_page' => $page_num,
            'total_page' => $total_page,
            'record_num' => $total);
        return $return_data;
    }


    /**
     * @Purpose:
     * 获取员工客户数量
     * @Method getExternalUserByUserId()
     *
     * @param int $userId
     * @param int $type
     *
     * @return array|int|string
     *
     * @throws \think\Exception
     *
     * @link XXXXXXXXXX
     *
     * @author  Administrator
     * @version 1.1
     * @since   2022/3/10
     */
    public function getExternalUserByUserId($userId = 0, $type = 0)
    {
        // 注册状态[3:非平台用户;1:企业用户;2:个人用户;]
        switch ($type) {
            case 1:
                $company = $this->alias('e')
                    ->join('corpwechat_user_all u', 'u.userid = e.external_user_id', 'LEFT')
                    ->join('member_bind mb', 'mb.unionid = u.unionid', 'LEFT')
                    ->join('member m', 'm.uid = mb.uid', 'LEFT')
                    ->where('mb.type', '=', 'weixin')
                    ->where('m.utype', 1)
                    ->where('e.userid', $userId)
                    ->count('e.id');
                $data = $company;
                break;

            case 2:
                $member = $this->alias('e')
                    ->join('corpwechat_user_all u', 'u.userid = e.external_user_id', 'LEFT')
                    ->join('member_bind mb', 'mb.unionid = u.unionid', 'LEFT')
                    ->join('member m', 'm.uid = mb.uid', 'LEFT')
                    ->where('mb.type', '=', 'weixin')
                    ->where('m.utype', 2)
                    ->where('e.userid', $userId)
                    ->count('e.id');
                $data = $member;
                break;

            case 3:
                $ordinary = $this->alias('e')
                    ->join('corpwechat_user_all u', 'u.userid = e.external_user_id', 'LEFT')
                    ->join('member_bind mb', 'mb.unionid = u.unionid', 'LEFT')
                    ->join('member m', 'm.uid = mb.uid', 'LEFT')
                    ->where('mb.type', '=', 'weixin')
                    ->where('m.utype', 'EXP', ' IS NULL ')
                    ->where('e.userid', $userId)
                    ->count('e.id');
                $data = $ordinary;
                break;

            case 0:
            default:
                $company = $this->alias('e')
                    ->join('corpwechat_user_all u', 'u.userid = e.external_user_id', 'LEFT')
                    ->join('member_bind mb', 'mb.unionid = u.unionid', 'LEFT')
                    ->join('member m', 'm.uid = mb.uid', 'LEFT')
                    ->where('mb.type', '=', 'weixin')
                    ->where('m.utype', 1)
                    ->where('e.userid', $userId)
                    ->count('e.id');
                $member = $this->alias('e')
                    ->join('corpwechat_user_all u', 'u.userid = e.external_user_id', 'LEFT')
                    ->join('member_bind mb', 'mb.unionid = u.unionid', 'LEFT')
                    ->join('member m', 'm.uid = mb.uid', 'LEFT')
                    ->where('mb.type', '=', 'weixin')
                    ->where('m.utype', 2)
                    ->where('e.userid', $userId)
                    ->count('e.id');
                $ordinary = $this->alias('e')
                    ->join('corpwechat_user_all u', 'u.userid = e.external_user_id', 'LEFT')
                    ->join('member_bind mb', 'mb.unionid = u.unionid', 'LEFT')
                    ->join('member m', 'm.uid = mb.uid', 'LEFT')
                    ->where('m.utype', 'EXP', ' IS NULL ')
                    ->where('e.userid', $userId)
                    ->count('e.id');
                $data = [
                    'ordinary' => $ordinary,
                    'company' => $company,
                    'member' => $member
                ];
                break;
        }

        return $data;
    }


    public function getExternalUserTotal($distinct = false)
    {
        $model = $this->field('id');
        if ($distinct) {
            $total = $model
                ->group('external_user_id')
                ->count('id');
        } else {
            $total = $model->count('id');
        }

        return intval($total);
    }


    public function getRegisterExternalUser()
    {
        // 注册状态[0:全部;3:非平台用户;1:企业用户;2:个人用户;]
        return $this->alias('e')
            ->join('corpwechat_user_all u', 'u.userid = e.external_user_id', 'LEFT')
            ->join('member_bind mb', 'mb.unionid = u.unionid', 'LEFT')
            ->join('member m', 'm.uid = mb.uid', 'LEFT')
            ->group('e.external_user_id')
            ->where('m.utype', 'IN', [1, 2])
            ->count('e.id');
    }

}