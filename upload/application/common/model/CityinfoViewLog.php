<?php

namespace app\common\model;

use Think\Db;

class CityinfoViewLog extends \app\common\model\BaseModel {
    protected $autoWriteTimestamp = true;
    protected $createTime = 'addtime';
    protected $updateTime = 'updatetime';
    protected $readonly = ['id'];

    const HOT_TOTAL = 20;  //热门信息条数固定,只取前20条


    public function getHotList() {
        $cache_key = 'cityinfo_hotlist';
        $cache = cache($cache_key);
        if ($cache) return $cache;
        $m = new CityinfoArticle();

        $prefix = config('database.prefix');
        $sql = sprintf("select a.article_id,count(*)as view_count,b.id,b.title,b.is_top,b.desc,b.view_times,b.addtime,b.endtime,b.refreshtime,b.imgs,b.type_id,c.title as type_name  
            from {$prefix}cityinfo_view_log a 
            left join {$prefix}cityinfo_article b on a.article_id=b.id
            left join {$prefix}cityinfo_type c on b.type_id=c.id
            where b.audit=1 and b.endtime>%d and b.is_public=1
            group by a.article_id 
            order by view_count desc limit %d", time(), self::HOT_TOTAL);
        $list = Db::query($sql);
        /**  if(count($list)<self::HOT_TOTAL){//数据不足或为空的解决办法
            $list2 = $m->getHostList(self::HOT_TOTAL-count($list));
            if(!empty($list2)){
                $list = array_merge($list, $list2);
            }
        } */
        $m->processArticleMulti($list);
        cache($cache_key, $list, 3600);
        return $list;
    }

    public function article() {
        return $this->hasOne('CityinfoArticle', 'id', 'article_id')->field('id,desc,endtime,audit,is_public,title,view_times,imgs,type_id,addtime');
    }
}
