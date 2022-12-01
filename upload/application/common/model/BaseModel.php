<?php

namespace app\common\model;

use Closure;
use think\console\Output;
use think\Model;

use function app\apiadmin\controller\ueditor\getfiles;

class BaseModel extends Model {
  public $map_platform = [
    'app' => 'APP',
    'mobile' => '手机浏览器',
    'wechat' => '微信浏览器',
    'miniprogram' => '微信小程序',
    'web' => '电脑浏览器',
    'system' => '系统',
  ];
  public $map_ad_platform = [
    'app' => 'APP',
    'mobile' => '触屏端',
    'miniprogram' => '微信小程序',
    'web' => 'pc端',
  ];
  public $map_education = [
    1 => '初中',
    2 => '高中',
    3 => '中技',
    4 => '中专',
    5 => '大专',
    6 => '本科',
    7 => '硕士',
    8 => '博士',
    9 => '博后',
  ];
  public $map_experience = [
    1 => '应届生',
    2 => '1年',
    3 => '2年',
    4 => '3年',
    5 => '3-5年',
    6 => '5-10年',
    7 => '10年以上',
  ];
  public $chkey = ['state', 'status', 'is_display'];
  public function getColumn() {
    $tablename = $this->getTablename();
    if (false === ($columbs = cache('cache_table_column_' . $tablename))) {
      $res = \think\Db::query(
        'SHOW COLUMNS FROM ' . config('database.prefix') . $tablename
      );
      $columbs = array_column($res, 'Field');
      cache('cache_table_column_' . $tablename, $columbs);
    }
    return $columbs;
  }
  public function getUpdataColumn() {
    $tablename = $this->getTablename();
    if (false === ($columbs = cache('cache_table_update_column_' . $tablename))) {
      $columbs = $this->getColumn();
      foreach ($this->readonly as $v) { // 去除只读字段
        $i = array_search($v, $columbs);
        if ($i !== false) {
          array_splice($columbs, $i, 1);
        }
      }
      array_unshift($columbs, 'id');
      cache('cache_table_update_column_' . $tablename, $columbs);
    }
    return $columbs;
  }
  public function getName() {
    return $this->name;
  }
  public function getTablename() {
    return uncamelize($this->name);
  }
  protected function check_spell_repeat($spell, $index = 0, $id = 0) {
    $spell = del_punctuation($spell);
    $spell_index = $index == 0 ? $spell : $spell . $index;
    $map['spell'] = array('eq', $spell_index);
    if ($id > 0) {
      $map['id'] = array('neq', $id);
    }
    $has = $this->where($map)->find();
    if ($has) {
      $index++;
      $spell_index = $this->check_spell_repeat($spell, $index, $id);
    }
    return $spell_index;
  }
  /** 处理薪资显示 */
  public function handle_wage($minwage, $maxwage, $negotiable = 0) {
    $wage_unit = config('global_config.wage_unit');
    if ($negotiable == 0) {
      if ($wage_unit == 1) {
        $minwage =
          $minwage % 1000 == 0
          ? $minwage / 1000 . 'K'
          : round($minwage / 1000, 1) . 'K';
        $maxwage = $maxwage
          ? ($maxwage % 1000 == 0
            ? $maxwage / 1000 . 'K'
            : round($maxwage / 1000, 1) . 'K')
          : 0;
      } elseif ($wage_unit == 2) {
        if ($minwage >= 10000) {
          if ($minwage % 10000 == 0) {
            $minwage = $minwage / 10000 . '万';
          } else {
            $minwage = round($minwage / 10000, 1);
            $minwage = strpos($minwage, '.')
              ? str_replace('.', '万', $minwage)
              : $minwage . '万';
          }
        } else {
          if ($minwage % 1000 == 0) {
            $minwage = $minwage / 1000 . '千';
          } else {
            $minwage = round($minwage / 1000, 1);
            $minwage = strpos($minwage, '.')
              ? str_replace('.', '千', $minwage)
              : $minwage . '千';
          }
        }
        if ($maxwage >= 10000) {
          if ($maxwage % 10000 == 0) {
            $maxwage = $maxwage / 10000 . '万';
          } else {
            $maxwage = round($maxwage / 10000, 1);
            $maxwage = strpos($maxwage, '.')
              ? str_replace('.', '万', $maxwage)
              : $maxwage . '万';
          }
        } elseif ($maxwage) {
          if ($maxwage % 1000 == 0) {
            $maxwage = $maxwage / 1000 . '千';
          } else {
            $maxwage = round($maxwage / 1000, 1);
            $maxwage = strpos($maxwage, '.')
              ? str_replace('.', '千', $maxwage)
              : $maxwage . '千';
          }
        } else {
          $maxwage = 0;
        }
      }
      if ($maxwage == 0) {
        $return = '面议';
      } else {
        if ($minwage == $maxwage) {
          $return = $minwage;
        } else {
          $return = $minwage . '~' . $maxwage;
        }
      }
    } else {
      $return = '面议';
    }
    return $return;
  }
  /** 处理正文,自动解码 */
  protected function getContentAttr($v) {
    return decode($v);
  }
  protected function checkContent($v) {
    $d = $this->getData();
    $points = $d ? (isset($d['points']) ? $d['points'] : (isset($d['price']) ? $d['price'] : (isset($d['expense']) ? $d['expense'] : 0))) : 0;
    return $points != 0
      && isset($d['buy'])
      && !(model('Buy')->getModel([
        'mid' => $d['id'],
        'model' => $this->name,
        'create' => $d['buy']
      ])->find())
      ? '付费内容,需要购买!' : decode($v);
  }
  /** 解码JSON other */
  protected function getOtherAttr($v) {
    return decode($v);
  }
  protected function getViewsAttr($v) {
    $r = $this->getData('click');
    if ($r !== null) {
      $this->where('id', $this->getData('id'))->setInc('click');
      $r += 1;
    }
    return $r;
  }
  protected function getReprintAttr($v) {
    $v = decode($v);
    return $this->retOtherAttr(is_array($v) ? $v :
      ['mid' => $v, 'model' => $this->name, '#method' => 'count']);
  }
  protected function getFavoritesAttr($v) {
    $v = decode($v);
    return $this->retOtherAttr(is_array($v) ? $v :
      ['mid' => $v, 'model' => $this->name, '#method' => 'count']);
  }
  protected function getCommentAttr($v) {
    $d = func_get_arg(1);
    $s = $d && isset($d['info5']) ? '`info5`' : '`comment`';
    $v = decode($v);
    $w = is_array($v) ? $v :
      [
        'mid' => $v, 'model' => $this->name,
        '#method' => 'count',
        '#field' => "*,`create` user,`id` $s",
        '#order' => 'addtime DESC',
        '#method' => 'select'
      ];
    return $this->retOtherAttr($w);
  }
  protected function getReportAttr($v) {
    $v = decode($v);
    return $this->retOtherAttr(is_array($v) ? $v :
      ['mid' => $v, 'model' => $this->name, '#method' => 'count']);
  }
  protected function getLikesAttr($v) {
    $v = decode($v);
    return $this->retOtherAttr(is_array($v) ? $v :
      ['mid' => $v, 'model' => $this->name, '#method' => 'count']);
  }
  protected function getInfo5Attr($v) {
    $this->setAttr('info5', $v);
    $r = func_get_arg(2);
    $ret = $v ? [
      'reprint' => $this->getReportAttr($v, $this, $r),
      'favorites' => $this->getFavoritesAttr($v, $this, $r),
      'comment' => $this->getCommentAttr($v, $this, $r),
      'report' => $this->getReportAttr($v, $this, $r),
      'likes' => $this->getLikesAttr($v, $this, $r)
    ] : [];
    return $ret;
  }
  protected function getUserAttr($v) {
    return model('MemberInfo')->field('id,nickname,photo,online,id expert')->find($v);
  }
  protected function getChildrenAttr($v) {
    $ret = $this->field('id,name,pid,id `children`')->where('pid', $v);
    $t = $this->_getFields();
    foreach ($this->chkey as $k) {
      if (in_array($k, $t)) {
        $ret = $ret->where($k, 1);
      }
    }
    $ret = $ret->select();
    return $ret ? $ret : null;
  }
  protected function getType_Attr($v) {
    try {
      return model($this->name . 'Type')->where('id', $v)->field('name')->find()->getData('name');
    } catch (\Throwable $th) {
    }
    try {
      return model($this->name . 'Category')->where('id', $v)->field('name')->find()->getData('name');
    } catch (\Throwable $th) {
      return $v;
    }
  }
  protected function getAddr_Attr($v) {
    $m = model('CategoryDistrict');
    $r = [];
    $d = ['pid' => $v];
    while ($d = $m->where('id', $d['pid'])->field('pid,name')->find()) {
      $r[] = $d['name'];
    }
    return join('-', array_reverse($r));
  }
  protected function getDistrict_Attr($v) {
    $d = model('CategoryDistrict')->where('id', $v)->find();
    return $d ? $d['name'] : ('错误:' . $v);
  }
  protected function getHistoryAttr($v) {
    $v = decode($v);
    if (!is_array($v)) {
      $v = ['mid' => $v];
    }
    if (!isset($v['model'])) {
      $v['model'] = $this->name;
    }
    if (isset($v['#method'])) {
      if ($v['#method'] == 0) {
        $v['#method'] = 'select';
        if (!isset($v['#alias'])) {
          $v['#alias'] = 'a';
          $v['#join'] = [[
            config('database.prefix') . 'member_info b',
            'a.create=b.id',
            'left'
          ]];
          $v['#field'] = 'b.id,b.nickname,b.photo';
          $v['#group'] = 'a.create';
        }
      }
    } else {
      $v['#method'] = 'count';
    }

    return $this->retOtherAttr($v);
  }

