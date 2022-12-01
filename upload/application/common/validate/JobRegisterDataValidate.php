<?php

/**
 * 职位报名信息 Validate
 * @author chenyang
 * Date Time：2022年5月7日10:11:42
 */

namespace app\common\validate;

use app\common\base\BaseValidate;

class JobRegisterDataValidate extends BaseValidate {
    protected $paramData = [
        'key_type'         => '', // 关键词类型
        'keyword'          => '', // 关键词
        'applied_position' => '', // 求职岗位
        'position_type'    => '', // 职位类型
        'handle_status'    => '', // 处理状态
        'per_page'         => '', // 每页显示数量
        'register_id'      => '', // 登记ID
        'remark'           => '', // 备注
        'add_start_time'   => '', // 报名开始时间
        'add_end_time'     => '', // 报名结束时间
    ];

    protected $interfaceParam  =   [
        // 类名
        'JobRegisterData' => [
            // 获取报名列表
            'getRegisterList' => [
                'validate' => [
                    ['field_name' => 'key_type', 'name' => '关键词类型', 'type' => 'is_numeric', 'long' => 1, 'require' => false],
                    ['field_name' => 'keyword', 'name' => '关键词', 'type' => 'is_string', 'require' => false],
                    ['field_name' => 'applied_position', 'name' => '求职岗位', 'type' => 'is_string', 'require' => false],
                    ['field_name' => 'position_type', 'name' => '职位类型', 'type' => 'is_string', 'require' => false],
                    ['field_name' => 'handle_status', 'name' => '处理状态', 'type' => 'is_numeric', 'long' => 1, 'require' => false],
                    ['field_name' => 'per_page', 'name' => '每页显示数量', 'type' => 'is_numeric', 'long' => 11, 'default' => 10, 'require' => false],
                ],
            ],
            // 获取报名详情
            'getRegisterInfo' => [
                'validate' => [
                    ['field_name' => 'register_id', 'name' => '登记ID', 'type' => 'is_numeric', 'long' => 11, 'empty' => false, 'require' => true],
                ],
            ],
            // 设置处理状态
            'setHandleStatus' => [
                'validate' => [
                    ['field_name' => 'register_id', 'name' => '登记ID', 'type' => 'is_string', 'empty' => false, 'require' => true],
                    ['field_name' => 'handle_status', 'name' => '处理状态', 'type' => 'is_numeric', 'long' => 1, 'require_value' => [0, 1], 'require' => true],
                ],
            ],
            // 设置备注
            'setRemark' => [
                'validate' => [
                    ['field_name' => 'register_id', 'name' => '登记ID', 'type' => 'is_numeric', 'long' => 11, 'empty' => false, 'require' => true],
                    ['field_name' => 'remark', 'name' => '备注', 'type' => 'is_string', 'default' => '', 'long' => 100, 'require' => true],
                ],
            ],
            // 删除报名信息
            'delRegister' => [
                'validate' => [
                    ['field_name' => 'register_id', 'name' => '登记ID', 'type' => 'is_string', 'empty' => false, 'require' => true],
                ],
            ],
            // 导出报名信息
            'exportRegisterData' => [
                'validate' => [
                    ['field_name' => 'register_id', 'name' => '登记ID', 'type' => 'is_string', 'empty' => false, 'require' => false],
                    ['field_name' => 'add_start_time', 'name' => '报名开始时间', 'type' => 'is_string', 'long' => 20, 'is_conversion' => true, 'require' => false],
                    ['field_name' => 'add_end_time', 'name' => '报名结束时间', 'type' => 'is_string', 'long' => 20, 'is_conversion' => true, 'require' => false],
                ],
            ],
        ]
    ];
}
