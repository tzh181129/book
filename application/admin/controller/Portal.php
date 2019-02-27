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

class Portal extends Authcheck
{
    public function menu()
    {
        $cate_id = $this->request->has('index_menu_cate_id') ? $this->request->param('index_menu_cate_id') : Db::name('index_menu_cate')->where('is_main', 1)->value('id');
        $data = \think\Db::name('index_menu')->where(['index_menu_cate_id' => $cate_id])->order('create_time asc')->select();
        $menuModel = new \app\common\model\IndexMenu();
        $data['data'] = $menuModel->menuList($data);
        return $this->showList($data);
    }

    public function editMenu()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $validate = new \think\Validate([
                'id' => 'require',
                'field' => 'require|length:1,50',
                'value' => 'require|length:1,500',
            ]);
            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }
            if (empty(Db::name('index_menu')->where(['id' => $post['id']])->find())) {
                return $this->error('非法请求');
            }
            if ($post['field'] === 'index_menu_cate_id') {
                if (empty(Db::name('index_menu_cate')->where(['id' => $post['value']])->find())) {
                    return $this->error('分组不存在');
                }
            }

            $data[$post['field']] = $post['value'];
            if (false == \think\loader::model('common/indexMenu')->allowField(true)->save($data, ['id' => $post['id']])) {
                return $this->error('操作失败');
            }
            return $this->success('操作成功');
        } else {
            return $this->error('非法请求');
        }
    }

    public function addMenu()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $validate = new \think\Validate([
                'name' => 'require|length:1,50',
                'index_menu_cate_id' => 'require',
                'is_target' => 'require',
            ]);
            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }

            $post['status'] = 1;

            if (false == \think\loader::model('common/indexMenu')->allowField(true)->save($post)) {
                return $this->error('操作失败');
            }
            return $this->success('操作成功');
        } else {
            return $this->error('非法请求');
        }
    }

    public function delMenu()
    {
        $param = $this->request->param();
        $array = [];
        if (!is_array($param['id'])) {
            $array[] = $param['id'];
        } else {
            $array = $param['id'];
        }
        $ids = Db::name('index_menu')->where(['pid' => ['IN', $array]])->column('id');
        if (!empty($ids)) {
            foreach ($ids as $v) {
                if (!in_array($v, $array)) {
                    return $this->error('要删除的菜单中有包含子菜单的存在');
                }
            }
        }
        if (false == $this->delete(['model' => 'common/indexMenu', 'key' => $array])) {
            return $this->error('操作失败');
        }
        return $this->success('操作成功');
    }

    public function menuCate()
    {
        $limit = $this->request->has('limit') ? $this->request->param('limit') : 20;
        $data = \think\loader::model('common/indexMenuCate')->order('create_time asc')->paginate($limit, false, ['query' => $this->request->param()]);

        return $this->showList($data);
    }

    public function editMenuCate()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $validate = new \think\Validate([
                'id' => 'require',
                'field' => 'require|length:1,50',
                'value' => 'require|length:1,500',
            ]);
            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }
            if (empty(Db::name('index_menu_cate')->where(['id' => $post['id']])->find())) {
                return $this->error('非法请求');
            }
            if ($post['field'] === 'key') {
                if (!empty(Db::name('index_menu_cate')->where(['key' => $post['value']])->find())) {
                    return $this->error('key已存在');
                }
            }
            if ($post['field'] === 'is_main') {
                Db::name('index_menu_cate')->where(['is_main' => 1])->update(['is_main' => 0]);
            }

            $data[$post['field']] = $post['value'];
            if (false == \think\loader::model('common/indexMenuCate')->allowField(true)->save($data, ['id' => $post['id']])) {
                return $this->error('操作失败');
            }
            return $this->success('操作成功');
        } else {
            return $this->error('非法请求');
        }
    }

    public function addMenuCate()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $validate = new \think\Validate([
                'name' => 'require|length:1,50',
                'key' => 'require|length:1,50',
            ]);
            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }
            if (!empty(Db::name('index_menu_cate')->where(['key' => $post['key']])->find())) {
                return $this->error('key已存在');
            }
            if ($post['is_main'] == 1) {
                Db::name('index_menu_cate')->where(['is_main' => 1])->update(['is_main' => 0]);
            } else {
                $post['is_main'] = 0;
            }

            if (false == \think\loader::model('common/indexMenuCate')->allowField(true)->save($post)) {
                return $this->error('操作失败');
            }
            return $this->success('操作成功');
        } else {
            return $this->error('非法请求');
        }
    }

    public function delMenuCate()
    {
        $param = $this->request->param();
        $array = [];
        if (!is_array($param['id'])) {
            $array[] = $param['id'];
        } else {
            $array = $param['id'];
        }
        $menu_id = Db::name('index_menu')->where(['index_menu_cate_id' => ['IN', $array]])->column('id');
        // 启动事务
        Db::startTrans();
        try {
            $this->delete(['model' => 'common/indexMenuCate', 'key' => $array]);
            $this->delete(['model' => 'common/indexMenu', 'key' => $menu_id]);
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return $this->error('操作失败');
        }
        return $this->success('操作成功');
    }

    public function carousel()
    {
        $limit = $this->request->has('limit') ? $this->request->param('limit') : 20;
        $cate_id = $this->request->has('carousel_cate_id') ? $this->request->param('carousel_cate_id') : Db::name('carousel_cate')->where('is_main', 1)->value('id');
        $data = \think\loader::model('common/carousel')->where(['carousel_cate_id' => $cate_id])->order('order asc')->paginate($limit, false, ['query' => $this->request->param()]);

        return $this->showList($data);
    }

    public function editCarousel()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $validate = new \think\Validate([
                'id' => 'require',
                'field' => 'require|length:1,50',
                'value' => 'require|length:1,500',
            ]);
            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }
            if (empty(Db::name('carousel')->where(['id' => $post['id']])->find())) {
                return $this->error('非法请求');
            }
            if ($post['field'] === 'carousel_cate_id') {
                if (empty(Db::name('carousel_cate')->where(['id' => $post['value']])->find())) {
                    return $this->error('分组不存在');
                }
            }

            $data[$post['field']] = $post['value'];
            if (false == \think\loader::model('common/carousel')->allowField(true)->save($data, ['id' => $post['id']])) {
                return $this->error('操作失败');
            }
            return $this->success('操作成功');
        } else {
            return $this->error('非法请求');
        }
    }

    public function addCarousel()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $validate = new \think\Validate([
                'title' => 'require|length:1,250',
                'carousel_cate_id' => 'require',
                'is_target' => 'require',
                'img' => 'require',
            ]);
            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }

            $post['status'] = 1;

            if (false == \think\loader::model('common/carousel')->allowField(true)->save($post)) {
                return $this->error('操作失败');
            }
            return $this->success('操作成功');
        } else {
            return $this->error('非法请求');
        }
    }

    public function delCarousel()
    {
        $param = $this->request->param();
        $array = [];
        if (!is_array($param['id'])) {
            $array[] = $param['id'];
        } else {
            $array = $param['id'];
        }
        if (false == $this->delete(['model' => 'common/carousel', 'key' => $array])) {
            return $this->error('操作失败');
        }
        return $this->success('操作成功');
    }

    public function carouselCate()
    {
        $limit = $this->request->has('limit') ? $this->request->param('limit') : 20;
        $data = \think\loader::model('common/carouselCate')->order('create_time asc')->paginate($limit, false, ['query' => $this->request->param()]);

        return $this->showList($data);
    }

    public function editCarouselCate()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $validate = new \think\Validate([
                'id' => 'require',
                'field' => 'require|length:1,50',
                'value' => 'require|length:1,500',
            ]);
            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }
            if (empty(Db::name('carousel_cate')->where(['id' => $post['id']])->find())) {
                return $this->error('非法请求');
            }
            if ($post['field'] === 'key') {
                if (!empty(Db::name('carousel_cate')->where(['key' => $post['value']])->find())) {
                    return $this->error('key已存在');
                }
            }
            if ($post['field'] === 'is_main') {
                Db::name('carousel_cate')->where(['is_main' => 1])->update(['is_main' => 0]);
            }

            $data[$post['field']] = $post['value'];
            if (false == \think\loader::model('common/carouselCate')->allowField(true)->save($data, ['id' => $post['id']])) {
                return $this->error('操作失败');
            }
            return $this->success('操作成功');
        } else {
            return $this->error('非法请求');
        }
    }

    public function addCarouselCate()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $validate = new \think\Validate([
                'name' => 'require|length:1,50',
                'key' => 'require|length:1,50',
            ]);
            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }
            if (!empty(Db::name('carousel_cate')->where(['key' => $post['key']])->find())) {
                return $this->error('key已存在');
            }
            if ($post['is_main'] == 1) {
                Db::name('carousel_cate')->where(['is_main' => 1])->update(['is_main' => 0]);
            } else {
                $post['is_main'] = 0;
            }

            if (false == \think\loader::model('common/carouselCate')->allowField(true)->save($post)) {
                return $this->error('操作失败');
            }
            return $this->success('操作成功');
        } else {
            return $this->error('非法请求');
        }
    }

    public function delCarouselCate()
    {
        $param = $this->request->param();
        $array = [];
        if (!is_array($param['id'])) {
            $array[] = $param['id'];
        } else {
            $array = $param['id'];
        }
        $carousel_id = Db::name('carousel')->where(['carousel_cate_id' => ['IN', $array]])->column('id');
        // 启动事务
        Db::startTrans();
        try {
            $this->delete(['model' => 'common/carouselCate', 'key' => $array]);
            $this->delete(['model' => 'common/carousel', 'key' => $carousel_id]);
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return $this->error('操作失败');
        }
        return $this->success('操作成功');
    }

    public function contentCate()
    {
        $cate_id = $this->request->has('content_cate_group_id') ? $this->request->param('content_cate_group_id') : Db::name('content_cate_group')->where('is_main', 1)->value('id');
        $data = \think\Db::name('content_cate')->where(['content_cate_group_id' => $cate_id])->order('create_time asc')->select();
        $menuModel = new \app\common\model\IndexMenu();
        $data['data'] = $menuModel->menuList($data);
        return $this->showList($data);
    }

    public function editContentCate()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $validate = new \think\Validate([
                'id' => 'require',
                'field' => 'require|length:1,50',
                'value' => 'require|length:1,500',
            ]);
            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }
            if (empty(Db::name('content_cate')->where(['id' => $post['id']])->find())) {
                return $this->error('非法请求');
            }
            if ($post['field'] === 'content_cate_group_id') {
                if (empty(Db::name('content_cate')->where(['id' => $post['value']])->find())) {
                    return $this->error('分组不存在');
                }
            }

            $data[$post['field']] = $post['value'];
            if (false == \think\loader::model('common/contentCate')->allowField(true)->save($data, ['id' => $post['id']])) {
                return $this->error('操作失败');
            }
            return $this->success('操作成功');
        } else {
            return $this->error('非法请求');
        }
    }

    public function addContentCate()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $validate = new \think\Validate([
                'name' => 'require|length:1,50',
                'content_cate_group_id' => 'require',
            ]);
            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }

            $post['status'] = 1;

            if (false == \think\loader::model('common/contentCate')->allowField(true)->save($post)) {
                return $this->error('操作失败');
            }
            return $this->success('操作成功');
        } else {
            return $this->error('非法请求');
        }
    }

    public function delContentCate()
    {
        $param = $this->request->param();
        $array = [];
        if (!is_array($param['id'])) {
            $array[] = $param['id'];
        } else {
            $array = $param['id'];
        }
        $ids = Db::name('content_cate')->where(['pid' => ['IN', $array]])->column('id');
        if (!empty($ids)) {
            foreach ($ids as $v) {
                if (!in_array($v, $array)) {
                    return $this->error('要删除的分类中有包含子分类的存在');
                }
            }
        }
        if (!empty(Db::name('content')->where(['content_cate_id' => ['IN', $array]])->find())) {
            return $this->error('要删除的分类下还存在内容');
        }
        if (false == $this->delete(['model' => 'common/contentCate', 'key' => $array])) {
            return $this->error('操作失败');
        }
        return $this->success('操作成功');
    }

    public function contentCateGroup()
    {
        $limit = $this->request->has('limit') ? $this->request->param('limit') : 20;
        $data = \think\loader::model('common/contentCateGroup')->order('create_time asc')->paginate($limit, false, ['query' => $this->request->param()]);

        return $this->showList($data);
    }

    public function editContentCateGroup()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $validate = new \think\Validate([
                'id' => 'require',
                'field' => 'require|length:1,50',
                'value' => 'require|length:1,500',
            ]);
            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }
            if (empty(Db::name('content_cate_group')->where(['id' => $post['id']])->find())) {
                return $this->error('非法请求');
            }
            if ($post['field'] === 'key') {
                if (!empty(Db::name('content_cate_group')->where(['key' => $post['value']])->find())) {
                    return $this->error('key已存在');
                }
            }
            if ($post['field'] === 'is_main') {
                Db::name('content_cate_group')->where(['is_main' => 1])->update(['is_main' => 0]);
            }

            $data[$post['field']] = $post['value'];
            if (false == \think\loader::model('common/contentCateGroup')->allowField(true)->save($data, ['id' => $post['id']])) {
                return $this->error('操作失败');
            }
            return $this->success('操作成功');
        } else {
            return $this->error('非法请求');
        }
    }

    public function addContentCateGroup()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $validate = new \think\Validate([
                'name' => 'require|length:1,50',
                'key' => 'require|length:1,50',
            ]);
            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }
            if (!empty(Db::name('content_cate_group')->where(['key' => $post['key']])->find())) {
                return $this->error('key已存在');
            }
            if ($post['is_main'] == 1) {
                Db::name('content_cate_group')->where(['is_main' => 1])->update(['is_main' => 0]);
            } else {
                $post['is_main'] = 0;
            }

            if (false == \think\loader::model('common/contentCateGroup')->allowField(true)->save($post)) {
                return $this->error('操作失败');
            }
            return $this->success('操作成功');
        } else {
            return $this->error('非法请求');
        }
    }

    public function delContentCateGroup()
    {
        $param = $this->request->param();
        $array = [];
        if (!is_array($param['id'])) {
            $array[] = $param['id'];
        } else {
            $array = $param['id'];
        }
        $menu_id = Db::name('content_cate')->where(['content_cate_group_id' => ['IN', $array]])->column('id');
        if (!empty($menu_id)) {
            return $this->error('该分组下还存在分类，不准删除');
        }
        // 启动事务
        Db::startTrans();
        try {
            $this->delete(['model' => 'common/contentCateGroup', 'key' => $array]);
            //$this->delete(['model'=>'common/contentCate','key'=>$menu_id]);
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return $this->error('操作失败');
        }
        return $this->success('操作成功');
    }

    public function content()
    {
        $limit = $this->request->has('limit') ? $this->request->param('limit') : 20;
        $group_id = $this->request->has('group_id') ? $this->request->param('group_id') : Db::name('content_cate_group')->where('is_main', 1)->value('id');
        $cate_id = $this->request->has('cate_id') ? $this->request->param('cate_id') : Db::name('content_cate')->where(['content_cate_group_id' => ['IN', $group_id]])->column('id');
        $cate = Db::name('content_cate')->where(['content_cate_group_id' => ['IN', $group_id]])->select();
        $data = \think\loader::model('common/content')->withCount('message')->where(['content_cate_id' => ['IN', $cate_id], 'status' => ['>', 0]])->order('create_time desc')->paginate($limit, false, ['query' => $this->request->param()]);
        foreach ($data as $k => $vo) {
            $data[$k]['content_cate'] = Db::name('content_cate')->where(['id' => $vo['content_cate_id']])->value('name');
        }
        return $this->showList($data);
    }

    public function editContent()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $validate = new \think\Validate([
                'title' => 'require|length:1,250',
                'content_cate_id' => 'require',
                'status' => 'require',
                'img' => 'require',
                'content' => 'require',
            ]);
            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }

            // $post['img'] = implode(',',$post['img']);

            if (false == \think\loader::model('common/content')->allowField(true)->save($post, ['id' => $post['id']])) {
                return $this->error('操作失败');
            }
            return $this->success('操作成功');
        } else {
            $id = $this->request->param('id');
            $data['content'] = \think\loader::model('common/content')->where(['id' => $id])->find();
            if (empty($data['content'])) {
                return $this->show(0, '参数错误');
            }
            $data['content']['img'] = explode(',', $data['content']['img']);
            $data['group'] = Db::name('content_cate_group')->select();
            $data['this_group_id'] = Db::name('content_cate')->where('id', $data['content']['content_cate_id'])->value('content_cate_group_id');
            $cate = Db::name('content_cate')->where(['content_cate_group_id' => $data['this_group_id']])->select();
            $menuModel = new \app\common\model\IndexMenu();
            $data['cate'] = $menuModel->menuList($cate);
            return $this->show(1, '', $data);

        }
    }

    public function addContent()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $validate = new \think\Validate([
                'title' => 'require|length:1,250|unique:content',
                'content_cate_id' => 'require',
                'status' => 'require',
                // 'img' => 'require',
                'content' => 'require',
            ]);
            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }

            if (isset($post['img'])) {
                // $post['img'] = implode(',',$post['img']);
            } else {
                $post['img'] = ' ';
            }

            $post['create_time'] = time();
            $post['update_time'] = time();

            if (false == \think\loader::model('common/content')->allowField(true)->save($post)) {
                return $this->error('操作失败');
            }
            return $this->success('操作成功');
        } else {
            return $this->error('非法请求');
        }
    }

    public function delContent()
    {
        $param = $this->request->param();
        $array = [];
        if (!is_array($param['id'])) {
            $array[] = $param['id'];
        } else {
            $array = $param['id'];
        }
        if (false == $this->soft(['model' => 'common/content', 'index' => $array])) {
            return $this->error('操作失败');
        }
        return $this->success('操作成功');
    }

    //书籍分类
    public function novelCate(){
        $limit = $this->request->has('limit') ? $this->request->param('limit') : 20;
        $data = \think\loader::model('common/novel_cate')->where(['status' => '1'])->order('create_time desc')->paginate($limit, false, ['query' => $this->request->param()]);
        foreach($data as $k=>$vo){
            $name= \think\loader::model('common/novel_cate')->where(['status'=>1,'id'=>$vo['pid']])->value('title');
            if($name){
                $data[$k]['name']=$name;
            }else{
                $data[$k]['name']='';
            }
        }
    }

    public function addNovelcate()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $validate = new \think\Validate([
                'title' => 'require',
                'pid'    =>'require',
            ]);
            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }

            $post['create_time'] = time();

            if (false == \think\loader::model('common/novel_cate')->allowField(true)->save($post)) {
                return $this->error('操作失败');
            }
            return $this->success('操作成功');
        } else {

            return $this->show(1, '');
        }
    }



    public function novel()
    {
        $limit = $this->request->has('limit') ? $this->request->param('limit') : 20;
        $data = \think\loader::model('common/novel')->where(['status' => '1'])->order('create_time desc')->paginate($limit, false, ['query' => $this->request->param()]);
        foreach ($data as $k => $vo) {
            $data[$k]['img'] = '<img src=' . $vo['img'] . ' width=200px;height=100px; />';
        }
        return $this->showList($data);
    }

    public function addNovel()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $validate = new \think\Validate([
                'title' => 'require',
                'author' => 'require',
                'description' => 'require',
            ]);
            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }

            $post['create_time'] = time();

            if (false == \think\loader::model('common/novel')->allowField(true)->save($post)) {
                return $this->error('操作失败');
            }
            return $this->success('操作成功');
        } else {

            return $this->show(1, '');
        }
    }

    public function editNovel()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();

            $validate = new \think\Validate([
                'id' => 'require',
                'title' => 'require',
            ]);
            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }
            // 启动事务
            Db::startTrans();
            try {
                \think\loader::model('common/novel')->allowField(true)->save($post, ['id' => $post['id']]);
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
            $data = \think\loader::model('common/novel')->where(['id' => $id])->find();
            $this->success('', '', $data);
        }
    }

    public function delNovel()
    {
        $param = $this->request->param();
        $array = [];
        if (!is_array($param['id'])) {
            $array[] = $param['id'];
        } else {
            $array = $param['id'];
        }
        if (false == $this->delete(['model' => 'common/novel', 'key' => $array])) {
            return $this->error('操作失败');
        }
        return $this->success('操作成功');
    }

    public function chapter()
    {
        $param = $this->request->param();
        $novel_id = $param['novel_id'];
        $limit = $this->request->has('limit') ? $this->request->param('limit') : 20;
        $data = \think\loader::model('common/chapter')->where(['novel_id' => $novel_id])->order('id asc')->paginate($limit, false, ['query' => $this->request->param()]);
        foreach ($data as $k => $vo) {
            $data[$k]['name'] = \think\loader::model('common/novel')->where(['id' => $novel_id])->value('title');
        }
        return $this->showList($data);
    }

    public function addChapter()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $validate = new \think\Validate([
                'title' => 'require',
                'content' => 'require',
            ]);
            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }

            $post['create_time'] = time();

            if (false == \think\loader::model('common/chapter')->allowField(true)->save($post)) {
                return $this->error('操作失败');
            }
            return $this->success('操作成功');
        } else {

            return $this->show(1, '');
        }
    }

    //批量导入章节
    public function reportChapter()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();

            $validate = new \think\Validate([
                'file_path' => 'require',
            ]);

            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }

            $post['create_time'] = time();
            chapter_insert($post['file_path'], $post);

            return $this->success('导入成功');
        } else {
            return $this->show(1, '');
        }

    }
    public function uploadChapter(){
        $result = $this->uploadFile('admin', 'novel');
        //添加文本文件
        $insert['name'] = $result['name'];
        $result['path'] = str_replace("\\", "/", $result['data']);

        return $this->show(1, '上传成功', $result);
    }

    public function uploadFile($module, $use)
    {
        if (request()->file('file')) {
            $file = request()->file('file');
        } else {
            $res['code'] = 0;
            $res['msg'] = '没有上传文件';
            return $res;
        }
        $file_info = $file->getInfo();

        $info = $file->move(ROOT_PATH . 'public' . DS . 'novel' . DS . $module . DS . $use);

        if ($info) {
            $res['code'] = 1;
            $res['name'] = $file_info['name'];
            $res['data'] = DS . 'novel' . DS . $module . DS . $use . DS . $info->getSaveName();
            return $res;
        } else {
            // 上传失败获取错误信息
            return $this->error('上传失败：' . $file->getError());
        }
    }

    public function editChapter()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();

            $validate = new \think\Validate([
                'id' => 'require',
                'title' => 'require',
            ]);
            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }
            // 启动事务
            Db::startTrans();
            try {
                \think\loader::model('common/chapter')->allowField(true)->save($post, ['id' => $post['id']]);
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
            $data = \think\loader::model('common/chapter')->where(['id' => $id])->find();
            $this->success('', '', $data);
        }
    }

    public function delChapter()
    {
        $param = $this->request->param();
        $array = [];
        if (!is_array($param['id'])) {
            $array[] = $param['id'];
        } else {
            $array = $param['id'];
        }
        if (false == $this->delete(['model' => 'common/chapter', 'key' => $array])) {
            return $this->error('操作失败');
        }
        return $this->success('操作成功');
    }

    public function edit()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $validate = new \think\Validate([
                'id' => 'require',
                'field' => 'require|length:1,50',
                'value' => 'require|length:1,500',
            ]);
            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }
            if (empty(Db::name('content')->where(['id' => $post['id']])->find())) {
                return $this->error('非法请求');
            }
            if ($post['field'] === 'key') {
                if (!empty(Db::name('content')->where(['key' => $post['value']])->find())) {
                    return $this->error('key已存在');
                }
            }

            $data[$post['field']] = $post['value'];
            if (false == \think\loader::model('content')->allowField(true)->save($data, ['id' => $post['id']])) {
                return $this->error('操作失败');
            }
            return $this->success('操作成功');
        } else {
            return $this->error('非法请求');
        }
    }


}
