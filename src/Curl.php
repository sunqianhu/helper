<?php

namespace Sunqianhu\Helper;

use Exception;

class Curl
{
    /**
     * @var array 选项
     */
    public $options = [
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTPHEADER => [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/117.0.0.0 Safari/537.36'
        ],
        CURLOPT_HEADER => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0
    ];

    /**
     * @var int http状态码
     */
    private $httpCode = 0;

    /**
     * @var bool 抛出http异常
     */
    private $throwHttpException = true;

    /**
     * 构造函数
     */
    public function __construct()
    {

    }

    /**
     * 替换选项
     * @param $options
     * @return Curl
     */
    public function setOptions($options)
    {
        $this->options = array_merge($this->options, $options);
        return $this;
    }

    /**
     * 设置选项
     * @param $key
     * @param $value
     * @return Curl
     */
    public function setOption($key, $value)
    {
        $this->options[$key] = $value;
        return $this;
    }

    /**
     * 删除选项
     * @param $key
     * @return Curl
     * @throws Exception
     */
    public function deleteOption($key)
    {
        if (!isset($this->options[$key])) {
            throw new Exception('选项不存在：' . $key);
        }
        unset($this->options[$key]);
        return $this;
    }

    /**
     * 设置是否抛出http异常
     * @param bool $throw
     * @return Curl
     */
    public function setThrowHttpException(bool $throw)
    {
        $this->throwHttpException = $throw;
        return $this;
    }

    /**
     * 得到http状态码
     * @return int
     */
    public function getHttpCode()
    {
        return $this->httpCode;
    }
    
    /**
     * get请求
     * @param string $url
     * @return bool|string
     * @throws Exception
     */
    public function get(string $url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        foreach ($this->options as $key => $option) {
            curl_setopt($ch, $key, $option);
        }
        $response = curl_exec($ch);

        //传输层
        $errno = curl_errno($ch);
        if($errno != 0){
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception('curl请求失败，请求网址：' . $url . '，错误号：' . $errno . '，' . '错误描述：' . $error);
        }

        //应用层
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->httpCode = $httpCode;
        if($this->throwHttpException && $httpCode >= 400){
            curl_close($ch);
            throw new Exception('curl请求失败，请求网址：' . $url . '，http状态码：' . $httpCode);
        }

        curl_close($ch);

        return $response;
    }

    /**
     * post请求
     * @param string $url
     * @param $fields
     * @return bool|string
     * @throws Exception
     */
    public function post(string $url, $fields = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        foreach ($this->options as $key => $option) {
            curl_setopt($ch, $key, $option);
        }
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        $response = curl_exec($ch);

        //传输层
        $errno = curl_errno($ch);
        if($errno != 0){
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception('curl请求失败，请求网址：' . $url . '，错误号：' . $errno . '，' . '错误描述：' . $error);
        }

        //应用层
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->httpCode = $httpCode;
        if($this->throwHttpException && $httpCode >= 400){
            curl_close($ch);
            throw new Exception('curl请求失败，请求网址：' . $url . '，http状态码：' . $httpCode);
        }

        curl_close($ch);

        return $response;
    }
}