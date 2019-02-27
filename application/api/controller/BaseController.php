<?php
/**
 * Created by PhpStorm.
 * User: 罗阁科技
 * Date: 2018/11/8
 * Time: 12:04
 */

namespace app\api\controller;

use think\exception\HttpResponseException;
use think\Request;
use think\Response;

class BaseController extends Base
{
    /*
     * HTTP status
     * 200
     * 400
     * 404
     */
    public function json($data, $code)
    {
        return $this->result($data, $code, '', 'json');
    }

    public function succeed($data)
    {
        return $this->json($data, 200);
    }

    public function errors($data)
    {
        return $this->json($data, 400);
    }

    public function noContent()
    {
        return $this->json([], 204);
    }

    protected function result($data, $code = 0, $msg = '', $type = '', array $header = [])
    {
        $result = [
            'code' => $code,
            'msg' => $msg,
            'time' => Request::instance()->server('REQUEST_TIME'),
            'data' => $data,
        ];
        $type = $type ?: $this->getResponseType();
        $response = Response::create($result, $type)->header($header);

        throw new HttpResponseException($response);
    }
}