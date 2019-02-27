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
class Admin extends Model
{
	protected $insert = ['create_time', 'update_time', 'last_login_time'];

    /**
     * 创建时间
     * @return bool|string
     */
    protected function setCreateTimeAttr()
    {
        return time();
    }

    /**
     * 更新时间
     * @return bool|string
     */
    protected function setUpdateTimeAttr()
    {
        return time();
    }

    /**
     * 上次登录时间
     * @return bool|string
     */
    protected function setLastLoginTimeAttr()
    {
        return time();
    }
}