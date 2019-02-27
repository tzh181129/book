<?php

namespace app\api\controller;
use app\api\controller\Base;

use think\Db;
use think\Request;

class Novel extends Base
{
    //首页推荐书籍
    public function recommed(){
        $data=\think\loader::model('common/novel')->where(['status'=>1,'order'=>1])->order('create_time desc')->limit(6)->select();
//        foreach($data as $k=>$vo){
//            $data[$k]['img']='https://www.pptzh.com'.$vo['img'];
//        }
        return $this->show(1, '', $this->setSign($data));
    }
    //书籍列表
    public function novelList(){
        $data=\think\loader::model('common/novel')->where(['status'=>1])->order('num desc')->select();
        return $this->show(1, '', $this->setSign($data));
    }

    //书籍章节列表与章节内容
    public function chapterList(){
        $param=$this->request->param();
        $novel_id=$param['novel_id'];
        $data=\think\loader::model('common/chapter')->where(['novel_id'=>$novel_id])->order('id asc')->select();
        return $this->show(1,'', $this->setSign($data));
    }
}
