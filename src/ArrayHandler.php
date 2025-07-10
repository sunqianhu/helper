<?php

namespace Sunqianhu\Helper;

class ArrayHandler
{
    /**
     * 转换为树数组
     * @param array $datas 数据
     * @return array
     */
    function convertTree($datas, $id = 'id', $parentId = 'parent_id', $children = 'children')
    {
        $middle = array(); //中间数组
        $tree = array(); //树形结构数组

        //重构索引
        foreach ($datas as $data) {
            $middle[$data[$id]] = $data;
        }
        $datas = $middle;
        unset($middle);

        //数组重构
        foreach ($datas as $data) {
            if (isset($datas[$data[$parentId]])) {
                //存在上级
                $datas[$data[$parentId]][$children][] = &$datas[$data[$id]]; //传地址，保证子项也跟到动。
            } else {
                //不存在上级
                $tree[] = &$datas[$data[$id]]; //传地址，保证后续更新datas，tree也被更新。
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
            // 获取两个字段的值
            $value1 = isset($item1[$field]) ? $item1[$field] : null;
            $value2 = isset($item2[$field]) ? $item2[$field] : null;

            // 如果两个值都为null，则认为它们相等
            if ($value1 === null && $value2 === null) {
                return 0;
            }

            // 如果其中一个值不存在，优先考虑存在的那个
            if ($value1 !== null && $value2 === null) {
                return 1;  // $item1 排在 $item2 前面
            } elseif ($value1 === null && $value2 !== null) {
                return -1; // $item1 排在 $item2 后面
            }

            // 处理数值比较
            if (is_numeric($value1) && is_numeric($value2)) {
                if ($value2 == $value1) {
                    return 0;
                }
                return ($value2 > $value1) ? -1 : 1; // 倒序排列
            }

            // 默认使用strcmp进行字符串比较，并明确倒序排列
            return -strcmp((string)$value1, (string)$value2); // 明确倒序排列
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
     * 获取列字符串
     * @param $array
     * @param $column
     * @param $separator
     * @return string
     */
    function getColumnString($array, $column, $separator = ',')
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