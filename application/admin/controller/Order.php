<?php
// +----------------------------------------------------------------------
// | Tplay [ WE ONLY DO WHAT IS NECESSARY ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://tplay.pengyichen.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yuanyuan < 38625673@qq.com >
// +----------------------------------------------------------------------

namespace app\admin\controller;

use think\Db;
use think\Cache;
use app\tplay\lib\Authcheck;
use Delivery\Express;

class Order extends Authcheck
{
    protected $service_unit = [
        ['id' => 1, 'title' => '小时'],
        ['id' => 2, 'title' => '局']
    ];


    /**
     * 订单列表
     */
    public function orderList()
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
            if ($data['key']['product_name'] != '')
            {
                $product_name= ['like', "%" .$data['key']['product_name']. "%"];
                $arr=\think\loader::model('common/product')->where(['name'=>$product_name])->find();
                $where['product_id']=$arr['id'];
            }
            if ($data['key']['order_no'] != '')
            {
                $where['order_no'] = $data['key']['order_no'];
            }
            if ($data['key']['seller_name'] != '')
            {
                $where['seller_name'] = ['like', "%" . $data['key']['seller_name'] . "%"];
            }
        }
        $data = \think\loader::model('common/order')->where($where)
            ->order('create_time desc')
            ->paginate($limit, false, ['query' => $this->request->param()])
            ->each(function ($item, $key) {
            });
        foreach ($data as $k => $v) {
            /*if($v['logistics_no'] !=''){
                $e = new  Express();
                $data = $e->getLogisticsInfo($v['logistics_no']);
                if($data == ''){
                    $status='正等待快递公司发货';
                }else{
                    $status=$data;
                }
            }else{
                $status=$v['status'];
            }
            \think\loader::model('common/order')->where(['id'=>$v['order_id']])->update(['status'=>$status]);
            */
            $data[$k]['product_name'] = \think\loader::model('common/product')->where(['id' => $v['product_id']])->value('name');
            $data[$k]['helpoint'] = \think\loader::model('common/product')->where(['id' => $v['product_id']])->value('helpoint');
        }
        return $this->showList($data);
    }

    //添加订单
    public function addOrder()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();

            $validate = new \think\Validate([
                'product_id' => 'require',
                'user_id' => 'require',
            ]);
            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }
            $post['order_no'] = makeOrderNo();
            $post['openid '] = \think\loader::model('common/user')->where(['id' => $post['user_id']])->value('openid');
            $post['process'] = '';
            $post['seller_id'] = \think\loader::model('common/product')->where(['id' => $post['product_id']])->value('seller_id');
            $post['status'] = 0;
            $post['create_time'] = time();
            // 启动事务
            Db::startTrans();
            try {
                \think\loader::model('common/order')->allowField(true)->save($post);
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                return $this->error('操作失败');
            }
            return $this->success('操作成功');
        } else {
            $where=[];
            $where['lower_time']=array('gt',time());
            $where['status']=0;
            $product = Db::name('product')->field('id,name')->where($where)->select();
            $result['product'] = $product;
            $user = Db::name('user')->field('id,name')->where(['status' => 1])->select();
            $result['user'] = $user;
            // $unit = Db::name('unit')->field('id,title')->where(['status' => 1])->select();
            // $result['data'] = $unit;
            return $this->show(1, 'ok', $result);
        }
    }

    //编辑订单
    public function editOrder()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();

            $validate = new \think\Validate([
                'id' => 'require',
                'logistics_no' => 'require',
                'kgs'           =>'require',
            ]);


            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }
            $post['status']=2;
            /*$e = new  Express();
            $data = $e->getLogisticsInfo($post['logistics_no']);
            if($data == ''){
                $post['status']='正等待快递公司发货';
            }else{
                $post['status']=$data;
            }*/
            // 启动事务
            Db::startTrans();
            try {
                \think\loader::model('common/order')->allowField(true)->save($post, ['id' => $post['id']]);
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

            $order = \think\loader::model('common/order')->where(['id' => $id])->find();
            if (empty($order)) {
                return $this->error('非法操作');
            }
            $order['product_name'] = \think\loader::model('common/product')->where(['id' => $order['product_id']])->value('name');
            $order['helpoint'] = \think\loader::model('common/product')->where(['id' => $order['product_id']])->value('helpoint');
            $order['seller_name'] = \think\loader::model('common/seller')->where(['id' => $order['seller_id']])->value('name');
            $order['address'] = \think\loader::model('common/delivery')->where(['openid' => $order['openid']])->value('area').'-'.\think\loader::model('common/delivery')->where(['openid' => $order['openid']])->value('address');
            $order['phone'] = \think\loader::model('common/delivery')->where(['openid' => $order['openid']])->value('phone');
            //$course['image'] = Db::name('image')->where(['id' => $course['image_id']])->value('path');

            $this->success('', '', $order);
        }
    }

    public function delOrder()
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
                \think\loader::model('common/order')->where(['id' => $id])->delete();;
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


    //订单列表
    public function list()
    {
        $limit = $this->request->has('limit') ? $this->request->param('limit') : 20;
        $list = \think\loader::model('common/order')
            ->where([])
            ->order('create_time desc')
            ->paginate($limit, false, ['query' => $this->request->param()])
            ->each(function ($item, $key) {
                // $item->icon = '<img width="50px" src="'.Db::name('resource')->where(['id' => $item->icon_id])->value('path').'"/>';
                $item->boss = Db::name('user')->where(['id' => $item->boss_uid])->value('name');
                $item->player = Db::name('user')->where(['id' => $item->play_uid])->value('name');
                $item->transaction_id = $item->transaction_id ? $item->transaction_id : '--';
                $item->skill = Db::name('skill')->where(['id' => $item->sid])->value('name');
            });

        return $this->showList($list);
    }

    //添加陪玩项目
    public function add()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $post['unit'] = implode(",", $post['unit']);

            $validate = new \think\Validate([
                'name' => 'require',
                'icon_id' => 'require',
            ]);
            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }
            $post['create_time'] = time();

            // 启动事务
            Db::startTrans();
            try {
                \think\loader::model('common/skill')->allowField(true)->save($post);
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                return $this->error('操作失败');
            }
            return $this->success('操作成功');
        } else {
            $result['data'] = 'ok';
            return $this->show(1, 'ok', $result);
        }
    }

    //上传图片文件
    public function uploadIcon()
    {
        $result = self::upload('admin', 'skillicon');
        //添加视频文件
        $insert['type'] = 1;
        $insert['name'] = $result['name'];
        $insert['path'] = str_replace("\\", "/", $result['data']);
        $insert['create_time'] = time();

        Db::name('resource')->insert($insert);
        $iid = Db::name('resource')->getLastInsID();

        $result['iid'] = $iid;
        $result['path'] = $insert['path'];

        return $this->show(1, 'ok', $result);
    }

    public function edit()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();

            $validate = new \think\Validate([
                'name' => 'require',
                'icon_id' => 'require'
            ]);
            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }

            $post['unit'] = implode(",", $post['unit']);

            // 启动事务
            Db::startTrans();
            try {
                \think\loader::model('common/skill')->allowField(true)->save($post, ['id' => $post['id']]);
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
            $info['skill'] = \think\loader::model('common/skill')->where(['id' => $id])->find();
            if (empty($info['skill'])) {
                return $this->error('非法操作');
            }

            $info['skill']['icon'] = Db::name('resource')->where(['id' => $info['skill']['icon_id']])->value('path');

            $unit = explode(",", $info['skill']['unit']);
            $service_unit = $this->service_unit;

            foreach ($service_unit as $k => $v) {
                if (in_array($v['id'], $unit)) {
                    $service_unit[$k]['checked'] = 1;
                } else {
                    $service_unit[$k]['checked'] = 0;
                }
            }
            $info['unit'] = $service_unit;

            $this->success('', '', $info);
        }
    }

    public function editPwd()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $validate = new \think\Validate([
                'old_pwd' => 'require|different:password',
                'password' => 'require|confirm'
            ]);
            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }
            $admin = Db::name('admin')->where(['id' => $this->admin_id])->find();
            if (md5(md5($post['old_pwd']) . $admin['salt']) !== $admin['password']) {
                return $this->error('旧密码错误');
            }
            $salt = salt();
            if (false == Db::name('admin')->where(['id' => $this->admin_id])->update(['password' => md5(md5($post['password']) . $salt), 'salt' => $salt])) {
                return $this->error('操作失败');
            } else {
                return $this->success('操作成功');
            }
        }
    }

    public function del()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $array = [];
            if (!is_array($post['id'])) {
                $array[] = $post['id'];
            } else {
                $array = $post['id'];
            }
            foreach ($array as $v) {
                if ($v == $this->admin_id) {
                    return $this->error('不能删除自己');
                }
            }
            if (false == $this->soft(['model' => 'common/admin', 'index' => $array])) {
                return $this->error('操作失败');
            }
            return $this->success('操作成功');
        }
    }

    //段位信息列表
    public function paragraphList()
    {
        $limit = $this->request->has('limit') ? $this->request->param('limit') : 20;
        $list = \think\loader::model('common/paragraph')
            ->where([])
            ->order('create_time desc')
            ->paginate($limit, false, ['query' => $this->request->param()])
            ->each(function ($item, $key) {
                $item->skill = Db::name('skill')->where(['id' => $item->sid])->value('name');
            });

        return $this->showList($list);
    }

    //添加段位信息
    public function addParagraph()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $validate = new \think\Validate([
                'name' => 'require',
                'sid' => 'require',
            ]);
            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }
            $post['create_time'] = time();

            // 启动事务
            Db::startTrans();
            try {
                \think\loader::model('common/paragraph')->allowField(true)->save($post);
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                return $this->error('操作失败');
            }
            return $this->success('操作成功');
        } else {
            //获取技能列表
            $result['skill'] = \think\loader::model('common/skill')->field('id,name')->where(['status' => 1])->order('sort')->select();
            return $this->show(1, 'ok', $result);
        }
    }

    //大区信息列表
    public function largeareaList()
    {
        $limit = $this->request->has('limit') ? $this->request->param('limit') : 20;
        $list = \think\loader::model('common/largearea')
            ->where([])
            ->order('create_time desc')
            ->paginate($limit, false, ['query' => $this->request->param()])
            ->each(function ($item, $key) {
                $item->skill = Db::name('skill')->where(['id' => $item->sid])->value('name');
            });

        return $this->showList($list);
    }

    //添加大区信息
    public function addLargearea()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $validate = new \think\Validate([
                'name' => 'require',
                'sid' => 'require',
            ]);
            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }
            $post['create_time'] = time();

            // 启动事务
            Db::startTrans();
            try {
                \think\loader::model('common/largearea')->allowField(true)->save($post);
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                return $this->error('操作失败');
            }
            return $this->success('操作成功');
        } else {
            //获取技能列表
            $result['skill'] = \think\loader::model('common/skill')->field('id,name')->where(['status' => 1])->order('sort')->select();
            return $this->show(1, 'ok', $result);
        }
    }

    //标签信息列表
    public function labelList()
    {
        $limit = $this->request->has('limit') ? $this->request->param('limit') : 20;
        $list = \think\loader::model('common/label')
            ->where(['type' => 1])
            ->order('create_time desc')
            ->paginate($limit, false, ['query' => $this->request->param()])
            ->each(function ($item, $key) {
                $item->skill = Db::name('skill')->where(['id' => $item->sid])->value('name');
            });

        return $this->showList($list);
    }

    //添加标签信息
    public function addLabel()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $validate = new \think\Validate([
                'name' => 'require',
                'sid' => 'require',
            ]);
            if (!$validate->check($post)) {
                return $this->error($validate->getError());
            }
            $post['type'] = 1;
            $post['create_time'] = time();

            // 启动事务
            Db::startTrans();
            try {
                \think\loader::model('common/label')->allowField(true)->save($post);
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                return $this->error('操作失败');
            }
            return $this->success('操作成功');
        } else {
            //获取技能列表
            $result['skill'] = \think\loader::model('common/skill')->field('id,name')->where(['status' => 1])->order('sort')->select();
            return $this->show(1, 'ok', $result);
        }
    }

}
