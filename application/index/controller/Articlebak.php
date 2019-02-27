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
    public function index()
    {
        $model = new \app\common\model\Content();
        $limit = $this->request->has("limit") ? $this->request->param("limit") : 10;

        $param = $this->request->param();
        if(!empty($param['article_cate_id']) and $param['article_cate_id'] > 0){
        	$where['article_cate_id'] = $param['article_cate_id'];
        }
        if (isset($param['keywords']) and !empty($param['keywords'])) {
            $where['title|content'] = ['like', '%' . $param['keywords'] . '%'];
        }
        $where['status'] = 1;

        $article = $model->withCount(['message'=>function($query){
                                        $query->where('status',1);
                                    }])->where($where)->order('create_time desc')->paginate($limit,false,['query'=>$this->request->param()]);

        $this->assign('info',$article);
        return $this->fetch();
    }

    public function show()
    {
    	$param = $this->request->param();
    	$validate = new \think\Validate([
		    'id'  => 'require|number|length:1,11',
		]);

		if (!$validate->check($param)) {
			return $this->error($validate->getError());
		}

    	$model = new \app\common\model\Content();
    	$article = $model->where(['status'=>1,'id'=>$param['id']])->find();
    	if(empty($article)) {
    		return $this->error('文章不存在');
    	}
    	$model->where(['id'=>$article['id']])->setInc('num');

        $article['last'] = $model->where(['status'=>1,'create_time'=>['<',strtotime($article['create_time'])]])->order('create_time desc')->field('id,title')->find();
        $article['next'] = $model->where(['status'=>1,'create_time'=>['>',strtotime($article['create_time'])]])->order('create_time asc')->field('id,title')->find();
        
        $this->assign('info',$article);

        $messages = \think\Loader::model("common/message")->where(['status'=>1,'content_id'=>$article['id']])->order('create_time desc')->paginate(10,false,['fragment'=>'tpblog-msg']);
        $this->assign('messages',$messages);

    	return $this->fetch();
    }
}
