<?php

namespace Sunqianhu\Helper;

class Response
{
    /**
     * 成功响应
     * @param $message
     * @param $data
     * @return false|string
     */
    public function success($message, $data = null)
    {
        $response = [
            'code' => 1,
            'message' => $message,
            'data' => $data
        ];
        return json_encode($response);
    }

    /**
     * 失败响应
     * @param $message
     * @param $data
     * @param $code
     * @return false|string
     */
    public function error($message, $data = null, $code = 0)
    {
        $response = [
            'code' => $code,
            'message' => $message,
            'data' => $data
        ];
        return json_encode($response);
    }
}