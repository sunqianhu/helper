<?php

namespace Sunqianhu\Helper;

use JsonException;

class JsonResponse
{
    /**
     * 得到成功
     * @param $message
     * @param $data
     * @return string
     * @throws JsonException JSON编码失败时抛出
     */
    public function getSuccess($message, $data = null)
    {
        $response = [
            'code' => 0,
            'message' => $message,
            'data' => $data
        ];
        return json_encode($response, JSON_THROW_ON_ERROR);
    }

    /**
     * 得到失败
     * @param $message
     * @param $data
     * @param $code
     * @return string
     * @throws JsonException JSON编码失败时抛出
     */
    public function getError($message, $data = null, $code = 1)
    {
        $response = [
            'code' => $code,
            'message' => $message,
            'data' => $data
        ];
        return json_encode($response, JSON_THROW_ON_ERROR);
    }
}
