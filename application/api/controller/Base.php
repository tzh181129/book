<?php
/**
 * Created by PhpStorm.
 * User: 罗阁科技
 * Date: 2018/11/15
 * Time: 11:16
 * 接口基础类
 */

namespace app\api\controller;

use app\api\service\WxApp as WxAppService;
use app\api\service\WXBizDataCrypt;
use app\tplay\lib\TplayPro;
use think\Config;
use think\Db;
use think\Request;

class Base extends TplayPro
{
    public $param;
    public $login_result;

    protected function _initialize()
    {
        parent::_initialize();

        // header('Access-Control-Allow-Origin:*');
        // header('Access-Control-Allow-Methods:POST');
        // header('Access-Control-Allow-Headers:x-requested-with,content-type');

        parent::_initialize();
        // 首先需要获取headers
        $param = $this->request->param();
        $result = $this->getOpenid();
        $param['openid'] = $result['openid'];

        // // 基础参数校验
        // if(empty($param['sign'])) {
        //     throw new ApiException('sign不存在', 400);
        // }
        // // 需要sign
        // if(!$this->checkSign($param)) {
        //     throw new ApiException('授权码sign失败', 401);
        // }
        // //把获取数据的sign缓存起来,做唯一性验证
        // Cache::set($param['sign'], 1, config('app.app_sign_cache_time'));

        // $this->param = $this->checkSign($param);

        $this->param = $param;
        $this->login_result = $result;
    }

    private function getOpenid()
    {
        $skey = Request::instance()->header('X-WX-Skey');

        if ($skey) {
            //查找已有会话
            $result['openid'] = Db::name('session')->where(['skey' => $skey])->value('openid');
        } else {
            //创建新会话
            $code = Request::instance()->header('X-WX-Code');
            $encrypt_data = Request::instance()->header('X-WX-Encrypted-Data');
            $iv = Request::instance()->header('X-WX-IV');


            //应返回openid:用户唯一标识,session_key:会话密钥,unionid：用户在开放平台的唯一标识符
            $service = (new WxAppService())->login($code);
            if (!isset($service["openid"]) || !isset($service["session_key"])) {
                // return ["status" => 0, "info" => "miss params:openid & session_key", "debug" => $service];
                $this->show(0, '登录失败', $this->setSign("miss params:openid & session_key"));
            }

            $appid = Config::get('wechat_config.appid');
            $pc = new WXBizDataCrypt($appid, $service["session_key"]);
            $errCode = $pc->decryptData($encrypt_data, $iv, $data);
            $data_arr = json_decode($data, true);

            $user = Db::name('user')->where(['openid' => $data_arr['openId']])->find();

            $user_data['openid'] = $data_arr['openId'];
            $user_data['name'] = $data_arr['nickName'];
            $user_data['gender'] = $data_arr['gender'];
            $user_data['avatar'] = $data_arr['avatarUrl'];
            $user_data['province'] = $data_arr['province'];
            $user_data['city'] = $data_arr['city'];


            if (!$user) {
                //添加新用户
                $user_data['create_time'] = time();
                Db::name('user')->insert($user_data);
            } else {
                Db::name('user')->where(['id' => $user['id']])->update($user_data);
            }

            $result['skey'] = sha1($service["openid"] . $service["session_key"]);
            $result['userInfo'] = $data;
            $result['openid'] = $service['openid'];

            //保存登录态至数据库
            $insert['skey'] = $result['skey'];
            $insert['session_key'] = $service['session_key'];
            $insert['openid'] = $service['openid'];
            Db::name('session')->insert($insert);

        }

        return $result;
    }
}
