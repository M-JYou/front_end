<?php

namespace app\common\validate;

use \app\common\validate\BaseValidate;

class Agent extends BaseValidate {
  protected $rule =   [
    'addtime'  => 'require|gt:0',                 // '创建时间',
    'create'   => 'require|gt:0',                 // '创建人id',
    'type' => 'require|number|between:1,10',      // '类型', -- 1:代招人才; 2:代找工作; 3:找代账; 4:实操实习; 5:财务外包; 6:保就业; 7:劳务派遣用工; 8:劳务派遣就业; 9:人才测评; 10:背景调查;
    'name' => 'require|max:32',                   // '名称',

    'gender' => 'in:0,1',                         // '性别 0:女;1:男',
    'resumetag' => 'between:166,186',             // '个人标签', -- qs_category.alias=QS_resumetag
    'language' => 'between:208,213',              // '语言能力', -- qs_category.alias=QS_language
    'current' => 'between:241,245',               // '目前状态', -- qs_category.alias=QS_current
    'language_level' => 'between:291,293',        // '语言等级', -- qs_category.alias=QS_language_level

    'trade' => 'between:1,45',                    // '公司行业', -- qs_category.alias=QS_trade
    'company_type' => 'between:45,54',            // '公司类型', -- qs_category.alias=QS_company_type
    'scale' => 'between:80,85',                   // '公司规模', -- qs_category.alias=QS_scale
    'jobtag' => 'between:145,165',                // '公司标签', -- qs_category.alias=QS_jobtag

    'category_district' => 'require|gt:0',        // '工作地点',
    'diploma' => 'require|between:1,8',           // '文凭', -- 1:小学; 2:中学; 3:职高; 4:大专; 5:大学; 6:硕士; 7:研究生; 8:博士;
    'tel' => 'require|checkMobile',               // '联系电话',
    'content' => 'require|max:1024',              // '需求描述',
    'state' => 'require|number',                  // '步骤',
    'status' => 'require|number',                 // '审核状态',
    'order' => 'max:10',                          // '付款订单',
    'amount' => 'gt:0',                           // '金额',
    'appraise' => 'max:255',                      // '评价',
  ];
}
