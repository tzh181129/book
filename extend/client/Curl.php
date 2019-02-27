<?php
/**
 * Bookfeel PHP Framework (http://www.bookfeel.cn)
 *
 * @author wuquanyao <bookfeel@yeah.net>
 * @copyright Copyright (c) 2015-2018
 * @license http://www.bookfeel.cn/license  MIT License
 */

namespace client;

/**
 * cUrl类
 * @package bookfeel\http\client
 */
class Curl
{
    /**
     * cUrl选项
     * @var array
     */
    protected $options        = [];

    /**
     * cookie信息
     * @var array
     */
    protected $cookie         = [];

    /**
     * header信息
     * @var array
     */
    protected $header         = [];

    /**
     * 最大重定向
     * @var int
     */
    protected $maxRedirect    = 10;

    /**
     * 执行超时,单位秒
     * @var int
     */
    protected $timeOut        = 30;

    /**
     * 连接超时,单位秒
     * @var int
     */
    protected $connectTimeOut = 5;

    /**
     * 保存cookie的文件
     * @var string
     */
    protected $cookieFile     = "";

    /**
     * 响应
     * @var string
     */
    protected $response       = null;

    /**
     * 状态码
     * @var int
     */
    protected $code            = 200;

    /**
     * cUrl句柄
     * @var resource
     */
    protected $curl           = null;

    /**
     * 架构函数
     * @param array $options CURL选项
     * @throws \Exception
     */
    public function __construct(array $options = [])
    {
        if(false === extension_loaded("curl"))
        {
            throw new \Exception("Curl extensions are not installed.");
        }

        $this->options =
        [
            CURLOPT_RETURNTRANSFER => true,//将执行结果以字符串输出,而不是直接输出
            CURLOPT_FOLLOWLOCATION => true,//TRUE时将会根据服务器返回HTTP头中的Location:重定向
            CURLOPT_USERAGENT      => true,//设置User-Agent头信息
            CURLOPT_FAILONERROR    => true,//当HTTP状态码大于等于400,TRUE将显示错误详情
            CURLINFO_HEADER_OUT	   => true,//True时可获取请求头信息
            CURLOPT_HEADER         => false,//True时可获取响应头信息,默认响应头不输出
            CURLOPT_NOBODY         => false,//True时不输出内容
            CURLOPT_AUTOREFERER    => true,//TRUE时将根据Location:重定向时,自动设置header中的Referer:信息
        ];

        $this->curl = curl_init();

        if(!empty($options))
        {
            $this->setOptions($options);
        }
    }

    /**
     * 设置cUrl选项
     * @param int|array $name  选项名
     * @param mixed     $value 选项值
     * @return Curl
     */
    public function setOptions($name, $value = null)
    {
        if(is_array($name))
        {
            $this->options = array_replace($this->options, $name);
        }
        else
        {
            $this->options[$name] = $value;
        }

        return $this;
    }

    /**
     * 自定义HTTP Header头信息
     * @param string|array $name header名
     * @param string $value      header值
     * @return Curl
     */
    public function setHeader($name, $value)
    {
        if(is_array($key))
        {
            foreach($name as $key => $value)
            {
                $this->header[$key] = $key . ":" . $value;
            }
        }
        else
        {
            $this->header[$name] = $name . ":" . $value;
        }

        return $this;
    }

    /**
     * 自定义HTTP UserAgent头信息
     * @param string $userAgent
     * @return Curl
     */
    public function setUserAgent($userAgent)
    {
        return $this->setOptions(CURLOPT_USERAGENT, $userAgent);
    }

    /**
     * 自定义HTTP Referer头信息
     * @param string $referer
     * @return Curl
     */
    public function setReferer($referer = "")
    {
        return $this->setOptions(CURLOPT_REFERER, $referer);
    }

    /**
     * 自定义HTTP Cookie头信息
     * @param string|array $name 数组或字符串
     * @param string $value
     * @return Curl
     */
    public function setCookie($name, $value = null)
    {
        if(is_array($name))
        {
            foreach($name as $key => $value)
            {
                $this->cookie[$key] = $key ."=". $value;
            }
        }
        else
        {
            $this->cookie[$name] = $name ."=". $value;
        }

        return $this;
    }

    /**
     * 设置cookie存取的文件
     * @param string $file 文件路径
     * @return Curl
     */
    public function setCookieFile($file)
    {
        $this->cookieFile = $file;
        return $this;
    }

