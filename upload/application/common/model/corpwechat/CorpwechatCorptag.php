<?php


namespace app\common\model\corpwechat;


use app\common\lib\Pinyin;
use app\common\model\BaseModel;
use think\Cache;

class CorpwechatCorptag extends BaseModel
{
    /**
     * @Purpose:
     * 模型初始化
     * @author  Administrator
     * @version 1.1
     * @since   2022/3/1
     */
    protected static function init()
    {
        self::event('after_write', function () {
            Cache::rm('cache_corpwechat_corptag');
            Cache::rm('cache_corpwechat_corptag_tree');
        });
        self::event('after_delete', function () {
            Cache::rm('cache_corpwechat_corptag');
            Cache::rm('cache_corpwechat_corptag_tree');
        });
        self::event('after_update', function () {
            Cache::rm('cache_corpwechat_corptag');
            Cache::rm('cache_corpwechat_corptag_tree');
        });
        self::event('after_insert', function () {
            Cache::rm('cache_corpwechat_corptag');
            Cache::rm('cache_corpwechat_corptag_tree');
        });
    }


    /**
     * @Purpose:
     * 获取标签
     * @Method getCache()
     *
     * @param string $group_id 标签组ID
     *
     * @return array|mixed
     *
     * @throws null
     *
     * @link XXXXXXXXXX
     *
     * @author  Administrator
     * @version 1.1
     * @since   2022/3/1
     */
    public function getCache($group_id = 'all')
    {
        $data = cache('cache_corpwechat_corptag');
        if (false === $data) {
            $list = $this->order('order desc,id asc')->column(
                'id,group_id,name',
                'id'
            );
            $data = [];
            foreach ($list as $key => $value) {
                $data[$value['group_id']][$value['id']] = $value['name'];
                $data['all'][$value['id']] = $value['name'];
            }
            cache('cache_corpwechat_corptag', $data);
        }
        if ($group_id != '') {
            $data = isset($data[$group_id]) ? $data[$group_id] : [];
        }
        return $data;
    }


    public function getTreeCache()
    {
        $list = cache('cache_corpwechat_corptag_tree');
        if (false === $list) {
            $list = [];
            $top = $this->getCache('0');
            foreach ($top as $key => $value) {
                $first = [];
                $first['id'] = $key;
                $first['name'] = $value;
                $first_children = $this->getCache($key);
                if ($first_children) {
                    $i = 0;
                    foreach ($first_children as $k => $v) {
                        $second['id'] = $k;
                        $second['name'] = $v;
                        $second_children = $this->getCache($k);
                        if ($second_children) {
                            $j = 0;
                            foreach ($second_children as $k1 => $v1) {
                                $third['id'] = $k1;
                                $third['name'] = $v1;
                                $second['children'][$j] = $third;
                                $third = [];
                                $j++;
                            }
                        } else {
                            $second['children'] = [];
                        }
                        $first['children'][$i] = $second;
                        $second = [];
                        $i++;
                    }
                } else {
                    $first['children'] = [];
                }
                $list[] = $first;
            }
            cache('cache_corpwechat_corptag_tree', $list);
        }
        return $list;
    }

}