<?php

namespace Sunqianhu\Helper;

class JsonResponse
{
    /**
     * 得到成功
     * @param $message
     * @param $data
     * @return false|string
     */
    public function getSuccess($message, $data = null)
    {
        $response = [
            'code' => 0,
            'message' => $message,
            'data' => $data
        ];
        return json_encode($response);
    }

    /**
     * 得到失败
     * @param $message
     * @param $data
     * @param $code
     * @return false|string
     */
    public function getError($message, $data = null, $code = 1)
    {
        $response = [
            'code' => $code,
            'message' => $message,
            'data' => $data
        ];
        return json_encode($response);
    }
}