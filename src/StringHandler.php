<?php

namespace Sunqianhu\Helper;

class StringHandler
{
    /**
     * 字符串长度
     * @param string $string 字符串
     * @return int 字符串长度
     */
    function length($string)
    {
        $match = array();
        $length = 0;
        if ($string === '') {
            return $length;
        }

        if (function_exists('mb_utf8length')) {
            $length = mb_utf8length($string, 'utf-8');
        } else {
            preg_match_all('/./u', $string, $match);
            $length = count($match[0]);
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
        $match = array();

        if ($string === '') {
            return $new;
        }

        if (function_exists('mb_substr')) {
            $new = mb_substr($string, $start, $length, 'utf-8');
        } else {
            preg_match_all('/./u', $string, $match);
            $new = join('', array_slice($match[0], $start, $length));
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
        if ($string === '') {
            return $string;
        }
        $total = $this->length($string);
        if ($total <= $length) {
            return $string;
        }
        return $this->sub($string, 0, $length) . '...';
    }

    /**
     * 转小帕斯卡
     * @param $word
     * @param string $delimiters
     * @return string
     */
    function toSmallPascal($word, $delimiters = '/')
    {
        if ($word === '') {
            return $word;
        }

        $word = str_replace('_', ' ', $word);
        $word = ucwords($word, $delimiters);
        $word = str_replace($delimiters, '', $word);
        return lcfirst($word);
    }

    /**
     * 分隔排序
     * @param $string
     * @param string $sort
     * @param string $delimiter
     * @param string $glue
     * @return string
     */
    function delimiterSort($string, $sort = 'asc', $delimiter = ',', $glue = ',')
    {
        if (empty($string)) {
            return $string;
        }

        $array = explode($delimiter, $string);
        if ($sort == 'desc') {
            arsort($array);
        } else {
            sort($array);
        }

        return implode($glue, $array);
    }
}