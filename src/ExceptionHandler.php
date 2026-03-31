<?php

namespace Sunqianhu\Helper;

use Throwable;

class ExceptionHandler
{
    /**
     * 注册异常处理器
     * @return void
     */
    public function register()
    {
        set_exception_handler([$this, 'handle']);
    }

    /**
     * 处理
     * @param Throwable $exception
     * @return void
     */
    public function handle(Throwable $exception)
    {
        $jsonResponse = new JsonResponse();
        echo $jsonResponse->getError($exception->getMessage());
    }
}