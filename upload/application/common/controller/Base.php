<?php

namespace app\common\controller;

use app\common\base\Admininfo;
use app\common\base\Userinfo;
use think\Config;
use think\Cookie;

class Base extends \think\Controller {
  /** @var Admininfo */
  protected $admininfo;
  /** @var Userinfo */
  protected $userinfo;
  protected $subsite = null;
  protected $subsiteCondition = [];
  protected $request;
  protected $module_name;
  protected $controller_name;
  protected $action_name;
  protected $expire_platform = [
    'app' => 604800, //7天有效期
    'mobile' => 604800, //7天有效期
    'wechat' => 604800, //7天有效期
    'miniprogram' => 604800, //7天有效期
    'web' => 3600 //60分钟有效期
  ];
  protected $dbname = '';
  protected $class_name = '';
  protected $pre = '';
  protected $diploma = [
    ['id' => 1, 'name' => '小学'],
    ['id' => 2, 'name' => '中学'],
    ['id' => 3, 'name' => '职高'],
    ['id' => 4, 'name' => '大专'],
    ['id' => 5, 'name' => '大学'],
    ['id' => 6, 'name' => '硕士'],
    ['id' => 7, 'name' => '研究生'],
    ['id' => 8, 'name' => '博士'],
  ];
  protected $resumetag = [];
  protected $language = [];
  protected $current = [];
  protected $language_level = [];
  protected $trade = [];
  protected $company_type = [];
  protected $scale = [];
  protected $jobtag = [];


  public function _initialize() {
    parent::_initialize();
    $this->admininfo = new Admininfo();
    $this->userinfo = new Userinfo();
    $this->request = \think\Request::instance();
    $this->module_name = strtolower($this->request->module());
    $this->controller_name = strtolower($this->request->controller());
    $this->action_name = strtolower($this->request->action());
    $this->filterIp();
    $this->initSubsite();
    $t = get_class($this);
    $this->dbname = substr($t, strrpos($t, '\\') + 1);
    $this->class_name = IsDebug ? $t : $this->dbname;
    IsDebug && $this->pre = '通用接口【' . $this->class_name . '】';

    $d = model('Category')->field('id,name,alias')->order('id asc')->select();
    $t = ['resumetag', 'language', 'current', 'language_level', 'trade', 'company_type', 'scale', 'jobtag'];
    foreach ($d as $v) {
      $n = mb_substr($v['alias'], 3);
      if (in_array($n, $t) && is_array($this->$n)) {
        unset($v['alias']);
        $this->$n[] = $v;
      }
    }
  }

  public function attr() {
    $attr = input('param.attr/s', '', 'trim');
    if (is_array($this->$attr)) {
      ext(200, '获取属性成功:' . $attr, $this->$attr);
    }
    ext(500, '获取属性失败:' . $attr, null);
  }

