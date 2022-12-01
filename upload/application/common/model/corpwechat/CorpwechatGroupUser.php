<?php
/**
 * 客户群成员表
 */

namespace app\common\model\corpwechat;

use app\common\model\BaseModel;

class CorpwechatGroupUser extends BaseModel
{
    // 设置返回数据集的对象名
    protected $resultSetType = 'collection';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;

    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    /**
     * @Purpose:
     * 获取客户群去重客户总数
     * @Method getDeWeightTotal()
     *
     * @param null
     *
     * @author  Administrator
     * @version 1.1
     * @since   2022/3/22
     */
    public function getDeWeightTotal()
    {
        $de_weight_total = $this->where('type', 2)
            ->field('id')
            ->group('userid')
            ->count('id');

        return intval($de_weight_total);
    }


    /**
     * @Purpose:
     * 获取客户群客户总数
     * @Method getDeWeightTotal()
     *
     * @param null
     *
     * @author  Administrator
     * @version 1.1
     * @since   2022/3/22
     */
    public function getMemberTotal()
    {
        $member_total = $this->where('type', 2)
            ->field('id')
            ->count('id');

        return intval($member_total);
    }

    /**
     * @Purpose:
     * 获取客户群详情群成员数据总条数
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
        $total = $this->alias('gu')
            ->join('corpwechat_user_all ua', 'ua.userid = gu.userid', 'LEFT')
            ->field('gu.id')
            ->where($map)
            ->count('gu.id');
        return $total;
    }


    /**
     * @Purpose:
     * 获取客户群详情群成员分页
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
        // 7日登录
        $build_join_sql = model('MemberActionLog')
            ->where('is_login', 1)
            ->whereTime('addtime', 'week')
            ->field('uid, count(id) as day')
            ->group('uid')
            ->buildSql();
        $data = $this->alias('gu')
            ->join('corpwechat_user_all ua', 'ua.userid = gu.userid', 'LEFT')
            ->join('member_bind mb', 'mb.unionid = ua.unionid', 'LEFT')
            ->join('member m', 'm.uid = mb.uid', 'LEFT')
            // 7日登录
            ->join([$build_join_sql => 'l'], 'l.uid = mb.uid', 'LEFT')
            ->with('externalUser')
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

        $return_data['rows'] = $rows;
        $return_data['pages'] = array(
            'now_page' => $page_num,
            'total_page' => $total_page,
            'record_num' => $total);
        return $return_data;
    }

    /**
     * @Purpose:
     * 一对多关联【CorpwechatExternalUser】
     * @Method externalUser()
     */
    public function externalUser()
    {
        return $this->hasMany('CorpwechatExternalUser', 'external_user_id', 'userid')
            ->alias('eu')
            ->field('eu.external_user_id,ul.name as external_user')
            ->join('corpwechat_user_all ul', 'ul.userid = eu.userid', 'LEFT');
    }


    public function getGroupExternalTotal($chatId)
    {
        $group_user_total = $this->where('type', 2)
            ->where('chat_id', $chatId)
            ->field('id')
            ->count('id');

        return intval($group_user_total);
    }

}