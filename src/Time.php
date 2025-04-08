<?php

namespace Sunqianhu\Helper;

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

    /**
     * 得到秒转为可读的时间
     * @return string
     */
    public function getSecondToReadableTime($second) {
        $year = floor($second / (365*24*3600));
        $remainder = $second % (365*24*3600);
        $month = floor($remainder / (30*24*3600));
        $remainder = $remainder % (30*24*3600);
        $day = floor($remainder / (24*3600));
        $remainder = $remainder % (24*3600);
        $hour = floor($remainder / 3600);
        $remainder = $remainder % 3600;
        $minute = floor($remainder / 60);
        $second = $remainder % 60;

        $readableTime = '';
        if ($year > 0) {
            $readableTime .= $year.'年';
        }
        if ($month > 0) {
            $readableTime .= $month.'月';
        }
        if ($day > 0) {
            $readableTime .= $day.'日';
        }
        if ($hour > 0) {
            $readableTime .= $hour.'小时';
        }
        if ($minute > 0) {
            $readableTime .= $minute.'分钟';
        }
        if ($second > 0) {
            $readableTime .= $second.'秒';
        }

        return $readableTime;
    }
}