  /** 初始化分站信息 */
  protected function initSubsite() {
    if (intval(config('global_config.subsite_open')) == 0) {
      return;
    }
    $subsiteid = 0;
    do {
      $subsiteid = Cookie::has('subsiteid') ? Cookie::get('subsiteid') : 0;
      if ($subsiteid) {
        break;
      }
      $header_info = \think\Request::instance()->header();
      $subsiteid = isset($header_info['subsiteid']) ? $header_info['subsiteid'] : 0;
      if ($subsiteid) {
        break;
      }
      $subsiteid = input('param.subsiteid/d', 0, 'intval');
    } while (0);
    if ($subsiteid > 0) {
      $this->subsite = model('Subsite')->where('id', $subsiteid)->find();
      if ($this->subsite === null) {
        return;
      }
      if ($this->subsite->district3 > 0) {
        $this->subsiteCondition = ['district3' => $this->subsite->district3];
      } else if ($this->subsite->district2 > 0) {
        $this->subsiteCondition = ['district2' => $this->subsite->district2];
      } else {
        $this->subsiteCondition = ['district1' => $this->subsite->district1];
      }
      $category_district_data = model('CategoryDistrict')->getCache();
      $this->subsite->district_text = isset($category_district_data[$this->subsite->district]) ? $category_district_data[$this->subsite->district] : '';
      $this->subsite->district_text = cut_str($this->subsite->district_text, 5);
      $this->subsite->district_level = $this->subsite->district3 > 0 ? 3 : ($this->subsite->district2 > 0 ? 2 : 1);
    }
    \think\Config::set('subsite', $this->subsite);
    \think\Config::set('subsiteid', $this->subsite === null ? 0 : $this->subsite->id);
    \think\Config::set('subsiteCondition', $this->subsiteCondition);
    if ($this->subsite !== null) {
      \think\Config::set('global_config.sitename', $this->subsite->sitename);
    }
  }
  public function filterIp() {
    if (!in_array($this->module_name, ['apiadmin', 'apiadminmobile'])) {
      $config = config('global_config');
      //dump($config);die;
      if (isset($config['filter_ip']) && $config['filter_ip'] != '') {
        $iparr = explode('|', $config['filter_ip']);
        $ip = get_client_ip();
        if (in_array($ip, $iparr)) {
          if (in_array($this->module_name, ['v1_0'])) {
            ext(60001, '您的IP已经被禁止访问，请联系网站管理员');
          } else {
            echo $this->fetch('common@deny/ipfilter');
            exit;
          }
        }
      }
    }
  }
  protected function ajaxReturn($code = 200, $message = '', $data = []) {
    $return = [
      'code' => $code,
      'message' => $message,
      'data' => $data
    ];
    exit(json_encode($return, JSON_UNESCAPED_UNICODE));
  }
  protected function auth($request_token) {
    $token = \app\common\lib\JwtAuth::getToken($request_token);
    if ($token->isExpired()) {
      return ['code' => 50002, 'info' => 'token失效'];
    }
    if (!$token->verify(config('sys.safecode'))) {
      return ['code' => 50001, 'info' => '非法token'];
    }
    return ['code' => 200, 'info' => $token->getData('info')];
  }
  protected function instCreate(array &$param, $uid = 0) {
    $param['create'] = $uid ? $uid : $this->userinfo->uid;
    return $param;
  }
  protected function cud(array &$param = []) {
    cache('cache_model_tree_' . $this->dbname);
  }
  protected function getData() {
    return input('param.');
  }
  public function add() {
    $this->_add($this->getData('add'));
  }
  protected function _add(array $param, $model = null) {
    $this->cud($param);
    if (!$model) {
      $model = model($this->dbname);
    }
    try {
      $d = $model->c($param);
      $e = $model->getError();
      $code = $e ? 500 : 200;
    } catch (\Throwable $th) {
      $code = $th->getCode();
      $d = $code == 200 ? $model->where($model->toWhere($param))->find() : $param;
      $e = $th->getMessage();
    }
    !$e && $e = '增加数据成功';
    ext($code, $this->pre . $e, $d);
  }
  public function edit() {
    if (isGet()) {
      $this->find();
    } else {
      $this->_edit($this->getData('edit'));
    }
  }
  protected function _edit(array $param, $where = null, $model = null) {
    $this->cud($param);
    if (!$model) {
      $model = model($this->dbname);
    }
    if (!$where && isset($param['create'])) {
      $where = ['id' => $param['id'], 'create' => $param['create']];
    }
    try {
      $d = $model->u($param, $where);
      $e = $model->getError();
      $code = $e ? 500 : 200;
    } catch (\Throwable $th) {
      $code = $th->getCode();
      $d = $code == 200 ? $model->where($model->toWhere($param))->find() : $param;
      $e = $th->getMessage();
    }
    !$e && $e = '修改数据成功';
    ext($code, $this->pre . $e, $d);
  }
  public function save() {
    $this->_save(input('?post.id'));
  }
  protected function _save($logic) {
    if ($logic) {
      $this->edit();
    } else {
      $this->add();
    }
  }
  public function index() {
    $this->find();
  }
  public function find() {
    $this->_find($this->getData('find'));
  }
  /** 查找数据库
   *
   * @param array $param 查询条件
   * @param string $order 排序规则
   * @param Closure $foreach 每一条数据都要循环的回调方法
   * @param array|string $colum 使用tp的对象形式返回规则
   * @param string $field 获取数据表字段
   * @param \think\Model $model 传递model
   * @return void
   */
  protected function _find(array $param, $field = '*', $order = null, $colum = null, $model = null) {
    if (!$model) {
      $model = model($this->dbname);
    }
    if (!$field) {
      $field = '*';
    }
    try {
      if (isset($param['id']) && $param['id'] && !is_array($param['id'])) {
        $param['#history'] = $this->userinfo->uid;
      } else {
        if (isset($param['history'])) {
          $field .= ',id `history`';
        }
      }
      $d = $model->r($param, $field, $order, $colum);
      $e = $model->getError();
      $code = $e ? 500 : 200;
    } catch (\Throwable $th) {
      $code = $th->getCode();
      $d = $param;
      $e = $th->getMessage();
    }
    !$e && $e = '获取数据成功';
    ext($code, $this->pre . $e, $d);
  }
  /** 查找数据库2
   *
   * @param array $param 查询条件
   * @param \think\Model $model 传递model
   * @param string $order 排序规则
   * @param string $field 获取数据表字段
   * @return void
   */
  protected function __find(array $param, $model = null, $order = null, $field = '*') {
    $this->_find($param, $field, $order, null, $model);
  }
  /** 查找数据3 */
  protected function ___find(array $param, $model = null) {
    $m = $model ? $model : model($this->dbname);
    $hmd = [];
    $bmd = [];
    $bid = [];
    $hid = [];
    $order = 'sort_id desc,id desc';
    if ($this->userinfo) {
      $hmd = model('Blacklist')->where('mid', $this->userinfo->uid)->column('create');
      $bmd = model('Favorites')->where([
        'create' => $this->userinfo->uid,
        'model' => 'MemberInfo'
      ])->column('mid');
      $l = count($bmd) - 1;
      while ($l >= 0) {
        if (in_array($bmd[$l], $hmd)) {
          array_splice($bmd, $l, 1);
          continue;
        }
        $l--;
      }
    }
    $t = $m->getColumn();
    $haveDT = in_array('display_type', $t);
    $notMe = !$this->admininfo && !($this->userinfo && $this->userinfo->uid == $param['create']);
    if ($haveDT && $notMe) {
      $param['display_type'] = ['>', 0];
    }
    if (in_array('is_display', $t) && $notMe) {
      $param['is_display'] = 1;
    }
    if ($bmd) {
      $t = $m->where($m->toWhere($param))
        ->where('create', 'in', $bmd);
      if ($hmd) {
        $t = $t->where('create', 'not in', $hmd);
      }
      $bid = $t->select();
    }
    if ($hmd) {
      $hid = $m->where('create', 'in', $hmd)->column('id');
    }
    if ($haveDT) {
      $tid = [];
      $t = $m->field('id,create')
        ->where('display_type', 1)
        // ->group('create')
        ->select();
      $tm = model('Favorites');
      foreach ($t as $v) {
        if (in_array($v['create'], $hmd)) {
          $hid[] = $v['id'];
        } elseif (!in_array($v['create'], $tid)) {
          $tid[] = $v['create'];
          if ($tm->where([
            'create' => $v['create'],
            'mid' => $this->userinfo->uid,
            'model' => 'MemberInfo'
          ])->find() == null) {
            $hmd[] = $v['create'];
            $hid[] = $v['id'];
          }
        }
      }
    }
    if (count($bid) > 0) {
      $order = 'FIELD(id,' . join(',', $bid) . ') DESC,' . $order;
    }
    if ($hid && !isset($param['id'])) {
      $param['id'] = ['not in', $hid];
    }
    $field = '*,`create` as `user`';
    $this->_find($param, $field, $order);
  }
  protected function conditionParam(array $param, string $order, $model = null) {
    $m = $model ? $model : model($this->dbname);
    $hmd = [];
    $bmd = [];
    $bid = [];
    $hid = [];
    // $order = 'sort_id desc,id desc';
    if ($this->userinfo) {
      $hmd = model('Blacklist')->where('mid', $this->userinfo->uid)->column('create');
      $bmd = model('Favorites')->where([
        'create' => $this->userinfo->uid,
        'model' => 'MemberInfo'
      ])->column('mid');
      $l = count($bmd) - 1;
      while ($l >= 0) {
        if (in_array($bmd[$l], $hmd)) {
          array_splice($bmd, $l, 1);
          continue;
        }
        $l--;
      }
    }
    $t = $m->getColumn();
    $haveDT = in_array('display_type', $t);
    $notMe = !$this->admininfo && !($this->userinfo && $this->userinfo->uid == $param['create']);
    if ($haveDT && $notMe) {
      $param['display_type'] = ['>', 0];
    }
    if (in_array('is_display', $t) && $notMe) {
      $param['is_display'] = 1;
    }
    if ($bmd) {
      $t = $m->where($m->toWhere($param))
        ->where('create', 'in', $bmd);
      if ($hmd) {
        $t = $t->where('create', 'not in', $hmd);
      }
      $bid = $t->select();
    }
    if ($hmd) {
      $hid = $m->where('create', 'in', $hmd)->column('id');
    }
    if ($haveDT) {
      $tid = [];
      $t = $m->field('id,create')
        ->where('display_type', 1)
        // ->group('create')
        ->select();
      $tm = model('Favorites');
      foreach ($t as $v) {
        if (in_array($v['create'], $hmd)) {
          $hid[] = $v['id'];
        } elseif (!in_array($v['create'], $tid)) {
          $tid[] = $v['create'];
          if ($tm->where([
            'create' => $v['create'],
            'mid' => $this->userinfo->uid,
            'model' => 'MemberInfo'
          ])->find() == null) {
            $hmd[] = $v['create'];
            $hid[] = $v['id'];
          }
        }
      }
    }
    if (count($bid) > 0) {
      $order = 'FIELD(id,' . join(',', $bid) . ') DESC,' . $order;
    }
    if ($hid && !isset($param['id'])) {
      $param['id'] = ['not in', $hid];
    }
    return $param;
  }

