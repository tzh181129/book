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
use think\Db;
use think\Cache;
use app\tplay\lib\Authcheck;

class User extends Authcheck
{
    public function index()
    {
        $limit = $this->request->has('limit') ? $this->request->param('limit') : 20;
        $data = \think\loader::model('common/user')->order('create_time desc')->paginate($limit,false,['query'=>$this->request->param()]);
        
        return $this->showList($data);
    }

    public function shielding()
    {
        $param = $this->request->param();
        $array = [];
        if(!is_array($param['id'])) {
            $array[] = $param['id'];
        } else {
            $array = $param['id'];
        }
        if(false == $this->soft(['model'=>'common/user','index'=>$array])) {
            return $this->error('操作失败');
        }
        return $this->success('操作成功');
    }

    public function message()
    {
        $limit = $this->request->has('limit') ? $this->request->param('limit') : 20;
        $data = \think\loader::model('common/message')->order('create_time desc')->paginate($limit,false,['query'=>$this->request->param()])->each(function($item, $key){
                $item->contentName = Db::name('content')->where(['id'=>$item->content_id])->value('title');
            });
        
        return $this->showList($data);
    }

    public function shieldingMsg()
    {
        $param = $this->request->param();
        $array = [];
        if(!is_array($param['id'])) {
            $array[] = $param['id'];
        } else {
            $array = $param['id'];
        }
        if(false == $this->soft(['model'=>'common/message','index'=>$array])) {
            return $this->error('操作失败');
        }
        return $this->success('操作成功');
    }

    public function delMsg()
    {
        $param = $this->request->param();
        $array = [];
        if(!is_array($param['id'])) {
            $array[] = $param['id'];
        } else {
            $array = $param['id'];
        }
        if(false == $this->delete(['model'=>'common/message','key'=>$array])) {
            return $this->error('操作失败');
        }
        return $this->success('操作成功');
    }

    public function contact()
    {
        $limit = $this->request->has('limit') ? $this->request->param('limit') : 20;
        $data = \think\loader::model('common/contact')->order('create_time desc')->paginate($limit,false,['query'=>$this->request->param()]);
        
        return $this->showList($data);
    }

    public function delContact()
    {
        $param = $this->request->param();
        $array = [];
        if(!is_array($param['id'])) {
            $array[] = $param['id'];
        } else {
            $array = $param['id'];
        }
        if(false == $this->delete(['model'=>'common/contact','key'=>$array])) {
            return $this->error('操作失败');
        }
        return $this->success('操作成功');
    }
}
