<?php

/**
 * 字段设置 Validate
 * @author chenyang
 * Date Time：2022年4月21日18:14:41
 */

namespace app\common\validate;

use app\common\base\BaseValidate;

class FieldSettingValidate extends BaseValidate {
    protected $paramData = [
        'field_type'       => '', // 字段类型:1|文本框,2|单选框,3|复选框,4|下拉框,5|多行文本
        'field_name'       => '', // 字段名称
        'field_remark'     => '', // 字段备注
        'is_system'        => '', // 是否系统字段:0|否,1|是
        'is_display'       => '', // 是否显示:0|否,1|是
        'field_id'         => '', // 字段ID
        'key_type'         => '', // 关键词类型
        'keyword'          => '', // 关键词
        'per_page'         => '', // 每页显示数量
        'field_value_list' => '', // 字段内容信息
        'field_value'      => '', // 字段值
        'sort'             => '', // 排序
        'field_alias'      => '', // 字段别名
    ];

    protected $interfaceParam  =   [
        // 类名
        'FieldSetting' => [
            // 获取字段列表
            'getFieldList' => [
                'validate' => [
                    ['field_name' => 'key_type', 'name' => '关键词类型', 'type' => 'is_numeric', 'long' => 1, 'require' => false],
                    ['field_name' => 'keyword', 'name' => '关键词', 'type' => 'is_string', 'require' => false],
                    ['field_name' => 'field_type', 'name' => '字段类型', 'type' => 'is_numeric', 'long' => 1, 'require' => false],
                    ['field_name' => 'is_system', 'name' => '是否系统字段', 'type' => 'is_numeric', 'long' => 1, 'require' => false],
                    ['field_name' => 'is_display', 'name' => '是否显示', 'type' => 'is_numeric', 'long' => 1, 'require' => false],
                    ['field_name' => 'per_page', 'name' => '每页显示数量', 'type' => 'is_numeric', 'long' => 11, 'default' => 10, 'require' => false],
                ],
            ],
            // 获取字段详情
            'getFieldInfo' => [
                'validate' => [
                    ['field_name' => 'field_id', 'name' => '字段ID', 'type' => 'is_numeric', 'long' => 11, 'empty' => false, 'require' => true],
                ],
            ],
            // 新增字段
            'addField' => [
                'validate' => [
                    ['field_name' => 'field_name', 'name' => '字段名称', 'type' => 'is_string', 'long' => 6, 'empty' => false, 'require' => true],
                    ['field_name' => 'field_remark', 'name' => '字段备注', 'type' => 'is_string', 'long' => 50, 'default' => '', 'require' => false],
                    ['field_name' => 'field_type', 'name' => '字段类型', 'type' => 'is_numeric', 'long' => 1, 'require_value' => [1, 2, 3, 4, 5], 'default' => 1, 'empty' => false, 'require' => true],
                    ['field_name' => 'is_display', 'name' => '是否显示', 'type' => 'is_numeric', 'long' => 1, 'require_value' => [0, 1], 'require' => true],
                    [
                        'field_name' => 'field_value_list', 'name' => '字段内容信息', 'type' => 'is_array', 'empty' => true, 'require' => false,
                        'deep' => [
                            ['field_name' => 'field_value', 'name' => '字段值', 'type' => 'is_string', 'long' => 8, 'empty' => false, 'require' => true],
                            ['field_name' => 'sort', 'name' => '排序', 'type' => 'is_numeric', 'long' => 11, 'require' => true],
                        ],
                    ],
                ],
            ],
            // 编辑字段
            'editField' => [
                'validate' => [
                    ['field_name' => 'field_id', 'name' => '字段ID', 'type' => 'is_numeric', 'long' => 11, 'empty' => false, 'require' => true],
                    ['field_name' => 'field_name', 'name' => '字段名称', 'type' => 'is_string', 'long' => 6, 'empty' => false, 'require' => true],
                    ['field_name' => 'field_remark', 'name' => '字段备注', 'type' => 'is_string', 'long' => 50, 'default' => '', 'require' => false],
                    ['field_name' => 'field_type', 'name' => '字段类型', 'type' => 'is_numeric', 'long' => 1, 'require_value' => [1, 2, 3, 4, 5], 'default' => 1, 'empty' => false, 'require' => true],
                    ['field_name' => 'is_display', 'name' => '是否显示', 'type' => 'is_numeric', 'long' => 1, 'require_value' => [0, 1], 'require' => true],
                    [
                        'field_name' => 'field_value_list', 'name' => '字段内容信息', 'type' => 'is_array', 'empty' => true, 'require' => false,
                        'deep' => [
                            ['field_name' => 'field_value', 'name' => '字段值', 'type' => 'is_string', 'long' => 8, 'empty' => false, 'require' => true],
                            ['field_name' => 'sort', 'name' => '排序', 'type' => 'is_numeric', 'long' => 11, 'require' => true],
                        ],
                    ],
                ],
            ],
            // 删除字段
            'delField' => [
                'validate' => [
                    ['field_name' => 'field_id', 'name' => '字段ID', 'type' => 'is_string', 'empty' => false, 'require' => true],
                ],
            ],
            // 获取字段内容
            'getFieldValueList' => [
                'validate' => [
                    ['field_name' => 'field_alias', 'name' => '字段别名', 'type' => 'is_string', 'empty' => false, 'require' => true],
                ],
            ],
        ]
    ];
}