  public function delete() {
    $this->_delete($this->getData('delete'));
  }
  protected function _delete(array $param, $model = null) {
    $this->cud($param);
    if (!$model) {
      $model = model($this->dbname);
    }
    try {
      $d = $model->d($param);
      $e = $model->getError();
      $code = $e ? 500 : 200;
    } catch (\Throwable $th) {
      $code = $th->getCode();
      $d = $param;
      $e = $th->getMessage();
    }
    !$e && $e = '删除数据成功';
    ext($code, $this->pre . $e, $d);
  }
  /** 全局获取tree */
  public function tree() {
    $this->_tree();
  }
  public function _tree($model = null) {
    if (!$model) {
      $model = model($this->dbname);
    }
    ext(200, '获取数据成功', $model->tree());
  }

  /** 转发 */
  public function reprint() {
    $code = 500;
    $message = '转发';
    $ret = null;
    $model = model('Reprint');
    if (isGet()) {
      $message = '获取' . $message . ' 成功';
      $code = 200;
      $data = input('param.');
      $data['create'] = $this->userinfo->uid;
      $data = $model->checkField($data);
      if (input('param.count')) {
        $ret = $model->where($data)->count();
      } else {
        $data['pagesize'] = input('param.pagesize/d', 0, 'intval');
        $data['page'] = input('param.page/d', 0, 'intval');
        $ret = $model->r($data, '*,`create` as user', 'addtime DESC');
      }
    } else {
      $message = '提交' . $message;
      if ($this->userinfo->uid) {
        if (input('?param.id')) {
          $data = clearArrNullRet([
            'create' => $this->userinfo->uid,
            'model' => $this->dbname,
            'mid' => input('param.id/d', null, 'intval'),
          ]);
          try {
            $ret = $model->create($data);
          } catch (\Throwable $th) {
            $ret = $model->where($data)->find();
          }
          $code = 200;
          $message .= '成功';
        } else {
          $message .= '缺少参数id';
        }
      } else {
        $message .= '需要登录';
      }
    }
    ext($code, $message, $ret);
  }
  /** 收藏 */
  public function favorites() {
    $code = 500;
    $message = '收藏';
    $ret = null;
    $model = model('Favorites');
    if (isGet()) {
      $message = '获取' . $message . ' 成功';
      $code = 200;
      $data = input('param.');
      $data['create'] = $this->userinfo->uid;
      $data = $model->checkField($data);
      if (input('param.count')) {
        $ret = $model->where($data)->count();
      } else {
        $data['pagesize'] = input('param.pagesize/d', 0, 'intval');
        $data['page'] = input('param.page/d', 0, 'intval');
        $ret = $model->r($data, '*,`create` as user', 'addtime DESC');
      }
    } else {
      $message = '提交' . $message;
      if ($this->userinfo->uid) {
        if (input('?param.id')) {
          $data = clearArrNullRet([
            'create' => $this->userinfo->uid,
            'model' => $this->dbname,
            'mid' => input('param.id/d', null, 'intval'),
          ]);
          try {
            $ret = $model->create($data);
          } catch (\Throwable $th) {
            $ret = $model->where($data)->find();
          }
          $code = 200;
          $message .= '成功';
        } else {
          $message .= '缺少参数id';
        }
      } else {
        $message .= '需要登录';
      }
    }
    ext($code, $message, $ret);
  }
  /** 点赞 */
  public function likes() {
    $code = 500;
    $message = '点赞';
    $ret = null;
    $model = model('Likes');
    if (isGet()) {
      $message = '获取' . $message . ' 成功';
      $code = 200;
      $data = input('param.');
      $data['create'] = $this->userinfo->uid;
      $data = $model->checkField($data);
      if (input('param.count')) {
        $ret = $model->where($data)->count();
      } else {
        $data['pagesize'] = input('param.pagesize/d', 0, 'intval');
        $data['page'] = input('param.page/d', 0, 'intval');
        $ret = $model->r($data, '*,`create` as user', 'addtime DESC');
      }
    } else {
      $message = '提交' . $message;
      if ($this->userinfo->uid) {
        if (input('?param.id')) {
          $data = clearArrNullRet([
            'create' => $this->userinfo->uid,
            'model' => $this->dbname,
            'mid' => input('param.id/d', null, 'intval'),
          ]);
          try {
            $ret = $model->create($data);
          } catch (\Throwable $th) {
            $ret = $model->where($data)->find();
          }
          $code = 200;
          $message .= '成功';
        } else {
          $message .= '缺少参数id';
        }
      } else {
        $message .= '需要登录';
      }
    }
    ext($code, $message, $ret);
  }
  /** 评论 */
  public function comment() {
    $code = 500;
    $message = '评论';
    $ret = null;
    $model = model('Comment');
    if (isGet()) {
      $message = '获取' . $message . ' 成功';
      $code = 200;
      $data = input('param.');
      $data['create'] = $this->userinfo->uid;
      $data = $model->checkField($data);
      if (input('param.count')) {
        $ret = $model->where($data)->count();
      } else {
        $data['pagesize'] = input('param.pagesize/d', 0, 'intval');
        $data['page'] = input('param.page/d', 0, 'intval');
        $ret = $model->r($data, '*,`create` as user', 'addtime DESC');
      }
    } else {
      $message = '提交' . $message;
      if ($this->userinfo->uid) {
        if (input('?param.id')) {
          $data = clearArrNullRet([
            'create' => $this->userinfo->uid,
            'model' => $this->dbname,
            'mid' => input('param.id/d', null, 'intval'),
            'content' => input('param.content/s', '', 'trim'),
          ]);
          try {
            $ret = $model->create($data);
          } catch (\Throwable $th) {
            $ret = $model->where($data)->find();
          }
          $code = 200;
          $message .= '成功';
        } else {
          $message .= '缺少参数id';
        }
      } else {
        $message .= '需要登录';
      }
    }
    ext($code, $message, $ret);
  }
  /** 举报 */
  public function report() {
    $code = 500;
    $message = '举报';
    $ret = null;
    $model = model('Report');
    if (isGet()) {
      $message = '获取' . $message . ' 成功';
      $code = 200;
      $data = input('param.');
      $data['create'] = $this->userinfo->uid;
      $data = $model->checkField($data);
      if (input('param.count')) {
        $ret = $model->where($data)->count();
      } else {
        $data['pagesize'] = input('param.pagesize/d', 0, 'intval');
        $data['page'] = input('param.page/d', 0, 'intval');
        $ret = $model->r($data, '*,`create` as user', 'addtime DESC');
      }
    } else {
      $message = '提交' . $message;
      if ($this->userinfo->uid) {
        if (input('?param.id')) {
          $data = clearArrNullRet([
            'create' => $this->userinfo->uid,
            'model' => $this->dbname,
            'mid' => input('param.id/d', null, 'intval'),
            'content' => input('param.content/s', '', 'trim'),
          ]);
          try {
            $ret = $model->create($data);
          } catch (\Throwable $th) {
            $ret = $model->where($data)->find();
          }
          $code = 200;
          $message .= '成功';
        } else {
          $message .= '缺少参数id';
        }
      } else {
        $message .= '需要登录';
      }
    }
    ext($code, $message, $ret);
  }
  /** 购买 */
  public function buy() {
    $code = 500;
    $message = '购买';
    $ret = null;
    $model = model('Buy');
    if (isGet()) {
      $message = '获取' . $message . ' 成功';
      $code = 200;
      $data = input('param.');
      $data['create'] = $this->userinfo->uid;
      $data = $model->checkField($data);
      if (input('param.count')) {
        $ret = $model->where($data)->count();
      } else {
        $data['pagesize'] = input('param.pagesize/d', 0, 'intval');
        $data['page'] = input('param.page/d', 0, 'intval');
        $ret = $model->r($data, '*,`create` as user', 'addtime DESC');
      }
    } else {
      $message = '提交' . $message;
      if ($this->userinfo->uid) {
        if (input('?param.id')) {
          $data = clearArrNullRet([
            'create' => $this->userinfo->uid,
            'model' => $this->dbname,
            'mid' => input('param.id/d', null, 'intval'),
            'content' => input('param.content/s', '', 'trim'),
          ]);
          try {
            $ret = $model->create($data);
          } catch (\Throwable $th) {
            $ret = $model->where($data)->find();
          }
          if ($ret) {
            $code = 200;
            $message .= '成功';
          } else {
            $message .= '失败:' . $th->getMessage();
          }
        } else {
          $message .= '缺少参数id';
        }
      } else {
        $message .= '需要登录';
      }
    }
    ext($code, $message, $ret);
  }
  /** 报名 */
  public function sign() {
    $code = 500;
    $message = '报名';
    $ret = null;
    $model = model('Sign');
    if (isGet()) {
      $message = '获取' . $message . ' 成功';
      $code = 200;
      $data = input('param.');
      $data['create'] = $this->userinfo->uid;
      $data = $model->checkField($data);
      if (input('param.count')) {
        $ret = $model->where($data)->count();
      } else {
        $data['pagesize'] = input('param.pagesize/d', 0, 'intval');
        $data['page'] = input('param.page/d', 0, 'intval');
        $ret = $model->r($data, '*,`create` as user', 'addtime DESC');
      }
    } else {
      $message = '提交' . $message;
      if ($this->userinfo->uid) {
        if (input('?param.id')) {
          $data = clearArrNullRet([
            'create' => $this->userinfo->uid,
            'model' => $this->dbname,
            'mid' => input('param.id/d', null, 'intval'),
          ]);
          try {
            $ret = $model->create($data);
          } catch (\Throwable $th) {
            $ret = $model->where($data)->find();
          }
          $code = 200;
          $message .= '成功';
        } else {
          $message .= '缺少参数id';
        }
      } else {
        $message .= '需要登录';
      }
    }
    ext($code, $message, $ret);
  }
  /** 类型 */
  public function type() {
    // ext($this->dbname . 'Type');
    try {
      $m = model($this->dbname . 'Type');
      $d = $m->r(input('param.'));
      $e = $m->getError();
      ext($e ? 500 : 200, $e ? $e : '获取数据成功', $d);
    } catch (\Throwable $th) {
      $m = model($this->dbname . 'Category');
      $d = $m->r(input('param.'));
      $e = $m->getError();
      ext($e ? 500 : 200, $e ? $e : '获取数据成功', $d);
    }
  }
  public function getTypeModel() {
    ext(modelType($this->dbname));
  }
  /** 达人 */
  public function master() {
    $mn = $this->dbname;
    $param = input('param.');
    $param['create'] = $this->userinfo->uid;
    $tj = encode($param);

    $d = clo(model('Member')
      ->alias('a')
      ->join(
        config('database.prefix') . 'member_info b',
        'a.uid=b.id',
        'left'
      )
      ->field("b.id,b.name,b.photo,'$tj' `master`,'$mn' `model`")
      ->where('last_login_time', '>', time() - 2592000) // 60*60*24*30 一个月
      ->order('last_login_time desc')
      ->limit(100)
      ->select());
    ext(200, '获取达人数据成功', $d);
  }
  /** 记录 */
  public function history() {
    // return $this->userinfo->uid;
    $ret = [];
    try {
      $mn = $this->dbname;
      $param = input('param.');
      $param['create'] = $this->userinfo->uid;
      $mm = model($mn);
      $ids = $mm->where($mm->toWhere($param))->column('id');
      $ret = model('History')
        ->alias('a')
        ->join(
          config('database.prefix') . 'member_info b',
          'a.create=b.id',
          'left'
        )
        ->field("b.id,b.name,b.photo,a.addtime")
        ->where('a.id', 'in', $ids)
        ->where('model', $mn)
        ->order('a.addtime desc')
        ->group('b.id')
        ->limit(100)
        // ->fetchSql()
        ->select();
    } catch (\Throwable $th) {
    }
    ext(200, '获取历史记录成功', $ret);
  }

  public function me() {
    if (!$this->userinfo->uid) {
      ext(500, '需要用户登录');
    }
    $m = model($this->dbname);
    $method = input('?param.all') ? 'select' : 'find';
    $w = $m->toWhere(['create' => $this->userinfo->uid]);
    if ($w) {
      ext(200, '获取数据成功', $m->where($w)->$method());
    } else {
      ext(500, '无此接口');
    }
  }
}
