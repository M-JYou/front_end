<?php

namespace app\common\behavior;

class InitConfig {
    public function run(&$params) {
        $qscms_config = model('\app\common\model\Config')->getCacheAll();
        $qscms_config['mobile_domain'] = $qscms_config['mobile_domain'] ? $qscms_config['mobile_domain'] : ($qscms_config['sitedomain'] . $qscms_config['sitedir'] . 'm/');
        if (isset($qscms_config['live_app_key']) && $qscms_config['live_app_key'] != '') {
            $qscms_config['live_app_secret'] = md5(md5($qscms_config['live_app_key'] . $qscms_config['live_app_secret']) . 'ergFGsdfgf545');
        } else {
            $qscms_config['live_app_secret'] = '';
        }
        \think\Config::set('global_config', $qscms_config);
    }
}
