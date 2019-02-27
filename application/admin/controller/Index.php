<?php
// +----------------------------------------------------------------------
// | Tplay [ WE ONLY DO WHAT IS NECESSARY ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://tplay.pengyichen.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 听雨 < 389625819@qq.com >
// +----------------------------------------------------------------------

namespace app\admin\controller;
use think\Db;
use think\Cache;
use app\tplay\lib\Base;

class Index extends Base
{
    public function getRoutes()
    {
        $routes_arr = [
            [
                "path" => "/",
                "component" => "views/app.html",
                "name" => "控制面板"
            ],
            [
                "path" => "/admin/editPwd",
                "component" => "views/admin/editPwd.html",
                "name" => "修改密码"
            ],
            [
                "path" => "/admin/index",
                "component" => "views/admin/index.html",
                "name" => "管理员"
            ],
            [
                "path" => "/admin/add",
                "component" => "views/admin/add.html",
                "name" => "新增管理员"
            ],
            [
                "path" => "/admin/edit",
                "component" => "views/admin/edit.html",
                "name" => "更改管理员"
            ],
            [
                "path" => "/admin/auth",
                "component" => "views/admin/auth.html",
                "name" => "管理员"
            ],
            [
                "path" => "/admin/addGroup",
                "component" => "views/admin/addGroup.html",
                "name" => "新增权限组"
            ],
            [
                "path" => "/admin/editGroup",
                "component" => "views/admin/editGroup.html",
                "name" => "更改权限组"
            ],
            [
                "path" => "/menu/index",
                "component" => "views/menu/index.html",
                "name" => "菜单"
            ],
            [
                "path" => "/menu/add",
                "component" => "views/menu/add.html",
                "name" => "添加菜单"
            ],
            [
                "path" => "/menu/edit",
                "component" => "views/menu/edit.html",
                "name" => "修改菜单"
            ],
            [
                "path" => "/menu/del",
                "component" => "views/menu/del.html",
                "name" => "删除菜单"
            ],
            [
                "path" => "/recycling/index",
                "component" => "views/recycling/index.html",
                "name" => "回收站"
            ],
            [
                "path" => "/system/system",
                "component" => "views/system/system.html",
                "name" => "系统配置"
            ],
            [
                "path" => "/system/addSystem",
                "component" => "views/system/addSystem.html",
                "name" => "新增系统配置"
            ],
            [
                "path" => "/system/rule",
                "component" => "views/system/rule.html",
                "name" => "权限节点"
            ],
            [
                "path" => "/system/addRule",
                "component" => "views/system/addRule.html",
                "name" => "新增权限节点"
            ],
            [
                "path" => "/system/file",
                "component" => "views/system/file.html",
                "name" => "文件存储"
            ],
            [
                "path" => "/portal/menu",
                "component" => "views/portal/menu.html",
                "name" => "门户菜单"
            ],
            [
                "path" => "/portal/addMenu",
                "component" => "views/portal/addMenu.html",
                "name" => "新增菜单"
            ],
            [
                "path" => "/portal/addMenuCate",
                "component" => "views/portal/addMenuCate.html",
                "name" => "新增菜单组"
            ],
            [
                "path" => "/portal/carousel",
                "component" => "views/portal/carousel.html",
                "name" => "轮播"
            ],
            [
                "path" => "/portal/addCarousel",
                "component" => "views/portal/addCarousel.html",
                "name" => "新增轮播"
            ],
            [
                "path" => "/portal/addCarouselCate",
                "component" => "views/portal/addCarouselCate.html",
                "name" => "新增轮播组"
            ],
            [
                "path" => "/portal/contentCate",
                "component" => "views/portal/contentCate.html",
                "name" => "分类管理"
            ],
            [
                "path" => "/portal/addContentCate",
                "component" => "views/portal/addContentCate.html",
                "name" => "新增分类"
            ],
            [
                "path" => "/portal/addContentCateGroup",
                "component" => "views/portal/addContentCateGroup.html",
                "name" => "新增分类组"
            ],
            [
                "path" => "/portal/content",
                "component" => "views/portal/content.html",
                "name" => "文章/商品"
            ],
            [
                "path" => "/portal/addContent",
                "component" => "views/portal/addContent.html",
                "name" => "新增文章/商品"
            ],
            [
                "path" => "/portal/editContent",
                "component" => "views/portal/editContent.html",
                "name" => "修改文章/商品"
            ],
            [
                "path" => "/user/index",
                "component" => "views/user/index.html",
                "name" => "前台用户"
            ],
            [
                "path" => "/user/message",
                "component" => "views/user/message.html",
                "name" => "评论/留言"
            ],
            [
                "path" => "/admin/profile",
                "component" => "views/admin/profile.html",
                "name" => "个人中心"
            ],
            [
                "path" => "/project/myProject",
                "component" => "views/project/myProject.html",
                "name" => "我的项目"
            ],
            [
                "path" => "/project/index",
                "component" => "views/project/index.html",
                "name" => "全部项目"
            ],
            [
                "path" => "/personnel/index",
                "component" => "views/personnel/index.html",
                "name" => "人员档案"
            ],
            [
                "path" => "/afiles/index",
                "component" => "views/afiles/index.html",
                "name" => "档案文件"
            ],
            [
                "path" => "/device/index",
                "component" => "views/device/index.html",
                "name" => "设备信息"
            ],
            [
                "path" => "/legal/index",
                "component" => "views/legal/index.html",
                "name" => "法律文献"
            ],
            [
                "path" => "/portal/novel",
                "component" => "views/portal/novel.html",
                "name" => "书籍管理"
            ],
            [
                "path" => "/portal/chapter",
                "component" => "views/portal/chapter.html",
                "name" => "书籍章节管理"
            ],
        ];
        // return ['data' => $routes_arr];
        return $this->show(1,'ok',$routes_arr);
    }

