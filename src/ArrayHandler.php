<?php

namespace Sunqianhu\Helper;

class ArrayHandler
{
    /**
     * 转换为树数组
     * @param array $data 数据
     * @return array
     */
    function convertTree($data, $id = 'id', $parentId = 'parent_id', $children = 'children')
    {
        $middle = array(); //中间数组
        $tree = array(); //树形结构数组

        //重构索引
        foreach ($data as $item) {
            $middle[$item[$id]] = $item;
        }
        $data = $middle;
        unset($middle);

        //数组重构
        foreach ($data as $item) {
            if (isset($data[$item[$parentId]])) {
                //存在上级
                $data[$item[$parentId]][$children][] = &$data[$item[$id]]; //传地址，保证子项也跟到动。
            } else {
                //不存在上级
                $tree[] = &$data[$item[$id]]; //传地址，保证后续更新datas，tree也被更新。
            }
        }

        return $tree;
    }

    /**
     * 按某字段降序排序二维数组
     * @param $array
     * @param $field
     * @return bool
     */
    function twoDimensionalRsort(&$array, $field) {
        return usort($array, function($item1, $item2) use($field) {
            $value1 = isset($item1[$field]) ? $item1[$field] : null;
            $value2 = isset($item2[$field]) ? $item2[$field] : null;

            if ($value1 === null && $value2 === null) {
                return 0;
            }else if ($value1 !== null && $value2 === null) {
                return -1;
            } elseif ($value1 === null && $value2 !== null) {
                return 1;
            }

            if (is_numeric($value1) && is_numeric($value2)) {
                if ($value1 == $value2) {
                    return 0;
                }
                return $value1 > $value2 ? -1 : 1; // 倒序排列
            }

            return -strcmp((string)$value1, (string)$value2);
        });
    }

    /**
     * 得到指定keys的集合
     * @param $data
     * @param $keys
     * @return array
     */
    function getDesignatedKeysList($data, $keys)
    {
        $list = [];
        foreach ($keys as $key => $value) {
            if (!is_numeric($key)) {
                if (isset($data[$key])) {
                    $list[$key] = $data[$key];
                } else {
                    $list[$key] = $value;
                }
            } else {
                if (isset($data[$value])) {
                    $list[$value] = $data[$value];
                }
            }
        }
        return $list;
    }

    /**
     * 获取多列的字符串表示
     * @param $array
     * @param $column
     * @param $separator
     * @return string
     */
    function getColumnsString($array, $column, $separator = ',')
    {
        if (!is_array($array)) {
            return '';
        }
        if (!isset($array[0][$column])) {
            return '';
        }
        return implode($separator, array_column($array, $column));
    }
}