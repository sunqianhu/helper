<?php

namespace Sunqianhu\Helper;

class NumberHandler
{

    /**
     * 格式化显示
     * @param $number
     * @return string
     */
    function showFormat($number)
    {
        //数字小于1万直接返回
        if ($number < 10000) {
            return $number;
        }

        //计算数字位数
        $digit = floor(log10($number));

        //根据位数确定单位
        switch ($digit) {
            case 4:
            case 5:
            case 6:
            case 7:
                $suffix = '万';
                break;
            case 8:
            case 9:
            case 10:
            case 11:
                $suffix = '亿';
                break;
            default:
                $suffix = '';
        }

        //格式化数字并返回
        return number_format($number / pow(10, $digit - ($digit % 4)), 1) . $suffix;
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