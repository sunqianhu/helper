<?php

namespace Sunqianhu\Helper;

use Exception;

class IdsValidate
{
    /**
     * 验证
     * @param $ids
     * @param $fieldName
     * @return true
     * @throws Exception
     */
    static public function check($ids, $fieldName)
    {
        if(!is_array($ids)){
            throw new Exception($fieldName.'格式必须是个数组');
        }
        foreach ($ids as $id){
            if(!is_numeric($id)){
                throw new Exception($fieldName.'中的id必须是数字类型');
            }
            if($id < 0){
                throw new Exception($fieldName.'中的id不能小于0');
            }
        }
        return true;
    }
}