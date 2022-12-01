<?php

/** 消息 */

namespace app\v1_0\controller\member;

class Chat extends \app\v1_0\controller\common\Base {
  public function _initialize() {
    parent::_initialize();
    $this->checkLogin();
  }
  protected $type = [
    'text',
    'img',
    'file',
    'video',
    'audio',
    'link',
  ];
  public function find() {
    ext(500, '没有此方法');
  }
  public function add(&$p = null) {
    $this->find();
  }
  public function edit(&$p = null) {
    $this->find();
  }
  public function delete(&$p = null) {
    $this->find();
  }
  public function type() {
    ext(500, 'type为数组成员文本', $this->type);
  }
  // 发送消息
  public function send() {
    $param = [
      'create' => $this->userinfo->uid,
      'target' => input('post.target/d', null, 'intval'),
      'type' => input('post.type/s', '', 'trim'),
      'content' => input('post.content/s', '', 'trim')
    ];
    if ($param['target'] && $param['type'] && $param['content']) {
      $this->_add($param);
    }
    ext(500, '数据格式错误');
  }
  // 获取未读消息会话列表
  public function listUnread($json = 0) {
    $ret = model($this->dbname)
      ->alias('a')
      ->join(
        config('database.prefix') . 'member_info b',
        'a.create=b.id',
        'left'
      )
      ->where(['a.target' => $this->userinfo->uid, 'a.new' => 1])
      ->group('a.create')
      ->order('a.addtime desc')
      ->field('b.id,b.nickname,b.photo,b.online,(SELECT count(*) FROM `'
        . config('database.prefix')
        . 'chat` c WHERE c.`create`=a.`create` AND c.`target`=\'' . $this->userinfo->uid
        . '\' AND c.`new`=1) sum')
      // ->fetchSql()
      ->select();
    if ($json) {
      return $json;
    }
    ext(200, '获取列表成功', $ret);
  }
  // 获取所有消息会话列表
  public function listAll($json = 0) {
    $d = model($this->dbname)
      ->whereOr('target', $this->userinfo->uid)
      ->whereOr('create', $this->userinfo->uid)
      ->order('addtime desc')
      ->field('create,target')
      // ->fetchSql()
      ->select();
    // ext($d);

    $ids = [$this->userinfo->uid];
    foreach ($d as $v) {
      if (!in_array($v['create'], $ids)) {
        $ids[] = $v['create'];
        // array_unshift($ids, $v['create']);
      } elseif (!in_array($v['target'], $ids)) {
        $ids[] = $v['target'];
      }
    }
    array_splice($ids, 0, 1);
    $d = model('MemberInfo')->alias('a')
      ->where('id', 'in', $ids)
      ->field('a.id,a.nickname,a.photo,a.online,(SELECT count(*) FROM `' . config('database.prefix')
        . 'chat` b WHERE b.`create`=a.id AND b.`target`=\'' . $this->userinfo->uid
        . '\' AND b.`new`=1) sum')
      ->select();
    $ret = [];
    foreach ($ids as $v) {
      $t = findArr($d, $v);
      if ($t) {
        $ret[] = $t;
      }
    }
    if ($json) {
      return $json;
    }
    ext(200, '获取列表成功', $ret);
  }
  // 获取消息列表
  public function chats() {
    $w = [
      'target&create' => ['in', [input('param.target/d', 0, 'intval'), $this->userinfo->uid]],
      'addtime' => ['>', input('post.addtime/d', 0, 'intval')],
    ];
    $m = model($this->dbname);
    $page = input('post.page/d', 1, 'intval');
    $pagesize = input('post.pagesize/d', 10, 'intval');
    $total = $m->where($w)->count();
    $items = $m->where($w)->order('addtime desc')
      ->page("$page,$pagesize")->select();
    $ids = [];
    foreach ($items as $v) {
      if ($v['new'] && $v['target'] == $this->userinfo->uid) {
        $ids[] = $v['id'];
      }
    }
    $m->where('id', 'in', $ids)->update(['new' => 0]);
    ext(200, '获取消息成功', [
      'items' => $items,
      'total' => $total,
      'current_page' => $page,
      'pagesize' => $pagesize,
      'total_page' => ceil($total / $pagesize),
    ]);
  }
  // 获取未读消息数
  public function countUnread() {
    ext(200, '获取数据成功', model($this->dbname)->where([
      'target' => $this->userinfo->uid,
      'new' => 1
    ])->count());
  }
}
