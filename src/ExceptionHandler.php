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
        $response = new Response();
        echo $response->error($exception->getMessage());
        exit;
    }
}