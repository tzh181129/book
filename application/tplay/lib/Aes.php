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

namespace app\tplay\lib;

/**
 * aes 加密 解密类库
 * @by singwa
 * Class Aes
 * @package app\common\lib
 */
class Aes {

    private $key = null;
    /**
     *
     * @param $key 		密钥
     * @return String
     */
    public function __construct() {
        // 需要小伙伴在配置文件/application/config.php中定义aes_key
        $this->key = config('app.aes_key');
    }

    /**
     * 加密
     * @param String input 加密的字符串
     * @param String key   解密的key
     * @return HexString
     */
    public function encrypt($input = '') {
//            如果是debug模式则使用固定的加密向量,为密钥的前16位
        $iv=substr(base64_encode(openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'))),0,16);
//        已aes 256位 CBC填充进行加密 返回结果base64加密
        $data=openssl_encrypt($input, "aes-256-cbc", $this->key, 0, $iv);
        return base64_encode($iv.$data);
    }

    /**
     * 解密
     * @param String input 解密的字符串
     * @param String key   解密的key
     * @return String
     */
    public function decrypt($input) {
        $input=base64_decode($input);
        $iv=substr($input,0,16);
        $data=substr($input,16);
        $return=openssl_decrypt($data, "aes-256-cbc", $this->key, 0, $iv);
        return $return;
    }
}