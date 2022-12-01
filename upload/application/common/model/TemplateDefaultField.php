<?php

/**
 * 模板默认字段 Model
 * @author chenyang
 * Date Time：2022年4月29日12:05:41
 */

namespace app\common\model;

class TemplateDefaultField extends BaseModel {
    protected $table = 'template_default_field';

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
