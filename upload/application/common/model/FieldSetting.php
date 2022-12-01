<?php

/**
 * 字段设置 Model
 * @author chenyang
 * Date Time：2022年4月21日18:26:20
 */

namespace app\common\model;

class FieldSetting extends BaseModel {
    protected $table = 'field_setting';

    /**
     * 获取分页列表
     * @access public
     * @author chenyang
     * @param  array        $where   [查询条件]
     * @param  array|string $field   [查询字段]
     * @param  integer      $perPage [每页显示数量]
     * @param  array|string $order   [排序条件]
     * @return array
     * Date Time：2022年4月22日13:23:48
     */
    public function getPageList($where, $field = '*', $perPage = 10, $order = []) {
        return db($this->table)->where($where)->field($field)->order($order)->paginate($perPage)->toArray();
    }

    /**
     * 列表
     * @access public
     * @author chenyang
     * @param  array        $where [查询条件]
     * @param  array|string $field [查询字段]
     * @param  array|string $order [排序条件]
     * @return array
     * Date Time：2022年5月5日11:25:47
     */
    public function getList($where = [], $field = '*', $order = []) {
        return db($this->table)->where($where)->field($field)->order($order)->select();
    }

    /**
     * 详情
     * @access public
     * @author chenyang
     * @param  array        $where [查询条件]
     * @param  array|string $field [查询字段]
     * @param  array|string $order [排序条件]
     * @return array
     * Date Time：2022年4月12日10:15:36
     */
    public function getInfo($where, $field = '*', $order = []) {
        $info = db($this->table)->where($where)->field($field)->order($order)->find();
        if (is_null($info) || empty($info)) {
            return [];
        }
        return $info;
    }

    /**
     * 新增
     * @access public
     * @author chenyang
     * @param  array $data [新增数据]
     * @return integer
     * Date Time：2022年4月21日18:27:21
     */
    public function add($data = []) {
        if (empty($data)) {
            return 0;
        }
        return db($this->table)->insertGetId($data);
    }

    /**
     * 修改
     * @access public
     * @author chenyang
     * @param  array $update [修改数据]
     * @param  array $where  [修改条件]
     * @return bool|integer
     * Date Time：2022年4月11日13:54:12
     */
    public function edit($where = [], $update = []) {
        if (empty($where)) {
            return false;
        }
        return db($this->table)->where($where)->update($update);
    }

    /**
     * 转换字段类型
     * @access public
     * @author chenyang
     * @param  integer $fieldType [字段类型]
     * @return array
     * Date Time：2022年5月5日11:38:23
     */
    public function switchFieldType($fieldType) {
        switch ($fieldType) {
            case 1:
                $name = '文本输入';
                break;
            case 2:
                $name = '选项单选';
                break;
            case 3:
                $name = '选项多选';
                break;
            case 4:
                $name = '选项下拉';
                break;
            case 5:
                $name = '多行文本';
                break;
            default:
                $name = '';
                break;
        }
        return $name;
    }

    /**
     * 列表
     * @access public
     * @author chenyang
     * @param  array        $where [查询条件]
     * @param  array|string $field [查询字段]
     * @param  array|string $order [排序条件]
     * @return array
     * Date Time：2022年5月7日16:23:08
     */
    public function getJoinValueList($where, $field = '*', $order = []) {
        $list = db($this->table)
            ->alias('a')
            ->join('field_value b', 'b.field_id = a.field_id', 'LEFT')
            ->where($where)
            ->field($field)
            ->order($order)
            ->select();
        return $list;
    }
}
