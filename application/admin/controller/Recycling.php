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

class Recycling extends Authcheck
{
    public function index()
    {
        $limit = $this->request->has('limit') ? $this->request->param('limit') : 20;
        $model = $this->request->has('model') ? $this->request->param('model') : 'admin';
        $status = $this->request->has('status') ? $this->request->param('status') : 'status';
        $data = \think\loader::model('common/'.$model)->where([$status=>-1])->order('update_time desc')->paginate($limit,false,['query'=>$this->request->param()]);
        
        return $this->showList($data);
    }

    public function del()
    {
        $param = $this->request->param();
        $array = [];
        if(!is_array($param['id'])) {
            $array[] = $param['id'];
        } else {
            $array = $param['id'];
        }

        if(false == $this->delete(['model'=>'common/'.$param['model'],'key'=>$array])) {
            return $this->error('操作失败');
        }
        return $this->success('操作成功');
    }

    public function restore()
    {
        
        $param = $this->request->param();
        $key = $this->request->has('key') ? $this->request->param('key') : 'id';
        $status = $this->request->has('status') ? $this->request->param('status') : 'status';
        $array = [];
        if(!is_array($param['id'])) {
            $array[] = $param['id'];
        } else {
            $array = $param['id'];
        }
        if(false == $this->soft(['model'=>'common/'.$param['model'],'index'=>$array,'val'=>1,'key'=>$key,'status'=>$status])) {
            return $this->error('操作失败');
        }
        return $this->success('操作成功');
    }
}
