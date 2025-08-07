<?php

namespace Sunqianhu\Helper;

class MarkDownHandler
{
    /**
     * 移除代码块的标记
     * @param $code
     * @param $flag
     * @return string
     */
    public function removeCodeFlag($code, $flag)
    {
        $pattern = '/```'.$flag.'\s*(.*?)\s*```/isu';
        return preg_replace($pattern, '$1', $code);
    }
}