    /**
     * 设置重定向最大次数
     * @param int $num 次数
     * @return Curl
     */
    public function setMaxRedirect($num)
    {
        $this->maxRedirect = (int)$num;
        return $this;
    }

    /**
     * 设置连接中需要的用户名和密码
     * @param string $username 用户名
     * @param string $password 密码
     * @return Curl
     */
    public function setUserPassword($username, $password)
    {
        return $this->setOptions(CURLOPT_USERPWD, $username .":". $password);
    }

    /**
     * 设置代理连接中需要的用户名和密码
     * @param string $username 用户名
     * @param string $password 密码
     * @return Curl
     */
    public function setProxyUserPassword($username, $password)
    {
        return $this->setOptions(CURLOPT_PROXYUSERPWD, $username .":". $password);
    }

    /**
     * 设置代理服务器地址
     * @param string $server 代理服务器地址
     * @param int    $port   代理端口
     * @param int    $type   代理类型,可选 CURLPROXY_HTTP 和 CURLPROXY_SOCKS5
     * @param int    $mode   代理认证模式,可选 CURLAUTH_BASIC 和 CURLAUTH_NTLM
     * @return Curl
     */
    public function setProxyServer($server, $port, $type = CURLPROXY_HTTP, $mode = CURLAUTH_BASIC)
    {
        $this->setOptions(CURLOPT_PROXY, $server);
        $this->setOptions(CURLOPT_PROXYPORT, $port);
        $this->setOptions(CURLOPT_PROXYAUTH, $mode);
        $this->setOptions(CURLOPT_PROXYTYPE, $type);

        return $this;
    }

    /**
     * cUrl连接超时设置,单位秒,0表示无限等待
     * @param int $second
     * @return Curl
     */
    public function setConnectTimeOut($second = 0)
    {
        $this->connectTimeOut = (int)$second;
        return $this;
    }

    /**
     * cUrl执行超时设置,单位秒,0表示无限等待
     * @param int $second
     * @return Curl
     */
    public function setTimeOut($second = 0)
    {
        $this->timeOut = (int)$second;
        return $this;
    }

    /**
     * 是否报告cUrl发生所有问题
     * @param boolean $verbose 是否报告问题
     * @return Curl
     */
    public function setVerbose($verbose = true)
    {
        return $this->setOptions(CURLOPT_VERBOSE, (boolean)$verbose);
    }

    /**
     * 设置客户端支持压缩,gzip/deflate/gzip,deflate
     * @param string $encoding 压缩方式
     * @return Curl
     */
    public function setEncoding($encoding = "gzip,deflate")
    {
        return $this->setOptions(CURLOPT_ENCODING, $encoding);
    }

    /**
     * 取消HTTPS请求验证
     * @return Curl
     */
    public function cancelSSLVerify()
    {
        return $this->setOptions(
            [
                CURLOPT_SSL_VERIFYHOST => false,//是否检查证书
                CURLOPT_SSL_VERIFYPEER => false,//是否验证对等证书
            ]);
    }

    /**
     * 发起请求
     * @param array $options CURL选项
     * @return Curl
     */
    protected function exec(array $options = [])
    {
        if(!empty($this->header))
        {
            $this->setOptions(CURLOPT_HTTPHEADER, array_values($this->header));
        }

        if(!empty($this->cookie))
        {
            $this->setOptions(CURLOPT_COOKIE, implode(";", $this->cookie));
        }

        if(is_string($this->cookieFile) && "" != $this->cookieFile)
        {
            $this->setOptions(CURLOPT_COOKIEJAR, $this->cookieFile);
            $this->setOptions(CURLOPT_COOKIEFILE, $this->cookieFile);
        }

        if(0 < $this->maxRedirect)
        {
            $this->setOptions(CURLOPT_MAXREDIRS, (int)$this->maxRedirect);
        }

        if(0 < $this->connectTimeOut)
        {
            $this->setOptions(CURLOPT_CONNECTTIMEOUT, $this->connectTimeOut);
        }

        if(0 < $this->timeOut)
        {
            $this->setOptions(CURLOPT_TIMEOUT, $this->timeOut);
        }

        if(!empty($options))
        {
            $this->setOptions($options);
        }

        curl_setopt_array($this->curl, $this->options);
        $this->response = curl_exec($this->curl);

        return $this;
    }