  protected function getUnameAttr($v) {
    return model('MemberInfo')->where('id', $v)->field('name')->find()->getData('name');
  }
  protected function getRMBAttr($v) {
    return sprintf('%.2f', $v / config('global_config.payment_rate'));
  }
  protected function getCreate_Attr($v) {
    return model('MemberInfo')->where('id', $v)->field('name')->find()['name'];
  }
  protected function getCreateInfoAttr($v) {
    return model('MemberInfo')->where('id', $v)->field('id,name,photo')->find();
  }
  protected function getModelDataAttr($v, $d) {
    if (isset($d['mid']) && isset($d['model'])) {
      $v = model($d['model'])->where('id', $d['mid'])->find();
    }
    return $v;
  }

  protected function retOtherAttr($v, string $modelName = null) {
    if (is_string($v)) {
      $v = decode($v);
    }
    $w = is_array($v) ? $v : ['id' => $v];
    $fn = debug_backtrace()[1]['function'];
    $s = [
      '#model' => mb_substr($fn, 3, strlen($fn) - 7),
      '#field' => '*',
      '#order' => null,
      '#limit' => null,
      '#group' => null,
      '#alias' => null,
      '#join' => null,
      '#method' => 'find',
    ];
    foreach ($s as $k => $value) {
      isset($w[$k]) && $s[$k] = $w[$k];
      unset($w[$k]);
    }
    $method = $s['#method'];
    !isset($w['is_display']) && $w['is_display'] = ['>', 0];
    !isset($w['status']) && $w['status'] = ['>', 0];
    !isset($w['state']) && $w['state'] = ['>', 0];
    // if ($modelName == 'GoodsType2') {
    //   // outp($modelName, $v, $w);
    //   $w['status1'] = 1;
    // }
    // echo '2'.$w['create'].'=';
    $m = model($modelName ? $modelName : $s['#model'])->getModel($w);


    if ($s['#alias']) {
      $m = $m->alias($s['#alias']);
    }
    if ($s['#join']) {
      $m = $m->join($s['#join']);
    }
    if ($s['#field']) {
      $m = $m->field($s['#field']);
    }
    if ($s['#order']) {
      $m = $m->order($s['#order']);
    }
    if ($s['#limit']) {
      $m = $m->limit($s['#limit']);
    }
    if ($s['#group']) {
      $m = $m->group($s['#group']);
    }
    // if ($s['#method']=='aaaa') {
    //   outp($ret = $m
    //   ->fetchSql()
    //   ->select());
    // }
    $ret = $m
      // ->fetchSql()
      ->$method();
    return $ret;
  }

