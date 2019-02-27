<?php
namespace app\api\controller;
use app\api\controller\Base;

class Index extends Base
{
    public function carousel()
    {
        $key = isset($this->param['key']) ? $this->param['key'] :  \think\Db::name('carousel_cate')->where('is_main',1)->value('key');
        $cate_id = \think\Db::name('carousel_cate')->where('key',$key)->value('id');
        $data = \think\Db::name('carousel')->where(['carousel_cate_id'=>$cate_id])->order('order asc')->select();
        foreach ($data as $k => $v) {
        	$data[$k]['img'] = $this->domain.$v['img'];
        }

        return $this->show(1,'',$this->setSign($data));
    }

    public function cate()
    {
        $pid = isset($this->param['pid']) ? $this->param['pid'] : 0;
        $key = isset($this->param['key']) ? $this->param['key'] :  \think\Db::name('content_cate_group')->where('is_main',1)->value('key');
        $cate_id = \think\Db::name('content_cate_group')->where('key',$key)->value('id');
        $data = \think\Db::name('content_cate')->where(['content_cate_group_id'=>$cate_id,'pid'=>$pid])->order('create_time asc')->select();
        
        return $this->show(1,'',$this->setSign($data));
    }

    public function menu()
    {
        $pid = isset($this->param['pid']) ? $this->param['pid'] : 0;
        $key = isset($this->param['key']) ? $this->param['key'] :  \think\Db::name('index_menu_cate')->where('is_main',1)->value('key');
        $cate_id = \think\Db::name('index_menu_cate')->where('key',$key)->value('id');
        $data = \think\Db::name('index_menu')->where(['index_menu_cate_id'=>$cate_id,'pid'=>$pid])->order('create_time asc')->select();
        
        return $this->show(1,'',$this->setSign($data));
    }

    public function content()
    {
        $limit = isset($this->param['rows']) ? $this->param['rows'] : 20;
        $key = isset($this->param['key']) ? $this->param['key'] :  \think\Db::name('content_cate_group')->where('is_main',1)->value('key');
        $group_id = \think\Db::name('content_cate_group')->where('key',$key)->value('id');
        $cate_id = isset($this->param['cate_id']) ? $this->param['cate_id'] : \think\Db::name('content_cate')->where(['content_cate_group_id'=>['IN', $group_id]])->column('id');
        
        $data = \think\loader::model('common/content')->where(['content_cate_id'=>['IN',$cate_id],'status'=>['>',0]])->order('create_time desc')->paginate($limit,false,['page'=>$this->param['page'],'query'=>$this->param])->each(function($item, $key){
			    $item->img = $this->domain.explode(',',$item->img)[0];
			});
        return $this->show(1,'',$this->setSign($data));
    }

    public function contentInfo()
    {
        if(!isset($this->param['id']) or $this->param['id'] < 1) {
            return $this->show(0,'参数不正确');
        }
        $data['goods'] = \think\Db::name('content')
                ->where(['id'=>$this->param['id'],'status'=>['>',0]])
                ->find();
        $data['goods']['img'] = explode(',',$data['goods']['img']);
        foreach ($data['goods']['img'] as $k => $v) {
            $data['goods']['img'][$k] = $this->domain.$v;
        }
        $data['message'] = \think\Db::name('message')
                            ->field('*,w.id as user_id,a.create_time as time')
                            ->alias('a')
                            ->join('user w','a.user_id = w.id','LEFT')
                            ->where(['a.content_id'=> $this->param['id'],'a.status'=>1])
                            ->order('a.create_time desc')
                            ->limit(2)
                            ->select();
        foreach ($data['message'] as $k => $v) {
            $data['message'][$k]['time'] = date('Y-m-d',$v['time']);
        }

        return $this->show(1,'',$this->setSign($data));
    }


}
