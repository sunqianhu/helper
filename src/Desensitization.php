<?php

namespace Sunqianhu\Helper;

use InvalidArgumentException;

class Desensitization
{

    /**
     * 得到手机号码加星
     * @param $phone
     * @return string
     */
    function getPhoneAddStar($phone)
    {
        $pattern = '/(\d{3})\d{5}(\d{3})/isuU';
        return preg_replace($pattern, '$1*****$2', $phone);
    }

    /**
     * 得到身份证号码加星
     * @param $idNumber
     * @return string
     */
    function getIdNumberAddStar($idNumber)
    {
        if (!preg_match('/\A[0-9]{17}[0-9Xx]\z/', $idNumber)) {
            return $idNumber;
        }

        return substr($idNumber, 0, 12) . '******';
    }

    /**
     * 得到姓名加星
     * @param $name
     * @return string
     */
    function getNameAddStar($name)
    {
        $pattern = '/(.).(.*)/isu';
        return preg_replace($pattern, '$1*$2', $name);
    }
}
