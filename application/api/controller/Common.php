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

namespace app\api\controller;
use app\api\service\WXBizDataCrypt;
use app\api\controller\Base;
use think\Config;
use think\Session;
use think\Cookie;
use think\Db;
use think\Cache;
use \think\Validate;

class Common extends Base
{
    public function login()
    {

        if ($this->login_result) {
            return $this->show(1, '登录成功', $this->setSign($this->login_result));
        }
        return $this->show(0, '登录失败', "登录失败");
    }
    //判断是否登录
    public function isLogin()
    {
        if ($this->login_result['openid']) {
            return $this->show(1, '已登录', '已登录');
        }
        return $this->show(0, '未登录', '未登录');
    }

}
