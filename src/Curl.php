<?php

namespace sunqianhu\helper;

use Exception;

class Curl
{
    /**
     * @var array 选项
     */
    public $options = [
        CURLOPT_TIMEOUT => 25,
        CURLOPT_HTTPHEADER => [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/117.0.0.0 Safari/537.36'
        ],
        CURLOPT_HEADER => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0
    ];

    /**
     * 构造函数
     */
    public function __construct()
    {

    }

    /**
     * 替换选项
     * @param $option
     * @return void
     */
    public function replaceOptions($options)
    {
        $this->options = $options;
    }

    /**
     * 设置选项
     * @param $key 键
     * @param $value 值
     * @return void
     */
    public function setOption($key, $value)
    {
        $this->options[$key] = $value;
    }

    /**
     * 删除选项
     * @param $key
     * @return void
     */
    public function deleteOption($key)
    {
        if (!isset($this->options[$key])) {
            return;
        }
        unset($this->options[$key]);
    }

    /**
     * get请求
     * @param string $url url
     * @return string
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

        if ($response === false) {
            $errno = curl_errno($ch);
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception('curl请求失败，错误号：' . $errno . ',' . '错误描述：' . $error);
        }
        curl_close($ch);

        return $response;
    }

    /**
     * post请求
     * @param string $url 网址
     * @param array $fields 字段
     * @return string
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

        if ($response === false) {
            $errno = curl_errno($ch);
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception('curl请求失败，错误号：' . $errno . ',' . '错误描述：' . $error);
        }
        curl_close($ch);

        return $response;
    }
}