<?php

namespace app\common\model\corpwechat;

use app\common\model\BaseModel;

class CorpwechatWelcomeWords extends BaseModel
{
    protected $pk = 'id';

    protected $readonly = [
        'id'
    ];

    protected $type = [
        'id' => 'integer',
        'type' => 'integer',
        'user_ids' => 'json'
    ];

    // 设置返回数据集的对象名
    protected $resultSetType = 'collection';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;

    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    public $errorMessage = '';

    /**
     * @Purpose:
     * 一对多关联【CorpwechatStaff】
     * @Method userList()
     */
    public function staffList()
    {
        return $this->hasMany('CorpwechatStaff', 'welcome_id', 'id')
            ->alias('s')
            ->field('s.userid, s.adminid, s.welcome_id, u.name')
            ->join('corpwechat_user_all u', 'u.userid = s.userid');
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
            ->with('staffList')
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
     * 获取欢迎语详情
     * @Method getDetails()
     *
     * @param $templateId
     * @param string $field
     *
     * @return array
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
     * @since   2022/3/10
     */
    public function getDetails($templateId, $field = '*')
    {
        $data = $this->field($field)
            ->with('staffList')
            ->find($templateId);
        if (isset($data) && !empty($data)) {
            return $data->toArray();
        } else {
            return array();
        }
    }

    /**
     * @Purpose:
     * 内链链接
     * @Method innerLinkInfo()
     *
     * @param string $link_type 内链类型[]
     * @param integer $inner_id 内链关联ID
     *
     * @return Jsonp
     *
     * @throws null
     *
     * @link {domain}corpwechat/welcome_words/innerLinkInfo
     *
     * @author  Mr.yx
     * @version 1.1
     * @since   2022/4/29 0029
     */
    public function innerLinkInfo($link_type, $inner_id)
    {
        switch ($link_type) {
            case 'reg_personal':
                // 求职者注册页
                $link_url = config('global_config.sitedomain') . '/member/reg/personal';
                break;

            case 'reg_company':
                // 企业注册页
                $link_url = config('global_config.sitedomain') . '/member/reg/company';
                break;

            case 'company':
                // 公司详情页
                if (!isset($inner_id) || empty($inner_id)) {
                    $this->errorMessage = '请先检索公司';
                    return false;
                }
                $link_url = config('global_config.sitedomain') . '/company/' . $inner_id;
                break;

            case 'job':
                // 职位详情页
                if (!isset($inner_id) || empty($inner_id)) {
                    $this->errorMessage = '请先检索职位';
                    return false;
                }
                $link_url = config('global_config.sitedomain') . '/job/' . $inner_id;
                break;

            case 'resume':
                // 简历详情页
                if (!isset($inner_id) || empty($inner_id)) {
                    $this->errorMessage = '请先检索简历';
                    return false;
                }
                $link_url = config('global_config.sitedomain') . '/resume/' . $inner_id;
                break;

            case 'notice':
                // 公告详情页
                if (!isset($inner_id) || empty($inner_id)) {
                    $this->errorMessage = '请先检索公告';
                    return false;
                }
                $link_url = config('global_config.sitedomain') . '/notice/' . $inner_id;
                break;

            case 'jobfair':
                // 招聘会详情页
                if (!isset($inner_id) || empty($inner_id)) {
                    $this->errorMessage = '请先检索招聘会';
                    return false;
                }
                $link_url = config('global_config.sitedomain') . '/jobfair/' . $inner_id;
                break;

            case 'jobfairol':
                // 网络招聘会详情页
                if (!isset($inner_id) || empty($inner_id)) {
                    $this->errorMessage = '请先检索网络招聘会';
                    return false;
                }
                $link_url = config('global_config.sitedomain') . '/jobfairol/' . $inner_id;
                break;

            case 'news':
                // 资讯详情页
                if (!isset($inner_id) || empty($inner_id)) {
                    $this->errorMessage = '请先检索资讯';
                    return false;
                }
                $link_url = config('global_config.sitedomain') . '/article/' . $inner_id;
                break;

            case 'index':
                // 首页
                $link_url = config('global_config.sitedomain');
                break;

            default:
                $this->errorMessage = '请选择正确的内链类型';
                return false;
                break;
        }

        return $link_url;
    }


    public function getErrorMessage()
    {
        return $this->errorMessage;
    }
}