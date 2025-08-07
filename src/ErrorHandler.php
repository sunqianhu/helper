<?php

namespace Sunqianhu\Helper;

class ErrorHandler
{
    /**
     * 处理
     * @param $errNo
     * @param $errStr
     * @param $errFile
     * @param $errLine
     * @return void
     */
    public function handle($errNo, $errStr, $errFile, $errLine){
        $errorTypes = [
            E_ERROR             => '致命错误',
            E_WARNING           => '警告',
            E_PARSE             => '解析错误',
            E_NOTICE            => '注意',
            E_CORE_ERROR        => '核心错误',
            E_CORE_WARNING      => '核心警告',
            E_COMPILE_ERROR     => '编译错误',
            E_COMPILE_WARNING   => '编译警告',
            E_USER_ERROR        => '用户错误',
            E_USER_WARNING      => '用户警告',
            E_USER_NOTICE       => '用户注意',
            E_STRICT            => '严格标准',
            E_RECOVERABLE_ERROR => '可恢复错误',
            E_DEPRECATED        => '已废弃',
            E_USER_DEPRECATED   => '用户已废弃',
        ];
        $errorType = $errorTypes[$errNo] ?? '未知错误';
        $message = $errorType.'：'.$errStr.'，在文件'.$errFile.'的第'.$errLine.'行';
        $jsonResponse = new JsonResponse();
        echo $jsonResponse->getError($message);
        exit;
    }
}