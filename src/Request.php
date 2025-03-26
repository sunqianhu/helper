<?php

namespace Sunqianhu\Helper;

class Request
{
    /**
     * 得到输入流值
     * @param $keys
     * @return array
     */
    function input($keys = [])
    {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        if (empty($data)) {
            return [];
        }
        if (empty($keys)) {
            return $data;
        }

        $arr = new Arr();
        return $arr->getDesignatedKeyList($data, $keys);
    }

    /**
     * 得到post值
     * @param $keys
     * @return array
     */
    function post($keys = [])
    {
        $data = $_POST;
        if (empty($data)) {
            return [];
        }
        if (empty($keys)) {
            return $data;
        }

        $arr = new Arr();
        return $arr->getDesignatedKeyList($data, $keys);
    }

    /**
     * 得到get值
     * @param $keys
     * @return array
     */
    function get($keys = [])
    {
        $data = $_GET;
        if (empty($data)) {
            return [];
        }
        if (empty($keys)) {
            return $data;
        }

        $arr = new Arr();
        return $arr->getDesignatedKeyList($data, $keys);
    }
}