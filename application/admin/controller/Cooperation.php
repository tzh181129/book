<?php

namespace app\admin\controller;

use think\Db;
use think\Cache;
use app\tplay\lib\Authcheck;

class Cooperation extends Authcheck
{
    //合作列表
    public function cooperation(){
        $limit = $this->request->has('limit') ? $this->request->param('limit') : 20;
        $data = \think\loader::model('common/cooperation')
            ->where(['status'=>0])
            ->order('create_time desc')
            ->paginate($limit, false, ['query' => $this->request->param()])
            ->each(function ($item, $key) {
            });
        return $this->showList($data);
    }

    public function addCooperation(){
        if ($this->request->isPost()) {
            $post = $this->request->post();

            $validate = new \think\Validate([
                'content' => 'require',
            ]);
            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }

            $post['create_time'] = time();
            $post['status']=0;
            // 启动事务
            Db::startTrans();
            try {
                \think\loader::model('common/cooperation')->allowField(true)->save($post);
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                return $this->error('操作失败');
            }
            return $this->success('操作成功');
        } else {

            return $this->show(1, 'ok');
        }
    }

    public function editCooperation(){
        if ($this->request->isPost()) {
            $post = $this->request->post();

            $validate = new \think\Validate([
                'id' => 'require',
            ]);
            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }
            if(isset($post['field'])) {
                $post['is_show']= $post['value'];
            }

            // 启动事务
            Db::startTrans();
            try {
                \think\loader::model('common/cooperation')->allowField(true)->save($post,['id'=>$post['id']]);
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                return $this->error('操作失败');
            }
            return $this->success('操作成功');

        } else {
            $id = $this->request->param('id');
            if (empty($id)) {
                return $this->error('参数不正确');
            }
            $data = Db::name('cooperation')->where(['id'=>$id])->find();
            $this->success('', '', $data);
        }
    }

    //合作列表
    public function league(){
        $limit = $this->request->has('limit') ? $this->request->param('limit') : 20;
        $data = \think\loader::model('common/cooperation')
            ->where(['status'=>1])
            ->order('create_time desc')
            ->paginate($limit, false, ['query' => $this->request->param()])
            ->each(function ($item, $key) {
            });
        return $this->showList($data);
    }

    public function addLeague(){
        if ($this->request->isPost()) {
            $post = $this->request->post();

            $validate = new \think\Validate([
                'content' => 'require',
            ]);
            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }

            $post['create_time'] = time();
            $post['status']=1;

            // 启动事务
            Db::startTrans();
            try {
                \think\loader::model('common/cooperation')->allowField(true)->save($post);
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                return $this->error('操作失败');
            }
            return $this->success('操作成功');
        } else {

            return $this->show(1, 'ok');
        }
    }

    public function editLeague(){
        if ($this->request->isPost()) {
            $post = $this->request->post();

            $validate = new \think\Validate([
                'id' => 'require',
            ]);
            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }
            if(isset($post['field'])) {
                $post['is_show']= $post['value'];
            }

            // 启动事务
            Db::startTrans();
            try {
                \think\loader::model('common/cooperation')->allowField(true)->save($post,['id'=>$post['id']]);
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                return $this->error('操作失败');
            }
            return $this->success('操作成功');

        } else {
            $id = $this->request->param('id');
            if (empty($id)) {
                return $this->error('参数不正确');
            }
            $data = Db::name('cooperation')->where(['id'=>$id])->find();
            $this->success('', '', $data);
        }
    }

    //合作列表
    public function xueyixia(){
        $limit = $this->request->has('limit') ? $this->request->param('limit') : 20;
        $data = \think\loader::model('common/cooperation')
            ->where(['status'=>2])
            ->order('create_time desc')
            ->paginate($limit, false, ['query' => $this->request->param()])
            ->each(function ($item, $key) {
            });
        return $this->showList($data);
    }

    public function addXueyixia(){
        if ($this->request->isPost()) {
            $post = $this->request->post();

            $validate = new \think\Validate([
                'content' => 'require',
            ]);
            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }

            $post['create_time'] = time();
            $post['status']=2;
            // 启动事务
            Db::startTrans();
            try {
                \think\loader::model('common/cooperation')->allowField(true)->save($post);
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                return $this->error('操作失败');
            }
            return $this->success('操作成功');
        } else {

            return $this->show(1, 'ok');
        }
    }

    public function editXueyixia()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();

            $validate = new \think\Validate([
                'id' => 'require',
            ]);
            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }
            if (isset($post['field'])) {
                $post['is_show'] = $post['value'];
            }

            // 启动事务
            Db::startTrans();
            try {
                \think\loader::model('common/cooperation')->allowField(true)->save($post, ['id' => $post['id']]);
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                return $this->error('操作失败');
            }
            return $this->success('操作成功');

        } else {
            $id = $this->request->param('id');
            if (empty($id)) {
                return $this->error('参数不正确');
            }
            $data = Db::name('cooperation')->where(['id' => $id])->find();
            $this->success('', '', $data);
        }
    }



}
