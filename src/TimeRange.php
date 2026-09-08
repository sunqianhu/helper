<?php

namespace Sunqianhu\Helper;

use Exception;

class TimeRange
{
    /**
     * 得到时间片段类型
     * @param $startTime
     * @param $endTime
     * @return string
     * @throws Exception
     */
    public function getSegmentType($startTime, $endTime)
    {
        if ($startTime > $endTime) {
            throw new Exception('开始时间不能大于结束时间');
        }

        $range = $endTime - $startTime;
        $daySeconds = 24 * 60 * 60;
        if ($range < $daySeconds) {
            return 'hour';
        }
        if ($range < 31 * $daySeconds) {
            return 'day';
        }
        if ($range < 3 * 31 * $daySeconds) {
            return 'week';
        }
        if ($range < 3 * 365 * $daySeconds) {
            return 'month';
        }
        return 'year';
    }

    /**
     * 得到时间片段列表
     * @param $startTime
     * @param $endTime
     * @return array
     * @throws Exception
     */
    public function getSegments($startTime, $endTime)
    {
        $segmentType = $this->getSegmentType($startTime, $endTime);
        if ($segmentType == 'hour') {
            return $this->getHourSegments($startTime, $endTime);
        }
        if ($segmentType == 'day') {
            return $this->getDaySegments($startTime, $endTime);
        }
        if ($segmentType == 'week') {
            return $this->getWeekSegments($startTime, $endTime);
        }
        if ($segmentType == 'month') {
            return $this->getMonthSegments($startTime, $endTime);
        }
        return $this->getYearSegments($startTime, $endTime);
    }

    /**
     * 得到小时片段列表
     * @param $startTime
     * @param $endTime
     * @return array
     * @throws Exception
     */
    public function getHourSegments($startTime, $endTime)
    {
        if ($startTime > $endTime) {
            throw new Exception('开始时间不能大于结束时间');
        }

        $segments = [];
        $time = strtotime(date('Y-m-d H:00:00', $startTime));
        while ($time <= $endTime) {
            $nextTime = strtotime('+1 hour', $time);
            $segmentStartTime = max($startTime, $time);
            $segmentEndTime = min($endTime, $nextTime - 1);
            $segments[] = [
                'key' => $segmentStartTime,
                'name' => date('G时', $segmentStartTime),
                'full_name' => date('Y年n月j日 G时', $segmentStartTime),
                'start_time' => $segmentStartTime,
                'end_time' => $segmentEndTime,
            ];
            $time = $nextTime;
        }
        return $segments;
    }

    /**
     * 得到日时间片段列表
     * @param $startTime
     * @param $endTime
     * @return array
     * @throws Exception
     */
    public function getDaySegments($startTime, $endTime)
    {
        if ($startTime > $endTime) {
            throw new Exception('开始时间不能大于结束时间');
        }

        $segments = [];
        $time = strtotime(date('Y-m-d 00:00:00', $startTime));
        while ($time <= $endTime) {
            $nextTime = strtotime('+1 day', $time);
            $segmentStartTime = max($startTime, $time);
            $segmentEndTime = min($endTime, $nextTime - 1);
            $segments[] = [
                'key' => $segmentStartTime,
                'name' => date('n月j日', $segmentStartTime),
                'full_name' => date('Y年n月j日', $segmentStartTime),
                'start_time' => $segmentStartTime,
                'end_time' => $segmentEndTime,
            ];
            $time = $nextTime;
        }
        return $segments;
    }

    /**
     * 得到周时间片段列表
     * @param $startTime
     * @param $endTime
     * @return array
     * @throws Exception
     */
    public function getWeekSegments($startTime, $endTime)
    {
        if ($startTime > $endTime) {
            throw new Exception('开始时间不能大于结束时间');
        }

        $segments = [];
        $time = strtotime('monday this week 00:00:00', $startTime);
        while ($time <= $endTime) {
            $nextTime = strtotime('+1 week', $time);
            $segmentStartTime = max($startTime, $time);
            $segmentEndTime = min($endTime, $nextTime - 1);
            $segments[] = [
                'key' => $segmentStartTime,
                'name' => '第' . (int) date('W', $time) . '周',
                'full_name' => date('Y年n月j日', $segmentStartTime)
                    . '-' . date('Y年n月j日', $segmentEndTime),
                'start_time' => $segmentStartTime,
                'end_time' => $segmentEndTime,
            ];
            $time = $nextTime;
        }
        return $segments;
    }

    /**
     * 得到月时间片段列表
     * @param $startTime
     * @param $endTime
     * @return array
     * @throws Exception
     */
    public function getMonthSegments($startTime, $endTime)
    {
        if ($startTime > $endTime) {
            throw new Exception('开始时间不能大于结束时间');
        }

        $segments = [];
        $time = strtotime(date('Y-m-01 00:00:00', $startTime));
        while ($time <= $endTime) {
            $nextTime = strtotime('+1 month', $time);
            $segmentStartTime = max($startTime, $time);
            $segmentEndTime = min($endTime, $nextTime - 1);
            $segments[] = [
                'key' => $segmentStartTime,
                'name' => date('n月', $segmentStartTime),
                'full_name' => date('Y年n月', $segmentStartTime),
                'start_time' => $segmentStartTime,
                'end_time' => $segmentEndTime,
            ];
            $time = $nextTime;
        }
        return $segments;
    }

    /**
     * 得到年时间片段列表
     * @param $startTime
     * @param $endTime
     * @return array
     * @throws Exception
     */
    public function getYearSegments($startTime, $endTime)
    {
        if ($startTime > $endTime) {
            throw new Exception('开始时间不能大于结束时间');
        }

        $segments = [];
        $time = strtotime(date('Y-01-01 00:00:00', $startTime));
        while ($time <= $endTime) {
            $nextTime = strtotime('+1 year', $time);
            $segmentStartTime = max($startTime, $time);
            $segmentEndTime = min($endTime, $nextTime - 1);
            $segments[] = [
                'key' => $segmentStartTime,
                'name' => date('Y年', $segmentStartTime),
                'full_name' => date('Y年', $segmentStartTime),
                'start_time' => $segmentStartTime,
                'end_time' => $segmentEndTime,
            ];
            $time = $nextTime;
        }
        return $segments;
    }
}