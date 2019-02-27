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

class Menu extends Base
{
    public function index()
    {
        // $menu['data'] = $this->menulist(\think\loader::model('common/adminMenu')->where(['status'=>1])->order('orders asc')->select());

        // $limit = $this->request->has('limit') ? $this->request->param('limit') : 20;
        // $menu  = \think\loader::model('common/adminMenu')->where(['status'=>1])->order('sort asc')
        //         ->paginate($limit, false, ['query'=>$this->request->param()])
        //         ->each(function($item, $key){
        //             // $item->sort = $item->sort;
        //         });

        $menu  = \think\loader::model('common/adminMenu')->where(['status'=>1])->order('sort asc')->select();

        foreach ($menu as $k => $v) 
        {
            $menu[$k]['icons'] = '<i class="layui-icon ' . $v['icon'] . '"></i>';
        }
        
        $menuModel = new \app\common\model\AdminMenu();
        // $menu['data'] = $menuModel->menuList($menu['data']);
        $menu['data'] = $menuModel->menuList($menu);

        return $this->showList($menu);
    }

    public function add()
    {
        if($this->request->isPost()) {
            $post = $this->request->post();
            $data = $post['data'];
            // $validate = new \think\Validate([
            //     'name' => 'require|length:1,50',
            //     'index_menu_cate_id' => 'require',
            //     'is_target' => 'require',
            // ]);
            // if (!$validate->check($post)) {
            //     return $this->error($validate->getError());
            // }

            $data['status'] = 1;
            
            if(false == \think\loader::model('common/adminMenu')->allowField(true)->save($data)) {
                return $this->error('操作失败');
            }
            return $this->success('操作成功');
        } else {
            return $this->error('非法请求');
        }
    }

    public function edit()
    {
        if($post = $this->request->post()) 
        {
            // $post = $this->request->post();
            // $validate = new \think\Validate([
            //     'id'  => 'require',
            //     'field' => 'require|length:1,50',
            //     'value' => 'require|length:1,500',
            // ]);
            // if (!$validate->check($post)) {
            //     return $this->error($validate->getError());
            // }
            // if(empty(Db::name('index_menu')->where(['id'=>$post['id']])->find())) {
            //     return $this->error('非法请求');
            // }
            // if($post['field'] === 'index_menu_cate_id') 
            // {
            //     if(empty(Db::name('index_menu_cate')->where(['id'=>$post['value']])->find())) {
            //         return $this->error('分组不存在');
            //     }
            // }

            // $data[$post['field']] = $post['value'];
            // var_dump($post);exit;
            $data = $post['data'];
            if(false == \think\loader::model('common/adminMenu')->allowField(true)->save($data,['id'=>$data['id']])) 
            {
                return $this->error('操作失败');
            }
            return $this->success('操作成功');
        } 
        else 
        {
            $id = $this->request->param('id');
            $data = \think\loader::model('common/adminMenu')->where(['id'=>$id])->find();
            if(empty($data)){
                return $this->show(0,'参数错误');
            }
            return $this->show(1,'',$data);
            // return $this->error('非法请求');
        }
    }

    public function del()
    {
        $param = $this->request->param();
        $array = [];
        if(!is_array($param['id'])) 
        {
            $array[] = $param['id'];
        } 
        else 
        {
            $array = $param['id'];
        }
        $ids = Db::name('admin_menu')->where(['pid'=>['IN',$array]])->column('id');
        if(!empty($ids)) 
        {
            foreach ($ids as $v) 
            {
                if(!in_array($v, $array))
                {
                    return $this->error('要删除的菜单中有包含子菜单的存在');
                }
            }
        }
        if(false == $this->delete(['model'=>'common/adminMenu','key'=>$array])) 
        {
            return $this->error('操作失败');
        }
        return $this->success('操作成功');
    }

    public function parentMenu()
    {
        $data = \think\loader::model('common/adminMenu')->where(['pid' => 0])->order('sort asc')->select();

        return $this->show(1,'ok',$data);
    }

    public function childMenu()
    {
        $pid = $this->request->param('pid');
        $data = \think\loader::model('common/adminMenu')->where(['pid' => $pid])->order('sort asc')->select();

        return $this->show(1,'ok',$data);
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
    {   $admin_info=Cache::get("admin".$this->admin_id);
        $rule = \think\loader::model('common/adminRule')->where(['type'=>1])->select();
        
        return $this->show(1,'ok',$rule);
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
        $use = isset($param['use']) ? $param['use'] : 'thumb';
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
}
