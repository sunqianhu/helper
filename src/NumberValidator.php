<?php

namespace Sunqianhu\Helper;

use Exception;

class NumberValidator
{
    /**
     * 验证
     * @param $fieldName
     * @param $id
     * @return true
     * @throws Exception
     */
    static public function check($fieldName, $id)
    {
        if(!is_numeric($id)){
            throw new Exception($fieldName.'必须是个数字');
        }
        return true;
    }
}