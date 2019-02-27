<?php
/**
 * Created by PhpStorm.
 * User: 罗阁科技
 * Date: 2018/11/20
 * Time: 11:48
 */

namespace app\admin\controller;

use app\tplay\lib\Base;
use think\Db;
use think\db\Query;
use think\helper\Arr;
use think\Request;
use app\tplay\lib\Authcheck;

class Message extends Authcheck
{
    public function noticeList()
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
                $where['user_name']= ['like', "%" .$data['key']['name']. "%"];
            }
        }

        $data = \think\loader::model('common/message')->where($where)
            ->order('create_time desc')
            ->paginate($limit, false, ['query' => $this->request->param()])
            ->each(function ($item, $key) {
            });
        foreach ($data as $k => $v) {
            if ($v['is_look'] == 0) {
                $data[$k]['look'] = '未读';
            } else {
                $data[$k]['look'] = '已读';
            }
            if ($v['status'] == 0) {
                $data[$k]['type'] = '反馈';
            } else {
                $data[$k]['type'] = '投诉';
            }
            if($v['order_no']!=''  || $v['order_no'] !='null'){
                $data[$k]['product_id']=\think\loader::model('common/order')->where(['order_no'=>$v['order_no']])->value('product_id');
                $data[$k]['product']=\think\loader::model('common/product')->where(['id'=>$v['product_id']])->value('name');
            }else{
                $data[$k]['product']='---';
            }
            $data[$k]['user_email']=$v['user_email'] !=''? $v['user_email'] : '---';
            $data[$k]['user_name']=$v['user_name'] !=''? $v['user_name'] : \think\loader::model('common/user')->where(['openid'=>$v['openid']])->value('name');
            $data[$k]['user_phone']=$v['user_phone'] !=''? $v['user_phone'] : '---';
        }
        return $this->showList($data);
    }

    public function editNotice()
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
            Db::startTrans();
            try {
                \think\loader::model('common/message')->where(['id' => $post['id']])->update(['is_look' => 1]);
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

            $order = \think\loader::model('common/message')->where(['id' => $id])->find();
            if($order['order_no']!=''  || $order['order_no'] !='null'){
                $order['product_id']=\think\loader::model('common/order')->where(['order_no'=>$order['order_no']])->value('product_id');
                $order['product']=\think\loader::model('common/product')->where(['id'=>$order['product_id']])->value('name');
            }
            else{
                $order['product']='---';
            }
            $order['user_name']=$order['user_name'] !=''? $order['user_name'] : \think\loader::model('common/user')->where(['openid'=>$order['openid']])->value('name');
            $order['user_phone']=$order['user_phone'] !=''? $order['user_phone'] : '---';
            $order['user_email']=$order['user_email'] !=''? $order['user_email'] : '---';
            if (empty($order)) {
                return $this->error('非法操作');
            }
            //$course['image'] = Db::name('image')->where(['id' => $course['image_id']])->value('path');

            $this->success('', '', $order);
        }
    }

    public function delNotice()
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
                \think\loader::model('common/message')->where(['id' => $id])->delete();;
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


}