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

namespace app\common\model;
use think\Db;
use think\Model;
class IndexMenu extends Model
{
    public function menuList($menu,$id=0,$level=0){
        
        static $menus = array();
        foreach ($menu as $value) {
            if ($value['pid']==$id) {
                $value['level'] = $level+1;
                if($level == 0)
                {
                    $value['str'] = str_repeat('__',$value['level']);
                }
                elseif($level == 2)
                {
                    $value['str'] = '&emsp;&emsp;'.'└ ';
                }
                elseif($level == 3)
                {
                    $value['str'] = '&emsp;&emsp;&emsp;'.'└ ';
                }
                else
                {
                    $value['str'] = '&emsp;'.'└ ';
                }
                $menus[] = $value;
                $this->menulist($menu,$value['id'],$value['level']);
            }
        }
        return $menus;
    }
}