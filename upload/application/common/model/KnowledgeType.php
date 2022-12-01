<?php

namespace app\common\model;

class KnowledgeType extends \app\common\model\BaseModel {
  protected $readonly = ['id', 'addtime'];
  protected $type = [
    'id' => 'integer',
  ];
}
