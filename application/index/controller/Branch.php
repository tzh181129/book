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

use app\index\controller\Base;
use app\admin\model\ArticleCate;
use \think\Db;

class Branch extends Base
{
    public function index()
    {
        $model = new ArticleCate();

        $cate = $model->withCount(['article'=>function($query){
                                        $query->where('status',1);
                                    }])->order('create_time asc')->select();

        $this->assign('info',$cate);
        return $this->fetch();
    }
}
