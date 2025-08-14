<?php

namespace Sunqianhu\Helper;

use Exception;

class IdValidator
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
        if($id < 0){
            throw new Exception($fieldName.'不能小于0');
        }
        return true;
    }
}