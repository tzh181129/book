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

namespace app\index\controller;

use app\index\controller\Base;
use \think\Cookie;
use \think\Db;


class Article extends Base
{

    //列表页
    public function index()
    {
        $model = new \app\common\model\Content();
        $limit = $this->request->has("limit") ? $this->request->param("limit") : 12;

        $param = $this->request->param();
        $where['id'] = $param['cid'];
        $where['status'] = 1;
        $where['pid']=0;
        $arr=Db::name('content_cate')->where($where)->find();
        $array=Db::name('content_cate')->where(['status'=>1,'pid'=>$arr['id']])->field('id')->select();
        if(empty($array)){
            //新闻
            $news=$model->where(['content_cate_id'=>$arr['id'],'status'=>1,'order'=>1])->order('create_time desc')->find();

            $article=$model->where(['content_cate_id'=>$arr['id'],'status'=>1,'order'=>0])
                ->order('create_time desc')
                ->paginate($limit,false,['query'=>$this->request->param()]);
            //库存分类
//            $product=Db::name('content_cate')->where(['content_cate_group_id'=>2,'status'=>1,'pid'=>9,])->order('create_time desc')->find();
            $stock=$model->where(['content_cate_id'=>$param['cid'],'status'=>1])->order('create_time asc')->select();
            foreach($stock as $k =>$vo){
                $stock[$k]['num']=$vo['num'] == 0 ? '': $vo['num'];
            }
//            $type=Db::name('content_cate')->where(['content_cate_group_id'=>2,'status'=>1,'pid'=>9,'id'=>['NOT IN',$product['id']]])->order('create_time desc')->select();
//            foreach($type as $k =>$vo){
//                $type[$k]['stock']=$model->where(['content_cate_id'=>$param['cid'],'status'=>1,'title'=>$vo['id']])->select();
//            }

            $this->assign('news',$news);
//            $this->assign('product',$product);
            $this->assign('stock',$stock);
//            $this->assign('type',$type);
        }else {
            //产品
           foreach($array as $k=>$vo){
               $article[$k]['product']=$model->where(['content_cate_id'=>$vo['id'],'status'=>1])->select();
           }
        }

        $this->assign('info',$article);
         //信息轮播
        $carousel=Db::name('carousel')->where(['carousel_cate_id'=>1,'status'=>1])->order('order asc')->select();
        $this->assign('carousel',$carousel);
        $template=Db::name('content_cate')->where(['id'=>$param['cid'],'status'=>1])->value('list_template');
        if ($template)
        {
            return $this->fetch(":{$template}");
        }
        return $this->fetch();
    }

    //详情页
    public function show()
    {
        $limit = $this->request->has("limit") ? $this->request->param("limit") : 12;
    	$param = $this->request->param();
        $validate = new \think\Validate([
            'id'  => 'require|number|length:1,11',
        ]);
        if (!$validate->check($param)) {
            return $this->error($validate->getError());
        }
    	$model = new \app\common\model\Content();
    	$article=$model->where(['id'=>$param['id'],'status'=>1])->find();
        if(empty($article)) {
            return $this->error('文章不存在');
        }
        //同类
        $info=$model
            ->where(['content_cate_id'=>$article['content_cate_id'],'status'=>1,'id'=>['NOT IN',$article['id']]])
            ->order('create_time desc')
            ->limit(3)->select();
        foreach ($info as $k =>$vo){
            $info[$k]['title']=mb_substr($vo['title'], 0, 9, 'utf-8');
            $info[$k]['description']=mb_substr($vo['description'],0,100,'utf-8');
        }
//        $article['last'] = $model->where(['status'=>1, 'content_cate_id' => $article['content_cate_id'], 'create_time'=>['<',strtotime($article['create_time'])]])->order('create_time desc')->field('id,title')->find();
//        $article['next'] = $model->where(['status'=>1, 'content_cate_id' => $article['content_cate_id'], 'create_time'=>['>',strtotime($article['create_time'])]])->order('create_time asc')->field('id,title')->find();
        $this->assign('info',$article);
        $this->assign('article',$info);

        $detail_template=Db::name('content_cate')->where(['id'=>$article['content_cate_id'],'status'=>1])->value('detail_template');
        if ($detail_template)
        {
            return $this->fetch(":{$detail_template}");
        }
    	return $this->fetch();
    }

    //单页详情页
    public function single()
    {
        $param = $this->request->param();
        $this->assign('current', $param['current']);
        $validate = new \think\Validate([
            'cid'  => 'require|number|length:1,11',
        ]);

        if (!$validate->check($param)) {
            return $this->error($validate->getError());
        }

        $model = new \app\common\model\Content();
        $article = $model->where(['status'=>1,'content_cate_id'=>$param['cid']])->find();
        if(empty($article)) {
            return $this->error('文章不存在');
        }
        $model->where(['id'=>$article['id']])->setInc('num');

        $this->assign('info',$article);

        //当前文章所属分类
        $article_cate = \think\Loader::model("common/content_cate")->where(['id' => $param['cid']])->find();

        //当前文章同级分类列表
        $cate_list = \think\Loader::model("common/content_cate")
                    ->field('id,name,is_single,url')
                    ->where(['content_cate_group_id' => $article_cate['content_cate_group_id']])
                    ->select();

        foreach ($cate_list as $k => $v) 
        {
            $content = \think\Loader::model("common/content")
                    ->field('id,title,content_cate_id')
                    ->where(['content_cate_id' => $v['id']])
                    ->find();
            $cate_list[$k]['content_id'] = $content['id'];
        }
        // var_dump(json_decode(json_encode($cate_list), true));exit;
        $this->assign('cid', $param['cid']);
        $this->assign('cate_list', $cate_list);
        if ($article_cate['detail_template']) 
        {
            return $this->fetch(":{$article_cate['detail_template']}");
        }
        return $this->fetch();
    }

    public function decoration()
    {
        $param = $this->request->param();
        $this->assign('current', $param['current']);

        $design_list = \think\loader::model('common/content')
                ->withCount(['message'=>function($query){
                                $query->where('status',1);
                            }])
                ->field('id, title, content_cate_id, img')
                ->where(['content_cate_id' => 2])
                ->order('create_time desc')
                ->limit(8)
                ->select();

        $this->assign('design_list', $design_list);

        $material_list = \think\loader::model('common/content')
                ->withCount(['message'=>function($query){
                                $query->where('status',1);
                            }])
                ->field('id, title, content_cate_id, img')
                ->where(['content_cate_id' => 8])
                ->order('create_time desc')
                ->limit(8)
                ->select();

        $this->assign('material_list', $material_list);

        $arrange_list = \think\loader::model('common/content')
                ->withCount(['message'=>function($query){
                                $query->where('status',1);
                            }])
                ->field('id, title, content_cate_id, img')
                ->where(['content_cate_id' => 9])
                ->order('create_time desc')
                ->limit(4)
                ->select();

        $this->assign('arrange_list', $arrange_list);
        return $this->fetch("decoration/decoration");
    }
}
