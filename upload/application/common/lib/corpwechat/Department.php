<?php

namespace app\common\lib\corpwechat;

use app\common\lib\corpwechat\promise\Corp;

class Department extends Corp {
  /**
   * 创建部门
   * @var string
   */
  const DEPARTMENT_CREATE = 'https://qyapi.weixin.qq.com/cgi-bin/department/create';
  const DEPARTMENT_UPDATE = 'https://qyapi.weixin.qq.com/cgi-bin/department/update';
  const DEPARTMENT_DELETE = 'https://qyapi.weixin.qq.com/cgi-bin/department/delete';
  /**
   * 获取部门列表
   * @var string
   */
  const DEPARTMENT_LIST = 'https://qyapi.weixin.qq.com/cgi-bin/department/list';


  /**
   * @Method Name 创建部门
   *
   * @param $department
   * @return mixed
   *
   * @link https://qyapi.weixin.qq.com/cgi-bin/department/create?access_token=ACCESS_TOKEN
   */
  public function DepartmentCreate($department) {
    return $this->callPost(self::DEPARTMENT_CREATE, $department);
  }

  public function DepartmentUpdate($department) {
    return $this->callPost(self::DEPARTMENT_UPDATE, $department);
  }

  public function DepartmentDelete($departmentId) {
    return $this->callGet(self::DEPARTMENT_DELETE, array('id' => $departmentId));
  }

  /**
   * @Method Name 获取部门列表
   *
   * @param null $departmentId 部门id。获取指定部门及其下的子部门（以及子部门的子部门等等，递归）。 如果不填，默认获取全量组织架构
   * @return mixed
   *
   * @link https://qyapi.weixin.qq.com/cgi-bin/department/list?access_token=ACCESS_TOKEN&id=ID
   */
  public function DepartmentList($departmentId = null) {
    $params = [];
    if ($departmentId)
      $params = array('id' => $departmentId);
    return $this->callGet(self::DEPARTMENT_LIST, $params);
  }
}
