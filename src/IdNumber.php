<?php
namespace sunqianhu\helper;

class IdNumber
{
    /**
     * 得到性别id
     * @param $idNumber
     * @return int
     */
    public function getGenderId($idNumber){
        $genderId = 0;
        if(strlen($idNumber) != 18){
            return $genderId;
        }

        $penultimate = substr($idNumber, -2, 1);
        if($penultimate % 2 == 1){
            $genderId = 1;
        }else{
            $genderId = 2;
        }

        return $genderId;
    }

    /**
     * 得到生日
     * @param $idNumber
     * @return int
     */
    public function getBirthday($idNumber){
        $birthday = 0;
        if(strlen($idNumber) != 18){
            return $birthday;
        }

        $year = substr($idNumber, 6, 4);
        $month = substr($idNumber, 10, 2);
        $day = substr($idNumber, 12, 2);
        $birthday = strtotime($year.'-'.$month.'-'.$day);

        return $birthday;
    }
}