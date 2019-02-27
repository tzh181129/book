<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]'     => [
        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],

    '/'                       => 'index/index?current=1',                    //太珩
    'product'                 =>'article/index?cid=9',                       //产品
    'mproduct'                =>'article/show?id=2',                         //Cr12MoV
    'hproduct'                =>'article/show?id=4',                         //H13ESR
    'cproduct'                =>'article/show?id=5',                         //Cr12
    'dproduct'                =>'article/show?id=6',                         //Skd11
    'dcproduct'               =>'article/show?id=28',                        //Dc53
    'stock'                   =>'article/index?cid=6',                       //库存
    'message'                 =>'article/index?cid=7',                       //信息
    'contact'                 =>'article/index?cid=8',                       //联系
    'detail'                  =>'article/show',                              //信息详情
];
