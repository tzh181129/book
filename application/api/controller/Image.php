<?php

namespace app\api\controller;

use app\tplay\lib\TplayPro;
use think\Config;
use think\Db;
use think\Request;

class Image extends TplayPro
{
    public function index(){
        $openid=$_POST['openid'];
        $result = self::upload('admin', 'image');
        $insert['name'] = $result['name'];
        $insert['path'] = str_replace("\\", "/", $result['data']);
        $insert['create_time'] = time();
        Db::name('image')->insert($insert);
        $path = $this->domain . $insert['path'];
        Db::name('user')->where(['openid' => $openid])->update(['avatar' => $path]);
        return $this->show(1,'头像修改成功');
    }
}
