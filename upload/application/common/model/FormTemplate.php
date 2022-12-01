<?php

/**
 * 表单模板 Model
 * @author chenyang
 * Date Time：2022年4月22日19:00:54
 */

namespace app\common\model;

class FormTemplate extends BaseModel {
    protected $table = 'form_template';

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
}
