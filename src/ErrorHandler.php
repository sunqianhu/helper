<?php

namespace Sunqianhu\Helper;

class ErrorHandler
{
    /**
     * 处理
     * @param $errno
     * @param $errstr
     * @param $errfile
     * @param $errline
     * @return void
     */
    public function handle($errno, $errstr, $errfile, $errline){
        $response = new Response();
        $errorTypes = [
            E_WARNING => '警告',
            E_NOTICE => '注意'
        ];
        $errorType = isset($errorTypes[$errno]) ? $errorTypes[$errno] : '未知错误';
        $message = "发生了一个$errorType错误：$errstr，位于文件 $errfile 的第 $errline 行";
        echo $response->error($message);
        exit;
    }
}