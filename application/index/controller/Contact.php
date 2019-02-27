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

namespace app\index\controller;

use think\Config;
use think\Session;
use think\Cookie;
use think\Db;
use \think\Validate;
use app\index\controller\Base;

class Contact extends Base
{
  public function contact()
  {
    if($this->request->isPost()){
      $post = $this->request->post();
      $validate = new \think\Validate([
          'name'  => 'require',
          'email' => 'require|email',
          'phone' => 'require|length:11',
          'content'=>'require|length:1,1000'
      ]);
      if (!$validate->check($post)) {
          return $this->error($validate->getError());
      }

      if(false == \think\loader::model('common/contact')->allowField(true)->save($post)) {
          return $this->error('提交失败');
      }
      return $this->success('提交成功');
    } else {
      return $this->fetch();
    }
  }
}
