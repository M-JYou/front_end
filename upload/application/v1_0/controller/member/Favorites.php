<?php

namespace app\v1_0\controller\member;

/** 收藏 */
class Favorites extends \app\v1_0\controller\common\Base {
  public function _initialize() {
    parent::_initialize();
    $this->checkLogin();
  }
  public function find() {
    $param = input('param.');
    $param['create'] = $this->userinfo->uid;
    $this->_find($param);
  }
  public function findModel() {
    $page = input('post.page/d', 1, 'intval');
    $pagesize = input('post.pagesize/d', 10, 'intval');
    $model = input('post.model/s', '');
    $m = model($this->dbname);

    $total = $m->where([
      'model' => $model,
      'create' => $this->userinfo->uid
    ])->count();
    ext([
      'items' => $m->where('a.model', $model)->where('a.create', $this->userinfo->uid)
        ->alias('a')
        ->join(
          config('database.prefix') . uncamelize($model) . ' b',
          'a.mid=b.id',
          'left'
        )
        ->field('a.addtime `createTime`,b.*')
        // ->fetchSql()
        ->select(),
      'total' => $total,
      'current_page' => $page,
      'pagesize' => $pagesize,
      'total_page' => ceil($total / $pagesize),
    ]);
  }
  public function add() {
    $param = input('param.');
    $param['create'] = $this->userinfo->uid;
    $c = 200;
    $m = "收藏成功";
    try {
      $d = model($this->dbname)->create($param);
    } catch (\Throwable $th) {
      $d = $th->getMessage();
      $c = 500;
      $m = "请勿重复收藏";
    }
    ext($c, $m, $d);
  }
  public function edit() {
    ext(500, '禁止修改');
  }
  public function delete() {
    $param = input('param.');
    $param['create'] = $this->userinfo->uid;
    $this->_find($param);
  }
}
