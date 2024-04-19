<?php

namespace Sunqianhu\Helper;

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
     * @param $phone
     * @return string
     */
    function getIdNumberAddStar($idNumber)
    {
        $pattern = '/(\d{12})\d{6}/isuU';
        return preg_replace($pattern, '$1******', $idNumber);
    }

    /**
     * 得到姓名加星
     * @param $phone
     * @return string
     */
    function getNameAddStar($name)
    {
        $pattern = '/(.).(.*)/isu';
        return preg_replace($pattern, '$1*$2', $name);
    }
}