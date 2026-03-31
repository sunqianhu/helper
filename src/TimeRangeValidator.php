<?php

namespace Sunqianhu\Helper;

use Exception;

class TimeRangeValidator
{
    /**
     * 验证
     * @param $fieldName
     * @param $times
     * @return true
     * @throws Exception
     */
    public function check($fieldName, $times)
    {
        if(!is_array($times)){
            throw new Exception($fieldName.'格式必须是个数组');
        }
        if(count($times) != 2){
            throw new Exception($fieldName.'必须有开始时间和结束时间');
        }
        if(!is_numeric($times[0])){
            throw new Exception($fieldName.'开始时间格式错误');
        }
        if(!is_numeric($times[1])){
            throw new Exception($fieldName.'结束时间格式错误');
        }
        if($times[0] > $times[1]){
            throw new Exception($fieldName.'开始时间不能大于结束时间');
        }
        return true;
    }
}