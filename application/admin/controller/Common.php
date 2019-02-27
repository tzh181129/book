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

namespace app\admin\controller;

use think\Cache;
use think\Controller;
use think\Db;
use think\Url;
use think\Session;
use think\Cookie;
use app\tplay\lib\TplayPro;


class Common extends TplayPro
{
    public $request;
    protected function _initialize()
    {
        parent::_initialize();
        $this->request=\think\Request::instance();
    }
    
    public function login(){
        if($this->request->isPost()){
            $this->request=\think\Request::instance();
            $post=input("post.");
            if(empty($post['vercode'])){
                TplayPro::show(0,"验证码必填");
            }
            if(!captcha_check($post['vercode'])){
                TplayPro::show(0,"验证码错误");
            }
            if(!isset($post['username'])||!isset($post['password'])){
                TplayPro::show(0,"会员名和密码必填");
            }
            $info=\think\loader::model('common/admin')->where(["username"=>$post['username']])->find();
            if(!$info){
                TplayPro::show(0,"会员名错误");
            }
            if($info->status <= 0){
                TplayPro::show(403,"禁止登录");
            }
            if(md5(md5($post['password']).$info->salt) !== $info->password){
                TplayPro::show(0,"密码错误");
            }else{
                $info->last_login_time = time();
                $info->last_login_ip = $this->request->ip();
                $info->login_num = $info->login_num+1;
                $info->save();
                $token=$this->generate_token(['id'=>$info->id,'phone'=>$info->phone]);
                Session::set('admin',['id'=>$info->id,'access_token'=>$token]);
                Cache::set("admin".$info->id,$info->toArray());
                TplayPro::show(1,"登录成功",['access_token'=>$token]);
            }
        }
    }

    
    public function logout(){
        $id = Session::get('admin.id');
        Cache::rm('admin'.$id);
        Session::delete('admin');
        TplayPro::show(401,"退出成功");
    }

    /**
     * 生成token
     * @param array $val 要生成token的数据
     * @param int $expTime 超时时间
     * @return string
     */
    protected function generate_token($val)
    {
         return sha1(md5(uniqid(md5(microtime(true)),true)).json_encode($val['phone']).$val['id']);
    }
}
