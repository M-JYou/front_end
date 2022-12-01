<?php

/**
 * 表单模板字段 Model
 * @author chenyang
 * Date Time：2022年4月22日19:00:17
 */

namespace app\common\model;

class FormTemplateField extends BaseModel {
    protected $table = 'form_template_field';

    /**
     * 列表
     * @access public
     * @author chenyang
     * @param  array        $where [查询条件]
     * @param  array|string $field [查询字段]
     * @param  array|string $order [排序规则]
     * @return array
     * Date Time：2022年4月22日14:38:22
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
     * @return array
     * Date Time：2022年4月12日10:15:36
     */
    public function getInfo($where, $field = '*') {
        $info = db($this->table)->where($where)->field($field)->find();
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
     * 批量新增
     * @access public
     * @author chenyang
     * @param  array $data [新增数据]
     * @return integer
     * Date Time：2022年4月22日10:28:37
     */
    public function addAll($data = []) {
        if (empty($data)) {
            return 0;
        }
        return db($this->table)->insertAll($data);
    }

    /**
     * 删除
     * @access public
     * @author chenyang
     * @param  array $where [删除条件]
     * @return bool
     * Date Time：2022年4月22日11:50:11
     */
    public function del($where = []) {
        if (empty($where)) {
            return false;
        }
        return db($this->table)->where($where)->delete();
    }

    /**
     * 列表
     * @access public
     * @author chenyang
     * @param  array        $where [查询条件]
     * @param  array|string $field [查询字段]
     * @param  array|string $order [排序条件]
     * @return array
     * Date Time：2022年4月24日10:04:22
     */
    public function getJoinFieldList($where, $field = '*', $order = []) {
        $list = db($this->table)
            ->alias('a')
            ->join('field_setting b', 'b.field_id = a.field_id', 'LEFT')
            ->where($where)
            ->field($field)
            ->order($order)
            ->select();
        return $list;
    }
}
