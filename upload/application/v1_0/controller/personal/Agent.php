<?php

/** 关注的企业列表 */

namespace app\v1_0\controller\personal;

class Agent extends \app\v1_0\controller\common\Base {
  public function _initialize() {
    parent::_initialize();
    // $this->checkLogin(2);
    // $this->interceptPersonalResume();
  }
  public function create() {
    $type = input('post.type/d', 0);
    if ($type >= 1 && $type <= 10) {

      $this->checkLogin(($type % 2) ? 1 : 2); // 校验登录 单数为企业,双数为个人
      $category_district = model('CategoryDistrict')->get(input('post.category_district/d', 0, 'intval'));
      if ($category_district) {
        $d = [
          'addtime' => time(), // 创建时间
          'create' => $this->userinfo->uid, // 创建人
          'type' => $type, // 类型
          'name' => input('post.name/s', null, ''),

          'category_district' => $category_district['id'], // 工作地点
          'diploma' => input('post.diploma/d'), // '文凭', -- 1:小学; 2:中学; 3:职高; 4:大专; 5:大学; 6:硕士; 7:研究生; 8:博士;
          'tel' => input('post.tel/s'), // '联系电话',
          'content' => input('post.content/s', ''), // '需求描述',
          'state' => 1,
        ];
        if ($type % 2) { // 企业 发布信息为需求人才
          $d['gender'] = input('post.gender/d');
          $d['resumetag'] = input('post.resumetag/d');
          $d['language'] = input('post.language/d');
          $d['language_level'] = input('post.language_level/d');
          $d['current'] = input('post.current/d');
        } else { // 个人 发布信息为需求工作
          $d['trade'] = input('post.trade/d');
          $d['company_type'] = input('post.company_type/d');
          $d['scale'] = input('post.scale/d');
          $d['jobtag'] = input('post.jobtag/d');
        }
        $m = model('Agent');
        $result = $m->validate(true)->allowField(true)->save($d);
        return $this->ajaxReturn($result ? 200 : 500, $result ? '保存成功' : $m->getError(), $result ? $result : null);
      }
      return $this->ajaxReturn(500, '地址id不存在');
    }
    return $this->ajaxReturn(500, '类型错误');
  }
  public function update() {
    $this->checkLogin();
    $m = model('Agent');
    $d = $m->where(['id' => input('post.id/d'), 'create' => $this->userinfo->uid, 'status' => 0])
      ->where('status', '<=', 5)->find();
    if ($d) { // 满足修改条件
      # code...
    }
    return $this->ajaxReturn(500, '当前状态不可修改');
  }
  public function tst($type = '') {
    return json_encode(['type' => isset($type) ? $type : '啥也没有', input('get.')]);
  }
}
