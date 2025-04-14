<?php

namespace Sunqianhu\Helper;

class TimeRange
{
    /**
     * 得到时间范围的月份列表
     * @param $startTime
     * @param $endTime
     * @return array
     */
    public function getMonths($startTime, $endTime)
    {
        $startTime = strtotime(date('Y-m-01', $startTime));
        $endTime = strtotime(date('Y-m-01', $endTime));
        $months = [];
        for ($time = $startTime; $time < $endTime; $time = strtotime('+1 month', $time)) {
            $monthStartTime = $time;
            $monthEndTime = strtotime('+1 month', $time) - 1;
            if($monthEndTime > $endTime){
                $monthEndTime = $endTime;
            }
            $months[] = [
                'key'=>date('Ym', $time),
                'date_format'=>date('Y-m', $time),
                'name'=>date('m月', $time),
                'full_name'=>date('Y年m月', $time),
                'start_time'=>$monthStartTime,
                'end_time'=>$monthEndTime
            ];
        }
        return $months;
    }
}