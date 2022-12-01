<?php

namespace app\common\model;

class AdminNotice extends \app\common\model\BaseModel {
    protected $readonly = ['id'];

    public function send($notice_id, $first, $keyword1, $remark) {
        $admin_notice = self::where('id', $notice_id)->find();
        $wechatNotifyRule = model('WechatNotifyRule')->where('alias', 'matter')->field('is_open,tpl_id')->find();
        if (!empty($admin_notice) && $admin_notice['is_open'] == 1 && $wechatNotifyRule['is_open'] == 1) {
            $notice_arr = model('AdminNoticeConfig')
                ->alias('c')
                ->join(
                    config('database.prefix') . 'admin a',
                    'c.admin_id=a.id',
                    'LEFT'
                )
                ->where('notice_id', $notice_id) //管理员微信通知条件写死修改 tapd:200
                ->select();
            foreach ($notice_arr as $k => $v) {
                $instance = new \app\common\lib\Wechat;
                $res = $instance->tmp_message(
                    $v['openid'],
                    $wechatNotifyRule['tpl_id'],
                    [
                        'first' => [
                            'value' => $first,
                        ],
                        'keyword1' => [
                            'value' => $keyword1,
                        ],
                        'keyword2' => [
                            'value' => date('Y-m-d H:i:s'),
                        ],
                        'remark' => [
                            'value' => $remark,
                            'color' => '#CC6600'
                        ],
                    ],
                    ''
                );
            }
        }
    }
}
