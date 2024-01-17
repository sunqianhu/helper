<?php

namespace sunqianhu\helper;

class StringHelper
{
    /**
     * 字符串长度
     * @param string $string 字符串
     * @return int 字符串长度
     */
    function length($string)
    {
        $matchs = array();
        $length = 0;

        if ($string === '') {
            return $length;
        }

        if (function_exists('mb_utf8length')) {
            $length = mb_utf8length($string, 'utf-8');
        } else {
            preg_match_all('/./u', $string, $matchs);
            $length = count($matchs[0]);
        }

        return $length;
    }

    /**
     * 截取字符串
     * @param string $string 字符串
     * @param int $start 开始位置
     * @param int $length 长度
     * @return string 截取后的字符串
     */
    function sub($string, $start, $length)
    {
        $new = '';
        $matchs = array();

        if ($string === '') {
            return $new;
        }

        if (function_exists('mb_substr')) {
            $new = mb_substr($string, $start, $length, 'utf-8');
        } else {
            preg_match_all('/./u', $string, $matchs);
            $new = join('', array_slice($matchs[0], $start, $length));
        }

        return $new;
    }

    /**
     * 从0开始带省略号截取
     * @param string $string 字符串
     * @param int $length 截取长度
     * @return string 截取后的字符串
     */
    function zeroEllipsisSub($string, $length)
    {
        $new = '';
        $total = 0; // 字符串总长度

        if ($string === '') {
            return $string;
        }

        $total = $this->length($string);
        if ($total <= $length) {
            return $string;
        }

        $new = $this->sub($string, 0, $length) . '...';
        return $new;
    }

    /**
     * 转小帕斯卡
     * @param $word
     * @param $split
     * @return void
     */
    function toSmallPascal($word, $delimiters = '/')
    {
        if ($word === '') {
            return $word;
        }

        $word = str_replace('_', ' ', $word);
        $word = ucwords($word, $delimiters);
        $word = str_replace($delimiters, '', $word);
        $word = lcfirst($word);

        return $word;
    }

    /**
     * 分隔排序
     * @param $string
     * @param $sort
     * @return void
     */
    function delimiterSort($string, $sort = 'asc', $delimiter = ',')
    {
        if (empty($string)) {
            return $string;
        }

        $array = explode(',', $string);
        if ($sort == 'desc') {
            arsort($array);
        } else {
            sort($array);
        }

        return implode($delimiter, $array);
    }
}