<?php
/**
 * Created by PhpStorm.
 * User: 罗阁科技
 * Date: 2018/11/16
 * Time: 18:19
 */

namespace app\admin\controller;

use app\tplay\lib\Base;
use think\Cookie;
use think\Db;
use think\db\Query;
use think\helper\Arr;
use think\Request;
use app\tplay\lib\Authcheck;

class Product extends Authcheck
{
    public function sellerList()
    {
        $limit = $this->request->has('limit') ? $this->request->param('limit') : 20;
        $where = [];
        $data = $this->request->param();
        if (isset($data['key']))
        {
            if ($data['key']['time'] != '')
            {
                $time = $data['key']['time'];
                $firsttime = substr($time, 0, 10);
                $lasttime = substr($time, 12);
                $where['create_time'] = array('between', array(strtotime($firsttime), strtotime($lasttime)));
            }
            if ($data['key']['name'] != '')
            {
                $where['name']= ['like', "%" .$data['key']['name']. "%"];
            }
        }

        if (isset($_GET['id'])) {
            if ($_GET['id'] != '' && $_GET['id'] != "undefined") {
                $where['id'] = $_GET['id'];
            }
        }
        $data = \think\loader::model('common/seller')->where($where)
            ->order('create_time desc')
            ->paginate($limit, false, ['query' => $this->request->param()])
            ->each(function ($item, $key) {
                $item->headimg = '<img class="seeimg" src="'.$this->domain.Db::name('image')->where(['id' => $item->headimg_id])->value('path').'"/>';
            });
        foreach ($data as $k => $v) {
            if ($v['type'] == 1) {
                $data[$k]['types'] = '个人店铺';
            } else {
                $data[$k]['types'] = '商家店铺';
            }
        }

        return $this->showList($data);


    }

    /**
     * 新增商家
     */
    public function addSeller()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();

