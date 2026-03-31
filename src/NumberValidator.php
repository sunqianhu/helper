<?php

namespace Sunqianhu\Helper;

use Exception;

class NumberValidator
{
    /**
     * 验证
     * @param $fieldName
     * @param $number
     * @return true
     * @throws Exception
     */
    static public function check($fieldName, $number)
    {
        if(!is_numeric($number)){
            throw new Exception($fieldName.'必须是个数字');
        }
        return true;
    }
}