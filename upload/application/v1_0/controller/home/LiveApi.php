<?php

namespace app\v1_0\controller\member;

class LiveApi extends \app\v1_0\controller\common\Base {
    public function getUid() {
        $data = [
            'uid' => $this->userinfo->uid,
            'mobile' => $this->userinfo->mobile
        ];
        $this->ajaxReturn(200, '获取UID', $data);
    }
}
