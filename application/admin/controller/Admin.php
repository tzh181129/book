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

class Admin extends Authcheck
{
    public function index()
    {
        $limit = $this->request->has('limit') ? $this->request->param('limit') : 20;
        $admin = \think\loader::model('common/admin')->where(['status'=>1])->order('create_time desc')->paginate($limit,false,['query'=>$this->request->param()])->each(function($item, $key){
            $item->login_time = date('Y-m-d H:i:s',$item->last_login_time);
        });
        
        return $this->showList($admin);
    }

    public function groupList()
    {
        $limit = $this->request->has('limit') ? $this->request->param('limit') : 20;
        $group = \think\loader::model('common/adminGroup')->where(['status'=>['>',0]])->order('create_time desc')->paginate($limit,false,['query'=>$this->request->param()]);

        return $this->showList($group);
    }

    public function add()
    {
        if($this->request->isPost()) {
            $post = $this->request->post();

            $validate = new \think\Validate([
                'username'  => 'require',
                'password' => 'require|confirm',
                'nickname' => 'require',
                'phone' => 'require|number|length:11',
            ]);
            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }
            if(!empty(Db::name('admin')->where(['username'=>$post['username']])->find())) {
                return $this->error('用户名已被占用');
            }

            $post['salt'] = salt();
            $post['password'] = md5(md5($post['password']).$post['salt']);
            if(empty($post['group_id'])) {
                if(false == \think\loader::model('common/admin')->allowField(true)->save($post)) {
                    return $this->error('操作失败');
                }
            } else {
                // 启动事务
                Db::startTrans();
                try{
                    \think\loader::model('common/admin')->allowField(true)->save($post);
                    $admin_id = \think\loader::model('common/admin')->id;
                    $list = [];
                    foreach ($post['group_id'] as $k => $v) {
                        $list[$k]['uid'] = $admin_id;
                        $list[$k]['group_id'] = $v;
                    }
                    \think\loader::model('common/adminGroupAccess')->saveAll($list);
                    // 提交事务
                    Db::commit();    
                } catch (\Exception $e) {
                    // 回滚事务
                    Db::rollback();
                    return $this->error('操作失败');
                }
            }
            return $this->success('操作成功');
        }
    }

    public function edit()
    {
        if($this->request->isPost()) 
        {
            $post = $this->request->post();

            $validate = new \think\Validate([
                'id' => 'require',
                'nickname' => 'require',
                'phone' => 'require|number|length:11'
            ]);
            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }
            
            $info['admin'] = \think\loader::model('common/admin')->where(['id'=>$post['id']])->find();
            if(empty($info['admin']) or (!empty($post['username']) and $post['username'] != $info['admin']['username'])) {
                return $this->error('用户名不可修改！');
            }

            // 启动事务
            Db::startTrans();
            try{
                \think\loader::model('common/admin')->allowField(true)->save($post,['id'=>$post['id']]);
                \think\loader::model('common/adminGroupAccess')->where('uid','eq',$post['id'])->delete();
                if(!empty($post['group_id'])) {
                    $list = [];
                    foreach ($post['group_id'] as $k => $v) {
                        $list[$k]['uid'] = $post['id'];
                        $list[$k]['group_id'] = $v;
                    }
                    \think\loader::model('common/adminGroupAccess')->saveAll($list);
                }
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
            if(empty($id)) {
                return $this->error('参数不正确');
            }
            $info['admin'] = \think\loader::model('common/admin')->where(['id'=>$id])->find();
            if(empty($info['admin'])) {
                return $this->error('非法操作');
            }

            $info['group'] = \think\loader::model('common/adminGroup')->where(['status'=>1])->select();
            $info['admin_group'] = \think\loader::model('common/adminGroupAccess')->where(['uid'=>$id])->select();
            $this->success('','',$info);
        }
    }

    public function editPwd()
    {
        if($this->request->isPost()){
            $post = $this->request->post();
            $validate = new \think\Validate([
                'old_pwd'  => 'require|different:password',
                'password' => 'require|confirm'
            ]);
            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }
            $admin = Db::name('admin')->where(['id'=>$this->admin_id])->find();
            if(md5(md5($post['old_pwd']).$admin['salt']) !== $admin['password']){
                return $this->error('旧密码错误');
            }
            $salt = salt();
            if(false == Db::name('admin')->where(['id'=>$this->admin_id])->setField(['password'=>md5(md5($post['password']).$salt),'salt'=>$salt])){
                return $this->error('操作失败');
            } else {
                return $this->success('操作成功');
            }
        }
    }

    public function del()
    {
        if($this->request->isPost()) {
            $post = $this->request->post();
            $array = [];
            if(!is_array($post['id'])) {
                $array[] = $post['id'];
            } else {
                $array = $post['id'];
            }
            foreach ($array as $v) {
                if($v == $this->admin_id) {
                    return $this->error('不能删除自己');
                }
            }
            if(false == $this->soft(['model'=>'common/admin','index'=>$array])) {
                return $this->error('操作失败');
            }
            return $this->success('操作成功');
        }
    }

    public function addGroup()
    {
        if($this->request->isPost()) {
            $post = $this->request->post();
            $validate = new \think\Validate([
                'title'  => 'require'
            ]);
            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }
            if(!empty($post['rule_id'])) {
                $post['rules'] = implode(',',$post['rule_id']);
            }
            if(false == \think\loader::model('common/adminGroup')->allowField(true)->save($post)) {
                return $this->error('操作失败');
            }
            return $this->success('操作成功');
        }
    }

    public function editGroup()
    {
        if($this->request->isPost()) {
            $post = $this->request->post();
            $validate = new \think\Validate([
                'title'  => 'require'
            ]);
            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }
            if(!empty($post['rule_id'])) {
                $post['rules'] = implode(',',$post['rule_id']);
            }
            if(false == \think\loader::model('common/adminGroup')->allowField(true)->save($post,['id'=>$post['id']])) {
                return $this->error('操作失败');
            }
            return $this->success('操作成功');
        } else {
            $id = $this->request->param('id');
            $info['group'] = \think\loader::model('common/adminGroup')->where(['id'=>$id])->find();
            if(empty($info['group'])) {
                return $this->error('非法操作');
            }
            if(!empty($info['group']['rules'])) {
               $info['group']['rules'] = explode(',',$info['group']['rules']); 
            }

            // $info['rule'] = \think\loader::model('common/adminRule')->where(['type'=>1])->select();
            
            $menu = $this->getMenuList();
            // var_dump($menu);
            foreach ($menu as $k => $v) 
            {
                if (isset($v['children']) && is_array($v['children'])) 
                {
                    $menu_child = $v['children'];
                    foreach ($menu_child as $vo => $item) 
                    {
                        $rule = \think\loader::model('common/adminRule')->where(['type'=>1, 'mid' => $item['id']])->select();
                        if ($rule) 
                        {
                            $menu[$k]['children'][$vo]['rule'] = json_decode(json_encode($rule), true);
                        }
                    }
                }
            }

            $info['rule'] = $menu;

            return $this->show(1,'ok',$info);
        }
    }

    public function delGroup()
    {
        if($this->request->isPost()) {
            $post = $this->request->post();
            $array = [];
            if(!is_array($post['id'])) {
                $array[] = $post['id'];
            } else {
                $array = $post['id'];
            }
            if(false == $this->soft(['model'=>'common/adminGroup','index'=>$array])) {
                return $this->error('操作失败');
            }
            return $this->success('操作成功');
        }
    }

    /**
     * 个人中心
     */
    public function profile()
    {
        // $id = $this->request->param('id');

        $data = \think\loader::model('common/admin')->where(['id' => session('admin.id')])->find();
        $data['image'] = $data['avatar'];
        if(empty($data)) 
        {
            return $this->error('非法操作');
        }

        return $this->show(1,'ok',$data);
    }

    public function editAvatar()
    {
        // $param = $this->request->param();
        $res   = $this->upload('admin', "avatar");
        if($res['code'] == 1)
        {
            $data['avatar'] = $res['data'];
            if(false == \think\loader::model('common/admin')->allowField(true)->save($data, ['id' => session('admin.id')])) 
            {
                return $this->error('操作失败');
            }
            return $this->show(1,'', $res['data']);
        }
        return $this->show(0, $res['msg']);
    }

    private function getMenuList()
    {
        $admin = \think\loader::model('common/admin')->where(['id' => $this->admin_id])->find();
        if ($admin['is_admin']) 
        {
            $menu = $this->menulist(\think\loader::model('common/adminMenu')->where(['status' => 1])->order('sort asc')->select());

            return $menu;
        }

        $group = \think\loader::model('common/adminGroupAccess')->where(['uid' => $this->admin_id])->find();
        
        $admin_group = \think\loader::model('common/adminGroup')->where(['id' => $group['group_id']])->find();
        
        $rules_where['id'] = ['in', $admin_group['rules']];
        $rules = \think\loader::model('common/adminRule')->field('mid')->where($rules_where)->select();

        $rules_arr = [];
        foreach ($rules as $k => $v) 
        {
            if (!in_array($v['mid'], $rules_arr)) 
            {
                $rules_arr[$k] = $v['mid'];
            }
        }
        $rules_str = implode(",", $rules_arr);

        //获取菜单的父级id
        $menupid_where['id'] = ['in', $rules_str];
        $menu_pid = \think\loader::model('common/adminMenu')->field('pid')->where($menupid_where)->select();
        $menu_pid_arr = [];
        foreach ($menu_pid as $k => $v) 
        {
            if (!in_array($v['pid'], $menu_pid_arr)) 
            {
                $menu_pid_arr[$k] = $v['pid'];
            }
        }
        $rules_arr = array_merge($menu_pid_arr, $rules_arr);
        array_push($rules_arr, '1');//添加首页菜单
        $rules_str = implode(",", $rules_arr);
        
        $menu_where['status'] = 1;
        $menu_where['id'] = ['in', $rules_str];

        $menu = $this->menulist(\think\loader::model('common/adminMenu')->where($menu_where)->order('sort asc')->select());

        return $menu;
    }

    protected function menulist($menu,$id=0,$level=0){
        $menus = array();
        //先找出顶级菜单
        foreach ($menu as $k => $v) {
            if($v['pid'] == 0) {
                $data=[
                    'id' => $v['id'],
                    'pid' => $v['pid'],
                    'title' => $v['title'],
                    'icon' => $v['icon'],
                    'path' => $v['path'],
                    'open' => 0,
                ];
                if (session('menu_parent_id') == $v['id']) 
                {
                    $data['open'] = 1;
                }
                $menus[$k] = $data;
            }
        }
        //通过顶级菜单找到下属的子菜单
        foreach ($menus as $k => $val) {
            foreach ($menu as $key => $v) {
                if($v['pid'] == $val['id']) {
                    $data=[
                        'id' => $v['id'],
                        'pid' => $v['pid'],
                        'title' => $v['title'],
                        'icon' => $v['icon'],
                        'path' => $v['path'],
                        'open' => 0,
                    ];
                    if (session('menu_child_id') == $v['id']) 
                    {
                        $data['open'] = 1;
                    }
                    $menus[$k]['children'][] = $data;
                }
            }
        }
        //三级菜单
        foreach ($menus as $k => $val) {
            if(isset($val['list'])) {
                foreach ($val['list'] as $ks => $vals) {
                    foreach ($menu as $key => $v) {
                        if($v['pid'] == $vals['id']) {
                            $data=[
                                'id' => $v['id'],
                                'pid' => $v['pid'],
                                'title' => $v['title'],
                                'icon' => $v['icon'],
                                'path' => $v['path'],
                                'open' => 0,
                            ];
                            $menus[$k]['children'][$ks]['children'][] = $data;
                        }
                    }
                }
            }
        }
        return $menus;
    }
}
