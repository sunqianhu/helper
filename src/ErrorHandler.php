<?php

namespace Sunqianhu\Helper;

class ErrorHandler
{
    /**
     * 注册错误处理器
     * @return void
     */
    public function register()
    {
        // 注册普通错误处理
        set_error_handler([$this, 'handle']);
    }

    /**
     * 处理普通错误 (Warning, Notice 等)
     * @param int    $errNo   错误级别
     * @param string $errStr  错误信息
     * @param string $errFile 文件名
     * @param int    $errLine 行号
     * @return bool 返回 false 让 PHP 内部错误处理器也执行（可选），返回 true 表示已处理
     */
    public function handle($errNo, $errStr, $errFile, $errLine)
    {
        //是否关闭错误报告
        if (error_reporting() === 0) {
            return false;
        }

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

        // 格式化信息，隐藏绝对路径（生产环境安全建议）
        $file = basename($errFile);
        $message = $errorType.'：'.$errStr.'，在文件'.$file.'的第'.$errLine.'行';

        $jsonResponse = new JsonResponse();
        echo $jsonResponse->getError($message);

        return true;
    }
}