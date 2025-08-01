<?php

namespace Sunqianhu\Helper;

use Throwable;

class ExceptionHandler
{
    /**
     * 处理
     * @param Throwable $exception
     * @return void
     */
    public function handle(Throwable $exception)
    {
        $jsonResponse = new JsonResponse();
        echo $jsonResponse->error($exception->getMessage());
        exit;
    }
}