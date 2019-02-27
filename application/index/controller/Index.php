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
        $param = $this->request->param();
        $this->assign('current', $param['current']);

        $design_list = \think\loader::model('common/content')
                ->withCount(['message'=>function($query){
                                $query->where('status',1);
                            }])
                // ->field('id, title, content_cate_id, img')
                ->where(['content_cate_id' => 6])
                ->order('create_time desc')
                ->limit(5)
                ->select();

        $this->assign('design_list', $design_list);

        $article = \think\loader::model('common/content')
                ->withCount(['message'=>function($query){
                                $query->where('status',1);
                            }])
                ->where(['status'=>1])
                ->order('create_time desc')
                ->limit(5)
                ->select();

        $this->assign('article',$article);

        $carousel = \think\loader::model('common/carousel')
                ->where(['carousel_cate_id' => Db::name('carousel_cate')->where('is_main',1)->value('id'),'order'=>1])
                ->select();
        foreach ($carousel as $k => $v) 
        {
            $carousel[$k]['img'] = str_replace("\\", "/", $v['img']);
        }
        
        $product_where['content_cate_id'] = ['in', '5,14,15,16,17,18'];
        $product_where['status'] = 1;
        $product_list = \think\loader::model('common/content')
                ->withCount(['message'=>function($query){
                                $query->where('status',1);
                            }])
                // ->field('id, title, content_cate_id, img')
                ->where($product_where)
                ->order('create_time desc')
                ->limit(8)
                ->select();
        foreach ($product_list as $k => $v) 
        {
            $product_list[$k]['images'] = explode(",", $v['img']);
        }

        $content_cate_where['id'] = ['in', '5,14,15,16,17,18'];
        $content_cate = Db::name('content_cate')->field('id,name')->where($content_cate_where)->select();

        $cate_name_arr = [];
        foreach ($content_cate as $k => $v) 
        {
            $product = Db::name('content')->field('title,img')->where(['content_cate_id' => $v['id']])->find();
            // $content_cate[$k]['title'] = $product['title'];
            $content_cate[$k]['img'] = request()->domain().$product['img'];
            $cate_name_arr[] = $v['name'];
        }
        $this->assign('content_cate', $content_cate);

        $cate_name_json = '';
        foreach ($cate_name_arr as $value) 
        {
            $cate_name_json[] = urlencode($value);
        } 
        $cate_name_json = json_encode($cate_name_json);

        $this->assign('cate_name_json', urldecode($cate_name_json));
        $case_list = \think\loader::model('common/content')
                ->withCount(['message'=>function($query){
                                $query->where('status',1);
                            }])
                ->where(['status'=>1,'tag'=>1])
                ->limit(3)
                ->select();
        // foreach ($case_list as $k => $v) 
        // {
        //     $case_list[$k]['images'] = explode(",", $v['img']);
        // }

        $news_list = \think\loader::model('common/content')
                ->withCount(['message'=>function($query){
                                $query->where('status',1);
                            }])
                // ->field('id, title, content_cate_id, img')
                ->where(['status'=>1, 'content_cate_id' => 8])
                ->order('create_time desc')
                ->limit(8)
                ->select();
        foreach ($news_list as $k => $v) 
        {
            $news_list[$k]['images'] = explode(",", $v['img']);
        }

        $this->assign('news_list', $news_list);
        $this->assign('case_list', $case_list);
        $this->assign('product_list', $product_list);
        $this->assign('carousel',$carousel);
        return $this->fetch();
    }

    public function welcome()
    {
        return $this->fetch();
    }

    public function bespoke(){
        $RealName=$_POST['RealName'];
        $Telephone=$_POST['Telephone'];
        $data=['RealName'=>$RealName,'Telephone'=>$Telephone,'bespoke_time'=>time(),'status' =>0];
        $bespoke=\think\loader::model('common/bespoke')->insert($data);
        if ($bespoke != null) {
            $result = array('msg' => 'aaaa');
            echo(json_encode($result));
        } else {
            return false;
        }
    }
    public function rebespoke(){
        $RealName=$_POST['Name'];
        $Telephone=$_POST['phone'];
        $data=['RealName'=>$RealName,'Telephone'=>$Telephone,'bespoke_time'=>time(),'status' =>0];
        $bespoke=\think\loader::model('common/bespoke')->insert($data);
        if ($bespoke != null) {
            $result = array('msg' => 'aaaa');
            echo(json_encode($result));
        } else {
            return false;
        }
    }


}
