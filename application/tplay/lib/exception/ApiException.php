<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/16
 * Time: 18:59
 */
namespace  app\tplay\lib\exception;
use think\Exception;

class ApiException extends Exception {

    public $message = '';
    public $httpCode = 500;
    public $code = 0;
    public $url = '';
    public $data = [];
    public $tarce = '';
    /**
     * @param string $message
     * @param int $httpCode
     * @param int $code
     */
    public function __construct($message = '', $httpCode = 0, $code = 0,$url='',$data=[]) {
        $this->httpCode = $httpCode;
        $this->message = $message;
        $this->code = $code;
        $this->url = $url;
        $this->data = $data;
    }
}