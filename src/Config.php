<?php

namespace sunqianhu\helper;

class Config
{
    /**
     * 获取值
     */
    public function get($name){
        $names = explode('.', $name);
        $data = require(dirname(__DIR__, 4).'/config/'.$names[0].'.php');
        if(count($names) == 1) {
            $value = $data;
        }elseif(count($names) == 2){
            $value = $data[$names[1]];
        }elseif(count($names) == 3){
            $value = $data[$names[1]][$names[2]];
        }else{
            $value = $data[$names[1]][$names[2]][$names[3]];
        }
        return $value;
    }
}