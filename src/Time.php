<?php

namespace sunqianhu\helper;

class Time
{

    /**
     * 得到时间段名称
     * @param $time
     * @return string
     */
    function getFragmentName($time)
    {
        if(empty($time)){
            return '';
        }

        $hour = date('G', $time);
        $name = '';
        if ($hour >= 0 && $hour < 5) {
            $name = '凌晨';
        } else if ($hour >= 5 && $hour < 8) {
            $name = '早上';
        } else if ($hour >= 8 && $hour < 11) {
            $name = '上午';
        } else if ($hour >= 11 && $hour < 13) {
            $name = '中午';
        } else if ($hour >= 13 && $hour < 17) {
            $name = '下午';
        } else if ($hour >= 17 && $hour < 19) {
            $name = '傍晚';
        } else if ($hour >= 19 && $hour < 23) {
            $name = '晚上';
        }else{
            $name = '子夜';
        }

        return $name;
    }
}