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
     * @return void
     */
    function rsortTwoDimensional(&$array, $field){
        usort($array, function($item1, $item2) use($field) {
            $difference = $item2['distance'] - $item1['distance'];
            if($difference>0){
                return 1;
            }else if ($difference<0){
                return -1;
            }
            return 0;
        });
    }

    /**
     * 得到指定key的集合
     * @param $data
     * @param $keys
     * @return void
     */
    function getDesignatedKeyList($data, $keys)
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
}