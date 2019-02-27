<?php

namespace app\api\service;

use \client\Curl;
use WxPay\WxPayApp;
use think\Controller;
use think\Config;

/**
 * 小程序API接口服务
 */
class WxApp extends Controller
{
    /**
     * 小程序主机
     * @var string
     */
    public $host   = "https://api.weixin.qq.com/";

    /**
     * 请求参数
     * @var array
     */
    public $params = [];

    public $key = '';

    protected function _initialize()
    {
        $wechat_config = Config::get('wechat_config');
        $this->params = [
            'appid' => $wechat_config['appid'],
            'secret' => $wechat_config['appsecret'],
        ];
        $this->key = $wechat_config['key'];
    }

    /**
     * 小程序登录, 通过登录凭证CODE获取 openid 和 session_key 等
     * @param string $code 登录凭证
     * @return mixed
     */
    public function login($code)
    {
        //小程序登录地址
        $host   = "https://api.weixin.qq.com/sns/jscode2session";

        $params = array_merge($this->params,
        [
            "js_code"    => $code,
            "grant_type" => "authorization_code",
        ]);

        return $this->get($params, $host);
    }

    /**
     * 小程序支付
     * @param array $order 订单数据
     */
    public function pay($order)
    {
        $data =
        [
            "trade_no"  => $order['order_no'],
            "timestamp" => time(),
            "openid"    => $order['openid'],
            "host"      => request()->ip(),
            "order"     => $order['id'],
            "money"     => $order['pay_total'] * 100,
            "remark"    => "心愿店",
        ];

        $result = (new WxPayApp())->send($data);

        // 二次签名
        $paySign = md5("appId=".$result['appId']."&nonceStr=".$result['nonceStr']."&package=".$result['package']."&signType=".$result['signType']."&timeStamp=".$result['timeStamp']."&key=".$this->key);

        unset($result['paySign']);

        $result['paySign'] = strtoupper($paySign);

        return $result;
    }

    /**
     * 发起GET请求
     * @param array  $params 附加参数
     * @param string $host   请求地址
     * @return mixed
     */
    protected function get(array $params, $host)
    {
        $curl = (new Curl())->cancelSSLVerify()->get($host, $params);

        if(false === ($result = $curl->response()))
        {
            throw new \Exception($curl->error());
        }

        //尝试解码
        return json_decode($result, true) ? : $result;
    }

    /**
     * 发起POST请求
     * @param array  $params 附加参数
     * @param string $host   请求地址
     * @return mixed
     */
    protected function post(array $params, $host)
    {
        $curl = (new Curl())->cancelSSLVerify()->post($host, $params);

        if(false === ($result = $curl->response()))
        {
            throw new \Exception($curl->error());
        }

        //尝试解码
        return json_decode($result, true) ? : $result;
    }
}
