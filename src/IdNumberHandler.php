<?php
namespace Sunqianhu\Helper;

use Exception;

class IdNumberHandler
{
    /**
     * 得到性别
     * @param $idNumber
     * @return int
     */
    public function getGender($idNumber){
        $genderId = 0;
        if(strlen($idNumber) != 18){
            return $genderId;
        }

        $penultimate = substr($idNumber, -2, 1);
        if($penultimate % 2 == 1){
            $genderId = 0;
        }else{
            $genderId = 1;
        }

        return $genderId;
    }

    /**
     * 得到生日
     * @param $idNumber
     * @return int
     * @throws Exception
     */
    public function getBirthday($idNumber){
        if(strlen($idNumber) != 18){
            throw new Exception('身份证号码必须是18位');
        }

        $year = substr($idNumber, 6, 4);
        $month = substr($idNumber, 10, 2);
        $day = substr($idNumber, 12, 2);
        return strtotime($year.'-'.$month.'-'.$day);
    }
}