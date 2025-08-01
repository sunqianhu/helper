<?php

namespace Sunqianhu\Helper;

class JsonResponse
{
    /**
     * 响应成功
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
     * 响应失败
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