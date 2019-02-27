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
use think\Db;
use think\Cache;
use think\Session;
use app\tplay\lib\TplayPro;

class Base extends TplayPro
{
    public $admin_id;
    public $param;
    public $module;
    public $controller;
    public $action;
    
    protected function _initialize()
    {
        parent::_initialize();

        $this->_setBaseInfo();

        $this->_setUserInfo();

        $this->userLog();
    }

    protected function _setBaseInfo(){
        $this->module = strtolower($this->request->module());
        $this->controller = strtolower($this->request->controller());
        $this->action = strtolower($this->request->action());
        $this->param = $this->request->param();

        $this->admin_id = $this->_checkToken();
        if(empty($this->admin_id)){
            return $this->show(401,"登录凭证已过期,请重新登录");
        }
    }

    protected function _setUserInfo(){
        $admin_info=Cache::get("admin".$this->admin_id);
        if(!$admin_info){
            $admin=\think\Loader::model("common/admin")->find($this->admin_id);
            if($admin->status<0){
                return $this->show(401,"当前账户出现异常,请联系超级管理员");
            }
            $admin_info=$admin->toArray();
            \think\Cache::set("admin".$this->admin_id,$admin_info);
        }
    }

    protected function _checkToken()
    {
        if(!Session::has('admin')) {
            TplayPro::show(401,"您的登录凭证已过期，请重新登录！");
        }

        return Session::get('admin.id');
    }
}