            $validate = new \think\Validate([
                'name' => 'require',
                'headimg_id' => 'require',
                'url' => 'require',
            ]);
            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }
            $post['create_time']=time();

            // 启动事务
            Db::startTrans();
            try {
                $post['create_time'] = time();
                \think\loader::model('common/seller')->allowField(true)->save($post);
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                return $this->error('操作失败');
            }
            return $this->success('操作成功');
        } else {
            // $unit = Db::name('unit')->field('id,title')->where(['status' => 1])->select();
            // $result['data'] = $unit;
            return $this->show(1, 'ok', '');
        }
    }

    public function editSeller()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();

            $validate = new \think\Validate([
                'id' => 'require',
                'name' => 'require',
            ]);
            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }
            // $name = $post['name'];
            // $url = $post['url'];
            // $status = $post['status'];
            // $desc = $post['desc'];

            // 启动事务
            Db::startTrans();
            try {
                \think\loader::model('common/seller')->allowField(true)->save($post,['id'=>$post['id']]);
                // \think\loader::model('common/seller')->where(['id' => $post['id']])->update(['name' => $name, 'url' => $url, 'status' => $status, 'desc' => $desc]);
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

            $seller = \think\loader::model('common/seller')->where(['id' => $id])->find();

            if (empty($seller)) {
                return $this->error('非法操作');
            }
            $seller['headimg'] = $this->domain.Db::name('image')->where(['id' => $seller['headimg_id']])->value('path');
            if ($seller['type'] == 1) {
                $seller['types'] = '个人店铺';
            } else {
                $seller['types'] = '商家店铺';
            }
            $this->success('', '', $seller);
        }
    }

    public function delSeller()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();

            $validate = new \think\Validate([
                'id' => 'require',
            ]);
            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }

            // 启动事务
            $id = $this->request->param('id');
            Db::startTrans();
            try {
                \think\loader::model('common/seller')->where(['id' => $id])->delete();
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                return $this->error('操作失败');
            }
            return $this->success('操作成功');
        } else {
            // $unit = Db::name('unit')->field('id,title')->where(['status' => 1])->select();
            // $result['data'] = $unit;
            return $this->show(1, 'ok', '');
        }
    }

    /**
     * 商品列表
     */
    public function goodsList()
    {
        $limit = $this->request->has('limit') ? $this->request->param('limit') : 20;
        $where = [];
        $data = $this->request->param();
        if (isset($data['key']))
        {
            if ($data['key']['time'] != '')
            {
                $time = $data['key']['time'];
                $firsttime = substr($time, 0, 10);
                $lasttime = substr($time, 12);
                $where['create_time'] = array('between', array(strtotime($firsttime), strtotime($lasttime)));
            }
            if ($data['key']['name'] != '')
            {
                $where['name']= ['like', "%" .$data['key']['name']. "%"];
            }

                if ($data['key']['seller_name'] != '')
                {
                    $seller_name= ['like', "%" .$data['key']['seller_name']. "%"];
                    $arr=\think\loader::model('common/seller')->where(['name'=>$seller_name])->find();
                    $where['seller_id']=$arr['id'];
                }

        }
        if (isset($_GET['time'])) {
            if ($_GET['time'] != '') {
                $time = $_GET['time'];
                $firsttime = substr($time, 0, 10);
                $lasttime = substr($time, 12);
                $where['create_time'] = array('between', array(strtotime($firsttime), strtotime($lasttime)));
            }
        }
        if (isset($_GET['id'])) {
            if ($_GET['id'] != '' && $_GET['id'] != "undefined") {
                $where['id'] = $_GET['id'];
            }
        }
        $data = \think\loader::model('common/product')->where($where)
            ->order('create_time desc')
            ->paginate($limit, false, ['query' => $this->request->param()])
            ->each(function ($item, $key) {
            });
        foreach ($data as $k => $v) {
            if ($v['status'] == 0) {
                $data[$k]['type'] = '使用';
            } else {
                $data[$k]['type'] = '禁用';
            }
            $time = time();
            if ($v['lower_time'] != '') {
                if ($time > $v['lower_time']) {
                    Db::name('product')->where(['id' => $v['id']])->update(['status' => 1, 'lower_time' => '']);
                    $data[$k]['seller_name'] = \think\loader::model('common/seller')->where(['id' => $v['seller_id']])->value('name');
                } else {
                    $data[$k]['seller_name'] = \think\loader::model('common/seller')->where(['id' => $v['seller_id']])->value('name');
                }
            } else {
                $data[$k]['seller_name'] = \think\loader::model('common/seller')->where(['id' => $v['seller_id']])->value('name');
            }
            $data[$k]['image'] = "<img class='seeimg' src='" . $this->domain . Db::name('image')->where(['id' => $v['cover_id']])->value('path') . "'/>";
        }
        return $this->showList($data);

    }

    /**
     * 新增商品
     */
    public function addGoods()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();

            $validate = new \think\Validate([
                'name' => 'require',
                'seller_id' => 'require',
                'price' => 'require',
                'stock' => 'require',
                'cover_id' => 'require',
                'helpoint'  =>'require',
            ]);
            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }


            $post['order_num'] = 0;
            $post['create_time'] = time();
            $post['status'] = 0;
            $post['lower_time'] = strtotime($post['lower_time']);
            // $post['cover_id'] = $post['image_id'];
            // $post['banner_id']=substr($post['banner'],1);
            $post['banner_id'] = $post['banner_one'] . ',' . $post['banner_two'] . ',' . $post['banner_three'];
            $post['banner_id'] = trim($post['banner_id'], ',');
            unset($post['banner_one'], $post['banner_two'], $post['banner_three']);

            // 启动事务
            Db::startTrans();
            try {
                \think\loader::model('common/product')->allowField(true)->save($post);
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                return $this->error('操作失败');
            }
            return $this->success('操作成功');
        } else {
            $seller = Db::name('seller')->field('id,name')->where(['status' => 1])->select();
            $result['data'] = $seller;
            return $this->show(1, 'ok', $result);
        }
    }

    public function editGoods()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();

            $validate = new \think\Validate([
                'id' => 'require',
            ]);
            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }

            $name = $post['name'];
            $price = $post['price'];
            $cover_id = $post['cover_id'];
            $stock = $post['stock'];
            $lower_time = strtotime($post['lower_time']);
            $url = $post['url'];
            // $status = $post['status'];
            $content = $post['content'];
            $helpoint = $post['helpoint'];
            $banner_id = trim($post['banner_one'] . ',' . $post['banner_two'] . ',' . $post['banner_three'], ',');
            // if($post['banner_status'] ==0) {
            //     $banner_id=substr($post['banner'],1);
            // }else{
            //     $banner= \think\loader::model('common/product')->where(['id' => $post['id']])->value('banner_id');
            //     $banner_id=$banner.$post['banner'];
            // }

            // 启动事务
            Db::startTrans();
            try {
                    \think\loader::model('common/product')->where(['id' => $post['id']])
                        ->update(['name' => $name, 'url' => $url, 'helpoint' => $helpoint, 'price' => $price,'cover_id'=>$cover_id,'banner_id'=>$banner_id, 'lower_time' => $lower_time, 'stock' => $stock, 'content' => $content]);
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

            $goods = \think\loader::model('common/product')->where(['id' => $id])->find();
            // $seller_id = \think\loader::model('common/product')->where(['id' => $id])->value('seller_id');
            // $data['seller_name'] = \think\loader::model('common/seller')->where(['id' => $seller_id])->value('name');
            $goods['cover']     = $this->domain.Db::name('image')->where(['id' => $goods['cover_id']])->value('path');
            $goods['banner_id'] = explode(',',$goods['banner_id']);

            $info = json_decode(json_encode($goods['banner_id'],true),true);
            foreach ($info as $k => $v) {
                if($v != '') {
                    $image[$k] = $this->domain . Db::name('image')->where(['id' => $v])->value('path');
                }
            }

            $goods['banner'] = $image;
            if ($goods['lower_time'] != '') {
                $goods['lower_time'] = date('Y-m-d', $goods['lower_time']);
            } else {
                $goods['lower_time'] = '';
            }
            if (empty($goods)) {
                return $this->error('非法操作');
            }
            $goods['seller'] = Db::name('seller')->field('id,name')->where(['status' => 1])->select();
            // var_dump(json_decode(json_encode($data), true));exit;
            //$course['image'] = Db::name('image')->where(['id' => $course['image_id']])->value('path');

            $this->success('', '', $goods);
        }
    }

    public function delGoods()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();

            $validate = new \think\Validate([
                'id' => 'require',
            ]);
            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }

            // 启动事务
            $id = $this->request->param('id');
            Db::startTrans();
            try {
                \think\loader::model('common/product')->where(['id' => $id])->delete();;
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                return $this->error('操作失败');
            }
            return $this->success('操作成功');
        } else {
            // $unit = Db::name('unit')->field('id,title')->where(['status' => 1])->select();
            // $result['data'] = $unit;
            return $this->show(1, 'ok', '');
        }

    }

    /**
     * 商品列表
     */
    public function productList()
    {
        $limit = $this->request->has('limit') ? $this->request->param('limit') : 20;
        $data = \think\loader::model('common/product')
            ->order('create_time desc')
            ->paginate($limit, false, ['query' => $this->request->param()])
            ->each(function ($item, $key) {
            });

        return $this->showList($data);
    }


    public function index()
    {
        /** @var Request $request */
        $request = $this->request;
        $query = new FeedbackModel();

        if ($request->has('status')) {
            $query->where('status', $request->param('status'));
        }
        $data = $query->paginate(15);

        return $this->showList($data);
    }

    public function status($ids, $status)
    {
        if (is_array($ids)) {
            Db::table('lg_feedback')->whereIn('id', $ids)->setField('status', $status);
        } else {
            FeedbackModel::where('id', $ids)->update(['status' => $status]);
        }

        return $this->success('操作成功');
    }

    public function deletes($ids)
    {
        $ids2 = [];
        foreach ($ids as $id) {
            $ids2[] = (int)$id;
        }
        FeedbackModel::destroy($ids2);
        return $this->success('删除成功');
    }

}