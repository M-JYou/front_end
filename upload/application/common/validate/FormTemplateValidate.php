<?php

/**
 * 表单模板 Validate
 * @author chenyang
 * Date Time：2022年4月22日17:00:37
 */

namespace app\common\validate;

use app\common\base\BaseValidate;

class FormTemplateValidate extends BaseValidate {
    protected $paramData = [
        'template_id'   => '', // 模板ID
        'template_name' => '', // 模板名称
        'template_desc' => '', // 模板备注
        'source'        => '', // 来源
        'field_list'    => '', // 字段信息
        'field_id'      => '', // 字段ID
        'is_must'       => '', // 是否必填:0|否,1|是
        'is_display'    => '', // 是否显示:0|否,1|是
        'sort'          => '', // 排序
        'key_type'      => '', // 关键词类型
        'keyword'       => '', // 关键词
        'per_page'      => '', // 每页显示数量
    ];

    protected $interfaceParam  =   [
        // 类名
        'FormTemplate' => [
            // 获取模板列表
            'getTemplateList' => [
                'validate' => [
                    ['field_name' => 'key_type', 'name' => '关键词类型', 'type' => 'is_numeric', 'long' => 1, 'require' => false],
                    ['field_name' => 'keyword', 'name' => '关键词', 'type' => 'is_string', 'require' => false],
                    ['field_name' => 'source', 'name' => '来源', 'type' => 'is_numeric', 'long' => 1, 'empty' => false, 'require' => true],
                    ['field_name' => 'per_page', 'name' => '每页显示数量', 'type' => 'is_numeric', 'long' => 11, 'default' => 10, 'require' => false],
                ],
            ],
            // 获取模板详情
            'getTemplateInfo' => [
                'validate' => [
                    ['field_name' => 'template_id', 'name' => '模板ID', 'type' => 'is_numeric', 'long' => 11, 'empty' => false, 'require' => true],
                ],
            ],
            // 新增模板
            'addTemplate' => [
                'validate' => [
                    ['field_name' => 'template_name', 'name' => '模板名称', 'type' => 'is_string', 'long' => 20, 'empty' => false, 'require' => true],
                    ['field_name' => 'template_desc', 'name' => '模板备注', 'type' => 'is_string', 'long' => 50, 'require' => false],
                    ['field_name' => 'source', 'name' => '来源', 'type' => 'is_numeric', 'long' => 1, 'empty' => false, 'require' => true],
                    [
                        'field_name' => 'field_list', 'name' => '字段信息', 'type' => 'is_array', 'empty' => false, 'require' => false,
                        'deep' => [
                            ['field_name' => 'field_id', 'name' => '字段ID', 'type' => 'is_numeric', 'long' => 11, 'empty' => false, 'require' => true],
                            ['field_name' => 'is_must', 'name' => '是否必填', 'type' => 'is_numeric', 'long' => 1, 'require_value' => [0, 1], 'require' => true],
                            ['field_name' => 'is_display', 'name' => '是否显示', 'type' => 'is_numeric', 'long' => 1, 'require_value' => [0, 1], 'require' => true],
                            ['field_name' => 'sort', 'name' => '排序', 'type' => 'is_numeric', 'long' => 11, 'require' => true],
                        ],
                    ],
                ],
            ],
            // 编辑模板
            'editTemplate' => [
                'validate' => [
                    ['field_name' => 'template_id', 'name' => '模板ID', 'type' => 'is_numeric', 'long' => 11, 'empty' => false, 'require' => true],
                    ['field_name' => 'template_name', 'name' => '模板名称', 'type' => 'is_string', 'long' => 20, 'empty' => false, 'require' => true],
                    ['field_name' => 'template_desc', 'name' => '模板备注', 'type' => 'is_string', 'long' => 50, 'require' => false],
                    ['field_name' => 'source', 'name' => '来源', 'type' => 'is_numeric', 'long' => 1, 'empty' => false, 'require' => true],
                    [
                        'field_name' => 'field_list', 'name' => '字段信息', 'type' => 'is_array', 'empty' => false, 'require' => false,
                        'deep' => [
                            ['field_name' => 'field_id', 'name' => '字段ID', 'type' => 'is_numeric', 'long' => 11, 'empty' => false, 'require' => true],
                            ['field_name' => 'is_must', 'name' => '是否必填', 'type' => 'is_numeric', 'long' => 1, 'require_value' => [0, 1], 'require' => true],
                            ['field_name' => 'is_display', 'name' => '是否显示', 'type' => 'is_numeric', 'long' => 1, 'require_value' => [0, 1], 'require' => true],
                            ['field_name' => 'sort', 'name' => '排序', 'type' => 'is_numeric', 'long' => 11, 'require' => true],
                        ],
                    ],
                ],
            ],
            // 删除模板
            'delTemplate' => [
                'validate' => [
                    ['field_name' => 'template_id', 'name' => '模板ID', 'type' => 'is_numeric', 'long' => 11, 'empty' => false, 'require' => true],
                ],
            ],
            // 更改使用
            'changeUse' => [
                'validate' => [
                    ['field_name' => 'template_id', 'name' => '模板ID', 'type' => 'is_numeric', 'long' => 11, 'empty' => false, 'require' => true],
                ],
            ],
            // 获取模板默认字段
            'getDefaultField' => [
                'validate' => [
                    ['field_name' => 'source', 'name' => '模板来源', 'type' => 'is_numeric', 'long' => 1, 'empty' => false, 'require' => true],
                ],
            ],
            // 获取字段列表
            'getFieldList' => [
                'validate' => [
                    ['field_name' => 'source', 'name' => '模板来源', 'type' => 'is_numeric', 'long' => 1, 'empty' => false, 'require' => true],
                ],
            ],
        ]
    ];
}
