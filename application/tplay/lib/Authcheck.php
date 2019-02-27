<?php
// +----------------------------------------------------------------------
// | Tplay [ WE ONLY DO WHAT IS NECESSARY ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://tplay.pengyichen.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 听雨 < 389625819@qq.com >
// +----------------------------------------------------------------------


namespace app\tplay\lib;
use app\tplay\lib\Auth;
use app\tplay\lib\Base;

class Authcheck extends Base
{
    protected function _initialize()
    {
        parent::_initialize();
        $rule = $this->controller.'/'.$this->action;
        $auth = new Auth();
        if(false == $auth->check($rule,$this->admin_id)){
            return $this->error('想啥呢，咱没那权限');
        }
    }
}
