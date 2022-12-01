<?php

namespace app\v1_0\controller\member;

use think\Db;

/** 用户详细信息 */
class MemberInfo extends \app\v1_0\controller\common\Base {

  public function _initialize() {
    parent::_initialize();
  }
  protected function cud(&$p = null) {
    $this->checkLogin();
  }
  public function add() {
    $this->edit();
  }
  public function index2() {
    // $m = new \app\common\model\MemberInfo;
    $m = model('MemberInfo');
    $d = $m->fetch();
    die(json_encode($d, JSON_UNESCAPED_UNICODE));
  }
  public function find() {
    $data = input('param.');
    $this->_find($data, 'id,online,nickname,photo,id expert');
  }
  public function edit() {
    $d = input('post.');
    $id = $this->userinfo->uid;
    $d['id'] = $id;
    $f = ['auth_face', 'auth_card', 'auth_weixin'];
    foreach ($f as $v) {
      if (isset($d[$v])) {
        unset($d[$v]);
      }
    }

    $this->_edit($d);
  }
  public function delete() {
    $this->ajaxReturn(500, '不能删除');
  }
  public function me() {
    $this->checkLogin();

    $pre = config('database.prefix');
    $tj = encode(['create' => $this->userinfo->uid, 'is_display' => ['>=', 0]]);
    $tj2 = encode(['create' => $this->userinfo->uid, '#method' => 'count']);
    $d = model($this->dbname)->alias('a')->where('a.id', $this->userinfo->uid)
      ->join($pre . 'member b', 'a.id=b.uid', 'left')
      ->join($pre . 'member_points c', 'a.id=c.uid', 'left')
      ->field(
        'a.*'
          . ',b.username,b.email,b.account,b.utype,b.mobile'
          . ',c.points'
          . ",'$tj' shop,'$tj' expert"
          . ",'$tj2' `favorites`, '$tj2' `likes`"
      )
      // ->fetchSql()
      ->find();
    if (!$d) {
      $md = model('Member')->where('uid', $this->userinfo->uid)->find();
      $d = [
        '#' => 1,
        'id' => $this->userinfo->uid,
        'auth_face' => 0,
        'auth_card' => 0,
        'auth_weixin' => 0,
        'online' => 0,
        'name' => $this->userinfo->uid,
        'card' => '',
        'photp' => 'https://www.zixia6.com/upload/resource/empty_photo.png',
        'addr' => [],
        'nickname' => $this->userinfo->uid,
        'weixin' => '',
        'background' => 'https://www.zixia6.com/assets/images/xtime.png',
        'autograph' => '',
        'username' => $md['username'],
        'email' => $md['email'],
        'account' => $md['account'],
        'utype' => $md['utype'],
        'points' => 0,
        'shop' => null,
        'expert' => null,
        'favorites' => 0,
        'likes' => 0,
      ];
      model($this->dbname)->create($d);
    }

    ext($d);
  }
  public function m() {
    $d=model('Uploadfile')->getFileUrl(312);
    outp($d);
  }
  public function give() {
    $this->checkLogin();
    $code = 200;
    $e = '转增' . config('global_config.points_byname') . '成功';
    try {
      $points = input('post.points/d', 0, 'intval');
      if ($points > 0) {
        $mn = model($this->dbname)->field('name')->find($this->userinfo->uid)['name'];
        $tid = input('post.target/d', 0, 'intval');
        $tn = model($this->dbname)->field('name')->find($tid)['name'];
        model('MemberPoints')->setPoints([
          ['uid' => $this->userinfo->uid, 'points' => $points * -1, 'note' => "赠与【 $tn 】$points" . config('global_config.points_byname')],
          ['uid' => $tid, 'points' => $points, 'note' => "收到【 $mn 】赠与的$points" . config('global_config.points_byname')],
        ]);
      } else {
        $code = 500;
        $e = '转增' . config('global_config.points_byname') . '数量必须大于0';
      }
    } catch (\Throwable $th) {
      $code = $th->getCode();
      if ($code < 500) {
        $code = 500;
        $e = '参数非法';
      } else {
        $e = $th->getMessage();
      }
    }
    ext($code, $e, null);
  }
  public function change() {
    $type = input('post/type/d', 0, 'intval');

    if (($type == 1 || $type == 2) && model('Member')->update(['utype' => $type], ['uid' => $this->userinfo->uid])) {
      $this->userinfo->utype = $type;
      ext(200, '修改类型成功,为避免意外,请重新登录');
    } else {
      ext(500, '数据错误,type只能为1或2');
    }
  }
}
