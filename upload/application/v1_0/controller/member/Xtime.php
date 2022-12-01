<?php

namespace app\v1_0\controller\member;

/** 虾时光 */
class Xtime extends \app\v1_0\controller\common\Base {
  public function _initialize() {
    parent::_initialize();
  }
  protected function cud(&$p = null) {
    $this->checkLogin();
  }
  public function add() {
    $data = [
      'create' => $this->userinfo->uid,
      'is_display' => input('post.is_display/d', 1, 'intval'),
      'xtime' => input('post.xtime/d', 0, 'intval'),
      'name' => input('post.name/s', '', 'trim'),
      'content' => input('post.content/s', '', 'trim'),
      'other' => input('post.other/a', []),
    ];
    // ext($data);
    $this->_add($data);
  }
  public function find() {
    $uid = $this->userinfo->uid ? $this->userinfo->uid : 0;
    $param = input('param.');
    if (input('?param.id')) {
      $this->___find($param);
    } else {
      $order = 'sort_id desc,id desc';
      $where = $this->conditionParam($param, $order);
      $mm = model('Xtime');
      $field = '*,`create` `user`,' . $uid . ' `visitor`';
      if (isset($where['info5'])) {
        $field = $mm->getField2($param, $field, 'id', 'info5');
      }
      $where = $mm->toWhere($where);

      $page = input('post.page/d', 1, 'intval');
      $pagesize = input('post.pagesize/d', 10, 'intval');

      $total = $mm->where($where)->count();
      ext(200, '获取数据成功', [
        'items' => $mm->where($where)
          ->field($field)
          ->orderRaw($order)
          ->page($page . ',' . $pagesize)
          ->select(),
        'total' => $total,
        'current_page' => $page,
        'pagesize' => $pagesize,
        'total_page' => ceil($total / $pagesize),
      ]);
    }
  }

  public function edit() {
    $data = [
      'id' => input('post.id/d', 0, 'intval'),
      'is_display' => input('post.is_display/d', null, 'intval'),
      'name' => input('post.name/s', null, 'trim'),
      'content' => input('post.content/s', null, 'trim'),
      'other' => input('post.other/a', null),
    ];
    $this->_edit($data, [
      'id' => input('post.id/d', 0, 'intval'),
      'create' => $this->userinfo->uid
    ]);
  }
  public function delete() {
    $this->_delete(['id' => input('post.id'), 'create' => $this->userinfo->uid]);
  }
  public function visitor() {
    $this->checkLogin();
    $mn = 'Xtime';
    $mid = model($mn)->where('create', $this->userinfo->uid)->column('id');
    $ret = model('Visitor')
      ->alias('a')
      ->join(
        config('database.prefix') . 'member_info b',
        'a.create=b.id',
        'left'
      )
      ->where(['model' => $mn, 'mid' => ['in', $mid], 'create' => ['<>', $this->userinfo->uid]])
      ->field(
        'b.id,b.name,b.photo,a.addtime'
          . ',(SELECT COUNT(*) FROM `qs_xtime` WHERE `create`=`b`.`id`) `send`'
          . ',(SELECT COUNT(*) FROM `qs_likes` WHERE `create`=`b`.`id`) `like`'
      )
      // ->fetchSql()
      ->group('b.id')
      ->orderRaw('a.addtime desc')
      ->limit(10)
      ->select();
    ext(200, '获取数据成功', $ret);
  }
  public function tst() {
    ext(model('Xtime')
      ->field('id,1 visitor')
      ->find());
  }
}