    public function menu()
    {
        $admin = \think\loader::model('common/admin')->where(['id' => $this->admin_id])->find();
        if ($admin['is_admin']) 
        {
            $menu = $this->menulist(\think\loader::model('common/adminMenu')->where(['status' => 1])->order('sort asc')->select());

            return $this->show(1,'ok',$menu);
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

        return $this->show(1,'ok',$menu);
    }

    /**
     * 选中当前菜单
     */
    public function currentMenuId()
    {
        $id = request()->post('data');
        $menu = \think\loader::model('common/adminMenu')->where(['id' => $id])->find();
        if (!$menu['pid']) 
        {
            //判断当前菜单是否含有下级菜单，如果没有则直接选中当前菜单即可
            $child_menu = \think\loader::model('common/adminMenu')->where(['pid' => $id])->find();
            if (!$child_menu) 
            {
                session('menu_parent_id', $id);
                session('menu_child_id', null);
            }
            session('menu_parent_id_bak', $id);
        }
        else
        {
            session('menu_parent_id', session('menu_parent_id_bak'));
            session('menu_child_id', $id);
        }
        exit;
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


    public function admin()
    {
        $admin = Cache::get("admin".$this->admin_id);
        return $this->success('请求成功','',['nickname'=>$admin['nickname']]);
    }

    public function curve()
    {
        /**
         * 计算当天流量走势
         */

        //取当天凌晨时间戳
        $today = strtotime(date('Y-m-d'));
        //找出今天的访问记录
        $log = \think\Db::name('user_log')->where(['create_time'=>['>=',$today]])->select();
        //取当前小时数，加1实现实时
        $h = date('H') < 24 ? date('H')+1 : date('H');
        $data = [];
        for ($i=0; $i <= $h; $i++) { 
            //取0-$h小时之间的整点时间戳
            $time = strtotime(date("Y-m-d {$i}:0:0"));
            $next_time = $time + 3600;
            $data['time'][] = $i < 10 ? '0'.$i.':00' :$i.':00';
            $uv = 0;
            $pv = 0;
            if(!empty($log)) {
                foreach ($log as $k => $v) {
                    if($v['create_time'] >= $time and $v['create_time'] < $next_time) {
                        $uv++;
                        $pv = $pv+$v['num'];
                    }
                }
            }
            $data['uv'][] = $uv;
            $data['pv'][] = $pv;
        }
        return $this->show(1,'ok',$data);
    }

    public function groups()
    {
        $group = \think\loader::model('common/adminGroup')->where(['status'=>1])->select();

        return $this->show(1,'ok',$group);
    }

    public function ruleList()
    {   
        $admin_info = Cache::get("admin".$this->admin_id);
        $rules = \think\loader::model('common/adminRule')->where(['type'=>1])->select();

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
                // var_dump($v);exit;
                // $rule = \think\loader::model('common/adminRule')->where(['type'=>1, 'mid' => $v['children']['id']])->select();
                // if ($rule) 
                // {
                //     $menu[$k]['children']['rule'] = $rule;
                // }
            }
        }

        // var_dump($menu);exit;
        // var_dump(json_decode(json_encode($menu), true));
        return $this->show(1,'ok',$menu);
    }

    public function indexMenuCate()
    {
        $data = \think\loader::model('common/indexMenuCate')->select();

        return $this->show(1,'ok',$data);
    }

    public function indexMenu()
    {
        if($this->request->param('cate_id')){
            $menu = \think\Db::name('index_menu')->where(['index_menu_cate_id'=>$this->request->param('cate_id')])->order('create_time asc')->select();
            $menuModel = new \app\common\model\IndexMenu();
            $menu = $menuModel->menuList($menu);

            return $this->show(1,'ok',$menu);
        }
    }

    public function carouselCate()
    {
        $data = \think\loader::model('common/carouselCate')->select();

        return $this->show(1,'ok',$data);
    }

    public function uploads()
    {
        $param = $this->request->param();
        $module = isset($param['module']) ? $param['module'] : 'admin';
        $use = isset($param['use']) ? $param['use'] : 'books';
        $res = $this->upload($module,$use);
        if($res['code'] == 1){
            return $this->show(1,'',$res['data']);
        }
        return $this->show(0,$res['msg']);
    }

    public function contentCateGroup()
    {
        $data = \think\loader::model('common/contentCateGroup')->select();

        return $this->show(1,'ok',$data);
    }

    public function contentCate()
    {
        if($this->request->param('group_id')){
            $menu = \think\Db::name('content_cate')->where(['content_cate_group_id'=>$this->request->param('group_id')])->order('create_time asc')->select();
            $menuModel = new \app\common\model\IndexMenu();
            $menu = $menuModel->menuList($menu);

            return $this->show(1,'ok',$menu);
        }
    }

    public function contentDesigner()
    {
        $designer = \think\loader::model('common/content')->field('id,title')->where(['content_cate_id' => 6])->select();
        return $this->show(1, 'ok', $designer);
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
}
