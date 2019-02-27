<?php
// +----------------------------------------------------------------------
// | Tpblog [ WE ONLY DO WHAT IS NECESSARY ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://pengyichen.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 听雨 < 389625819@qq.com >
// +----------------------------------------------------------------------
namespace app\index\controller;

use \think\Db;
use \think\Cookie;
use \think\Cache;
use \think\Request;
use app\tplay\lib\TplayPro;

class Base extends \think\Controller
{
    public function __construct(Request $request)
    {
        // $domain_arr = explode(".", request()->domain());
        // preg_match('/(\w+)\.com/', $domain, $a);
        // var_dump($domain_arr[1]);

        //动态设置模板
        // config('template.view_path','template/'.$domain_arr[1].'/');

        // 是否为手机访问
//        if (Request::instance()->isMobile())
//        {
//            // echo '当前为手机访问';
//            config('template.view_path','template/mobile/');
//        }

        parent::__construct($request);
    }
    
    public function _initialize()
    {
        TplayPro::userLog();
        $options = [
            // 缓存类型为File
            'type'  =>  'File', 
            // 缓存有效期为永久有效
            'expire'=>  7200, 
            //缓存前缀
            'prefix'=>  'tplay',
             // 指定缓存目录
            'path'  =>  '../runtime/cache/',
        ];
        Cache::connect($options);

        if(false == Cache::get('menu')) {
            $main_menu_id = Db::name('index_menu_cate')->where(['is_main'=>1])->value('id');
            // $menu = Db::name('index_menu')->where(['status'=>1,'index_menu_cate_id'=>$main_menu_id])->select();
            $where['status'] = 1;
            $where['index_menu_cate_id'] = $main_menu_id;
            $where['pid'] = 0;
            $parent_menu = Db::name('index_menu')->where($where)->select();
            $where['pid'] = ['gt', 0];
            $child_menu  = Db::name('index_menu')->where($where)->select();

            foreach ($parent_menu as $k => $v) 
            {
                $list = [];
                foreach ($child_menu as $key => $val) 
                {
                    if ($val['pid'] == $v['id']) 
                    {
                        $list[] = $val;
                    }
                }
                $parent_menu[$k]['list'] = $list;
                
            }
            Cache::set('menu',$parent_menu,7200);
        } else {
            $parent_menu = Cache::get('menu');
        }

        $this->assign('menu',$parent_menu);

        if(false == Cache::get('web')) {
            $web = getSystem('web_config');
            Cache::set('web',$web,7200);
        } else {
            $web = Cache::get('web');
        }
    	$this->assign('web',$web);

        if(Cookie::has('user')) {
            $user = Db::name('user')->where(['login_string'=>Cookie::get('user')])->find();
            if(!empty($user)){
                $this->assign('user',$user);
            }
        }
    }
}
