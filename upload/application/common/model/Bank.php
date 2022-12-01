<?php

namespace app\common\model;


class Bank extends \app\common\model\BaseModel {
  protected $readonly = ['id', 'addtime'];
  protected $insert = ['addtime'];
  protected function setAddtimeAttr() {
    return time();
  }
  protected $type     = [
    'id' => 'integer', // INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
    'addtime' => 'integer', // INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
    'is_display' => 'integer', // TINYINT NOT NULL DEFAULT 1 COMMENT '软删除',
    'name' => 'string', // CHAR(32) NOT NULL DEFAULT '' COMMENT '名称',
    'cover' => 'string', // CHAR(255) NOT NULL DEFAULT '' COMMENT '封面url',
    'content' => 'string', // TEXT NOT NULL COMMENT '正文json',
    'server' => 'string', // CHAR(64) NOT NULL DEFAULT '' COMMENT '客服id',
    'tel' => 'string', // CHAR(16) NOT NULL DEFAULT '' COMMENT '电话',
    'category_district' => 'integer', // INT UNSIGNED NOT NULL COMMENT '行政区域id',
    'pid' => 'integer', // INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '上级id',
  ];
  public function getArticleAttr($v = null) {
    return model('Article')->where('other', $v)->select();
  }
  public function addr() {
    $t = model('CategoryDistrict')->get($this->category_district);
    $this['addr'] = $t ? $t['name'] : '';
    return $this;
  }
  public function _find(array $param, $retData = false) {
    try {
      if (is_array($param)) {
        if (isset($param['id']) && $param['id'] > 0) {
          $ret = $this
            ->alias('a')
            ->join('qs_category_district b', 'a.category_district=b.id', 'left')
            ->field('a.*,b.name as addr,a.id as article')
            ->find($param['id']);
        } else {
          $where = $this->toWhere($param, 'a.');
          foreach ($where as $k => $v) {
            if ($v === '') {
              unset($where[$k]);
            }
          }
          $ispage = isset($param['pagesize']) && $param['pagesize'] > 0;
          if ($ispage) {
            $total = $this->where($where)->count();
            if (($page = isset($param['page']) ? intval($param['page']) : 1) < 1) {
              $page = 1;
            }
            if (($pagesize = isset($param['pagesize']) ? intval($param['pagesize']) : 10) < 1) {
              $pagesize = 10;
            }
            $mm = $this->where($where)
              ->alias('a')
              ->orderRaw('id asc')
              ->page($page . ',' . $pagesize);
            $ret = [
              'items' => $mm
                ->alias('a')
                ->join('qs_category_district b', 'a.category_district=b.id', 'left')
                ->field('a.*,b.name as addr,a.id as article')
                ->select(),
              'total' => $total,
              'current_page' => $page,
              'pagesize' => $pagesize,
              'total_page' => ceil($total / $pagesize),
            ];
          } else {
            $ret = $this
              ->alias('a')
              ->join('qs_category_district b', 'a.category_district=b.id', 'left')
              ->field('a.*,b.name as addr,a.id as article')
              // ->fetchSql()
              ->where($where)->orderRaw('id asc')->select();
          }
        }
        if ($retData) {
          return $ret;
        }
        ext(200, '获取数据成功', $ret);
      } else {
        $this->error = '参数不正确';
      }
    } catch (\Throwable $th) {
      $this->error = $th->getMessage();
    }
    if ($retData) {
      return false;
    }
    ext(500, $this->error, $param);
  }
}
