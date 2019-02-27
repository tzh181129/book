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

use app\index\controller\Base;
use \think\Session;
use \think\Db;

class Index extends Base
{
    public function index()
    {
        $article = \think\loader::model('common/content')->withCount(['message'=>function($query){
                                        $query->where('status',1);
                                    }])->where(['status'=>1])->order('create_time desc')->limit(5)->select();
        $this->assign('article',$article);
        $carousel = \think\loader::model('common/carousel')->where(['carousel_cate_id'=>Db::name('carousel_cate')->where('is_main',1)->value('id')])->order('order asc')->select();
        $this->assign('carousel',$carousel);
        return $this->fetch();
    }
}
