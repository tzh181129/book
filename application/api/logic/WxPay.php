<?php
namespace app\api\logic;

use WxPay\Notify;
use WxPay\WxPayData;
use WxPay\WxPayResults;

use think\Log;
use think\Db;

/**
 * 微信回调处理逻辑
 * @package app\halei\logic
 */
class WxPay
{
    public function handleNotify($xml = '')
    {
    	$notify = new Notify();
        $wxPayResults = new WxPayResults();
        //签名验证
        $data = $wxPayResults->Init($xml);

        Log::init(['type' => 'File', 'path' => ROOT_PATH . '/extend/pay_log/']);
        Log::write('支付回调参数:'.json_encode($data));

        if (empty($data)) 
        {
            //验证不通过返回
            $notify->NotifyReturn();
            die;
        }
        //验证通过返回
        $notify->NotifyReturn(true);

        //1.检查订单状态是否已支付
        // $order = OrderModel::detail(array('order_no' => $data['out_trade_no']));
        $order = Db::name('order')->where(['order_no' => $data['out_trade_no']])->find();

        if ($order['status'] == 2) 
        {
            die;
        }
        else
        {
            //修改订单状态
            // $order_model = new OrderModel();
            // $order_model->where(array('order_no' => $data['out_trade_no']))->update(array('status' => 1));
            $update['transaction_id'] = $data['transaction_id'];
            $update['status'] = 2;
            Db::name('order')->where(['order_no' => $data['out_trade_no']])->update($update);
        }

        // file_put_contents(ROOT_PATH.time().'++++'.$data['out_trade_no'].'.txt', $data);die;
        Log::write('支付成功订单号:'.$data['out_trade_no']);

    }

    
}