    /**
     * GET请求
     * @param string $url 请求地址,注意加上协议http或https
     * @param array  $data 请求参数
     * @param array  $options CURL选项
     * @return Curl
     */
    public function get($url, array $data = [], array $options = [])
    {
        if(!empty($data))
        {
            $url .= (false === strpos($url, "?") ? "?" : "&") . http_build_query($data);
        }

        return $this->setOptions(
        [
            CURLOPT_URL     => $url,
            CURLOPT_HTTPGET => true
        ])->exec($options);
    }

    /**
     * POST请求
     * @param string $url 请求地址,注意加上协议http或https
     * @param array  $data 请求参数
     * @param array  $options CURL选项
     * @return Curl
     */
    public function post($url, array $data = [], array $options = [])
    {
        return $this->setOptions([
            CURLOPT_URL        => $url,
            CURLOPT_POST       => true,
            CURLOPT_POSTFIELDS => $data
        ])->exec($options);
    }

    /**
     * PUT请求
     * @param string $url 请求地址,注意加上协议http或https
     * @param array  $data 请求参数
     * @param array  $options CURL选项
     * @return Curl
     */
    public function put($url, array $data = [], array $options = [])
    {
        if(!empty($data))
        {
            $url .= (false === strpos($url, "?") ? "?" : "&") . http_build_query($data);
        }

        return $this->setOptions(
        [
            CURLOPT_URL           => $url,
            CURLOPT_CUSTOMREQUEST => "PUT"
        ])->exec($options);
    }

    /**
     * PATCH请求
     * @param string $url 请求地址,注意加上协议http或https
     * @param array  $data 请求参数
     * @param array  $options CURL选项
     * @return Curl
     */
    public function patch($url, array $data = [], array $options = [])
    {
        if(!empty($data))
        {
            $url .= (false === strpos($url, "?") ? "?" : "&") . http_build_query($data);
        }

        return $this->setOptions(
        [
            CURLOPT_URL           => $url,
            CURLOPT_CUSTOMREQUEST => "PATCH"
        ])->exec($options);
    }

    /**
     * DELETE请求
     * @param string $url 请求地址,注意加上协议http或https
     * @param array  $data 请求参数
     * @param array  $options CURL选项
     * @return Curl
     */
    public function delete($url, array $data = [], array $options = [])
    {
        if(!empty($data))
        {
            $url .= (false === strpos($url, "?") ? "?" : "&") . http_build_query($data);
        }

        return $this->setOptions(
        [
            CURLOPT_URL           => $url,
            CURLOPT_CUSTOMREQUEST => "DELETE"
        ])->exec($options);
    }

    /**
     * 文件下载,前提使用get,post等发起请求
     * @param string $save     文件保存路径,如果路径不存在将自动创建
     * @param string $filename 要生成的文件名, abc.png
     * @return boolean
     */
    public function save($save, $filename)
    {
        if(is_string($this->response))
        {
            if(is_string($save))
            {
                !is_dir($save) && mkdir($save, 0777, true);

                $result = file_put_contents($save ."/". $filename, $this->response);

                if(false !== $result)
                {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * 获取响应数据,设置了CURLOPT_RETURNTRANSFER时成功则返回结果
     * @return boolean|string
     */
    public function response()
    {
        return $this->response;
    }

    /**
     * 获取响应状态码
     * @return int
     */
    public function code()
    {
        return curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
    }

    /**
     * 获取传输数据
     * @param int $option 参数,null:表示是获取全部信息
     * @return string
     * @see http://php.net/manual/zh/function.curl-getinfo.php
     */
    public function info($option = null)
    {
        return null === $option ? curl_getinfo($this->curl) : curl_getinfo($this->curl, $option);
    }

    /**
     * 错误字符串,当curl_exec执行失败时,调用次函数
     * @return string
     */
    public function error()
    {
        return curl_error($this->curl);
    }

    /**
     * 错误代码,当curl_exec执行失败时,调用次函数
     * @return int
     */
    public function errno()
    {
        return curl_errno($this->curl);
    }

    /**
     * cUrl版本
     * @return array
     */
    public function version()
    {
        return curl_version();
    }

    /**
     * 析构函数
     */
    public function __destruct()
    {
        if(is_resource($this->curl))
        {
            curl_close($this->curl);
        }
    }
}