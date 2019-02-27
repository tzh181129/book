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

class Rule extends Authcheck
{
    public function index()
    {
        $limit = $this->request->has('limit') ? $this->request->param('limit') : 20;
        $data = \think\loader::model('common/adminRule')->where(['type'=>['>',0]])->order('mid')->paginate($limit,false,['query'=>$this->request->param()]);
        
        return $this->showList($data);
    }

    public function edit()
    {
        if($this->request->isPost()) {
            $post = $this->request->post();
            $validate = new \think\Validate([
                'id'  => 'require',
                'field' => 'require',
                'value' => 'length:1,50',
            ]);
            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }
            if(empty(Db::name('admin_rule')->where(['id'=>$post['id']])->find())) {
                return $this->error('非法请求');
            }

            $data[$post['field']] = $post['value'];
            if(false == \think\loader::model('common/adminRule')->allowField(true)->save($data,['id'=>$post['id']])) {
                return $this->error('操作失败');
            }
            return $this->success('操作成功');
        } else {
            return $this->error('非法请求');
        }
    }

    public function add()
    {
        if($this->request->isPost()) {
            $post = $this->request->post();
            if (isset($post['cid'])) 
            {
                $post['mid'] = $post['cid'];
            }
            else
            {
                $post['mid'] = $post['pid'];
            }
            unset($post['cid'], $post['pid']);

            $validate = new \think\Validate([
                'title'  => 'require|length:1,50',
                'name' => 'require|length:1,50',
                'condition' => 'length:1,200',
            ]);
            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }

            if(!empty(Db::name('admin_rule')->where(['name'=>$post['name']])->find())) {
                return $this->error('规则已存在');
            }
            
            if(false == \think\loader::model('common/adminRule')->allowField(true)->save($post)) {
                return $this->error('操作失败');
            }
            return $this->success('操作成功');
        } else {
            return $this->error('非法请求');
        }
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
        if(false == $this->soft(['model'=>'common/adminRule','index'=>$array,'status'=>'type'])) {
            return $this->error('操作失败');
        }
        return $this->success('操作成功');
    }
}
