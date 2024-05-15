<?php

namespace Sunqianhu\Helper;

class Safety
{

    /**
     * 递归使用反斜线引用字符串
     * @param $time
     * @return string
     */
    public function addslashesRecursive($data)
    {
        if (empty($data)) {
            return $data;
        }

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->addslashesRecursive($value);
            }
        } else {
            $data = addslashes($data);
        }

        return $data;
    }

    /**
     * 递归将特殊字符转换为 HTML 实体
     * @param $data
     * @param $excludeKey
     * @return mixed|string
     */
    public function htmlspecialcharsRecursive($data, $excludeKey = ''){
        if(empty($data)){
            return $data;
        }

        $excludeKeys = explode(',', $excludeKey);
        if(is_array($data)){
            foreach ($data as $key => $value){
                if(!empty($excludeKeys)){
                    if(in_array($key, $excludeKeys, true)){
                        continue;
                    }
                }

                $data[$key] = $this->htmlspecialcharsRecursive($value, $excludeKey);
            }
        }else{
            $data = htmlspecialchars($data);
        }

        return $data;
    }
}