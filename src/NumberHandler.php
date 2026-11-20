<?php

namespace Sunqianhu\Helper;

class NumberHandler
{

    /**
     * 格式化显示
     * @param $number
     * @return string
     */
    public function getReadableNumber($number)
    {
        //数字小于1万直接返回
        if ($number < 10000) {
            return $number;
        }

        if ($number < 100000000) {
            return number_format($number / 10000, 1) . '万';
        }
        if ($number < 1000000000000) {
            return number_format($number / 100000000, 1) . '亿';
        }
        return number_format($number / 1000000000000, 1) . '万亿';
    }

    /**
     * 得到百分比
     * @param $number1
     * @param $number2
     * @param $precision
     * @return float
     */
    public function getPercentage($number1, $number2, $precision = 2)
    {
        $total = $number1 + $number2;
        if($total == 0){
            return 0;
        }
        return round($number1 / $total * 100, $precision);
    }
}
