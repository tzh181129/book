<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/16
 * Time: 18:59
 */
namespace  app\tplay\lib\exception;
use think\exception\Handle;

use app\tplay\lib\TplayPro;
use app\tplay\lib\Time;

class ApiHandleException extends  Handle {

    /**
     * http 状态码
     * @var int
     */
    public $httpCode = 500;
    public $message = '';
    public $code = 0;
    public $url = '';
    public $data = [];
    public $tarce = '';
    public function render(\Exception $e)
    {
        if(isset($_REQUEST['app_debug'])){
            return parent::render($e);
        }
        if ($e instanceof ApiException) {
            $this->httpCode=$e->httpCode;
            $this->code=$e->code;
            $this->data=$e->data;
            $this->url=$e->url;
        }
        $res=[];
        $res['code']=$this->code;
        $res['msg']=$e->getMessage();
        $data=$e->getTrace();
        $res['timestamp']=Time::get13TimeStamp();
        $header=[];
        $header['timestamp']=$res['timestamp'];
        $res['data']=$data;
        $res['sign']=IAuth::setSign($res);
        return json($res,$this->httpCode,$header);
    }
}