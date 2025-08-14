<?php

namespace Sunqianhu\Helper;

use Exception;

class IdsValidator
{
    /**
     * 验证
     * @param $fieldName
     * @param $ids
     * @return true
     * @throws Exception
     */
    static public function check($fieldName, $ids)
    {
        if(!is_array($ids)){
            throw new Exception($fieldName.'格式必须是个数组');
        }
        foreach ($ids as $key=>$id){
            if(!is_numeric($id)){
                throw new Exception($fieldName.'中的第'.($key+1).'项必须是数字类型');
            }
            if($id < 0){
                throw new Exception($fieldName.'中的第'.($key+1).'项不能小于0');
            }
        }
        return true;
    }
}