  protected function setPidAttr($v) {
    cache('cache_model_tree_' . $this->name);
    return $v;
  }
  /** 获取树形 */
  public function tree($where = ['pid' => 0]) {
    $cacheName = 'cache_model_tree_' . $this->name;
    if (isset($this->id) && isset($this->pid) && isset($this->name) && !($ret = cache($cacheName))) {
      $ret = $this->field('id,name,pid,id `children`')->where($where)->select();
      cache($cacheName, $ret);
    }
    return $ret;
  }
  public function _getFields() {
    return isset($this->fields) ? $this->fields : $this->getColumn();
  }
  public function checkField($param = null) {
    return checkArray($this->_getFields(), $param);
  }
  public function toWhere(array $param, $keyPrefix = '') {
    $where = [];
    if ($keyPrefix && substr($keyPrefix, -1) != '.') {
      $keyPrefix .= '.';
    }
    if (isset($param['#like'])) {
      $where[join('|' . $keyPrefix, $this->_getFields())] = [
        'like', is_array($param['#like']) ? ('%' . join('%', $param['#like']) . '%') : $param['#like']
      ];
    }
    if (isset($param['id'])) {
      if ($param['id']) {
        $where[$keyPrefix . 'id'] = $param['id'];
      }
      unset($param['id']);
    }
    $t = checkArray($this->_getFields(), $param);
    foreach ($t as $key => $value) {
      $where[$keyPrefix . $key] = trimAll($value);
    }
    return $where;
  }
  public function getById($param = null, $field = '*', $except = false) {
    $ret = null;
    if ($param) {
      if (is_string($param) || is_numeric($param)) {
        $ret = $this->field($field, $except)->find($param);
      } elseif (is_array($param) && isset($param['id'])) {
        $ret = $this->field($field, $except)->find($param['id']);
      }
    }
    return $ret;
  }
  public function c($param = null) {
    return $this->create(checkFields($param, $this->getColumn()));
  }
  public function u($param = null, $where = null) {
    try {
      if (is_array($param) && (isset($param['id']) || $where)) {
        if (!$where) {
          $where = ['id' => $param['id']];
        }
        $this->validate(true)->update(checkFields($param, $this->getUpdataColumn(), $this->type), $where);
        return $this->r($where);
      }
    } catch (\Throwable $th) {
      $this->error = $th->getMessage();
    }
    return false;
  }
  public function getModel(array $param, $keyPrefix = '') {
    $ret = $this;
    $where = [];
    if (!$keyPrefix) {
      $keyPrefix = '';
    }
    if ($keyPrefix && substr($keyPrefix, -1) != '.') {
      $keyPrefix .= '.';
    }
    $fs = $this->_getFields();

    if (isset($param['#order']) && is_string($param['#order']) && $param['#order']) {
      $t = explode(',', $param['#order']);
      $ro = [];
      foreach ($t as $tv) {
        if ($tv) {
          if (count($tva = explode(' ', $tv)) == 2 && in_array($tva[0], $fs)) {
            $ro[] = $keyPrefix . $tva[0] . ' ' . (strtolower($tva[1]) == 'asc' ? 'asc' : 'desc');
          }
        }
      }
      $ret = $ret->order(join(',', $ro));
    }
    if (isset($param['#like']) && $param['#like']) {
      $la = is_array($param['#like']) ? $param['#like'] : [$param['#like']];
      foreach ($fs as $fv) {
        if (!isset($this->type[$fv]) || $this->type[$fv] == 'string' || $this->type[$fv] == 'array') {
          foreach ($la as $lv) {
            $ret = $ret->whereOr($keyPrefix . $fv, 'like', $lv);
          }
        }
      }
    }
    $t = checkArray($fs, $param);
    foreach ($t as $key => $value) {
      $k = $keyPrefix . $key;
      $v = trimAll($value);
      if (isExp($v)) {
        if (is_string($v[1]) && $v[1][0] == '`') {
          $l = strlen($v[1]);
          if ($v[1][--$l] == $v[1][0] && in_array(substr($v[1], 1, --$l), $fs)) {
            $ret = $ret->where($k, 'exp', getExp($v));
          }
        } else {
          // ext($k, $v);
          $ret = $ret->where($k, $v[0], $v[1]);
        }
        continue;
      } elseif (is_array($v)) {
        $v = ['in', $v];
      } else {
        $v = ['=', $v];
      }
      $where[$k] = $v;
    }
    // if (isset($param['status1'])) {
    //   outp($ret->where($where)->fetchSql()->select());
    // }
    // ext($where);
    // ext($ret->where($where)->fetchSql()->select());
    return $ret->where($where);
  }
  public function getField2(array $param, $field, string $dn, string $sn) {
    if (isset($param[$sn])) {
      if ($field) {
        if (strpos($field, $sn) === false) {
          $field .= ",`$dn` `$sn`";
        }
      } else {
        $field = "*,`$dn` `$sn`";
      }
    }
    return $field;
  }
  public function r(array $param = [], $field = null, $order = null, $colum = null) {
    try {
      if (is_array($param)) {
        $field = $this->getField2($param, $field, 'id', 'info5');
        $fs = $this->_getFields();
        foreach ($param as $k => $value) {
          // echo (in_array($k, $fs) ? 1 : 0) . $k . "\n";
          if (substr($k, -1) === '_' && in_array(($sk = substr($k, 0, strlen($k) - 1)), $fs)) {
            $field = $this->getField2($param, $field, $sk, $k);
          }
        }
        if (!$order) {
          $order = null; // 'id desc';
        }
        $method = isset($param['id']) && !is_array($param['id']) && (is_numeric($param['id']) || is_string($param['id'])) ? 'find' :  'select';
        $ispage = isset($param['pagesize']) && $param['pagesize'] > 0;
        if ($ispage) {
          $total = $this->getModel($param)->count();
          if (($page = isset($param['page']) && $param['page'] > 0 ? intval($param['page']) : 1) < 1) {
            $page = 1;
          }
          if (($pagesize = isset($param['pagesize']) ? intval($param['pagesize']) : 10) < 1) {
            $pagesize = 10;
          }
          $mm = $this->getModel($param)
            ->page($page . ',' . $pagesize);
          if ($order) {
            $mm = $mm->orderRaw($order);
          }
          $ret = [
            'items' => $colum ? $mm->column($colum) : $mm->field($field)->select(),
            'total' => $total,
            'current_page' => $page,
            'pagesize' => $pagesize,
            'total_page' => ceil($total / $pagesize),
          ];
        } else {

          $mm = $this->getModel($param)
            // ->fetchSql()
            ->field($field);
          if ($order) {
            $mm = $mm->orderRaw($order);
          }

          $ret = $colum ? $mm->column($colum) : $mm->$method();
          if ($ret && isset($ret['id']) && $method == 'find') {
            model('History')->adddata([
              'model' => $this->name,
              'create' => $param['#history'],
              'mid' => $ret['id']
            ]);
          }
        }
        return $ret;
      } else {
        $this->error = '参数不正确';
      }
    } catch (\Throwable $th) {
      $this->error = $th->getMessage();
    }
    return false;
  }
  public function d($param = null) {
    if (count($this->toWhere($param))) {
      return $this->getModel($param)->delete();
    }
  }
  public function s(array $param = null, array $where = null, $validate = false, $allowField = false) {
    if (count($param)) {
      $d = checkArray($this->_getFields(), $param);
      ext($d);
      return $this->validate($validate)->allowField($allowField)->save($d, $where);
    }
    return false;
  }
}
