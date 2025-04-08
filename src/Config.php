<?php

namespace Sunqianhu\Helper;

use Exception;

class Config
{
    /**
     * 得到配置
     * @var array
     */
    private static $cache = [];

    /**
     * 获取配置值
     * @param $name
     * @return array|mixed|null
     * @throws Exception
     */
    public static function get($name)
    {
        $keys = explode('.', $name);
        $file = $keys[0];

        //加载配置
        if (isset(self::$cache[$file])) {
            $fileConfig = self::$cache[$file];
        }else{
            $fileConfig = self::getFileConfig($file);
            self::$cache[$file] = $fileConfig;
        }

        $config = $fileConfig;
        $fields = array_slice($keys, 1);
        foreach ($fields as $field) {
            if(!isset($config[$field])){
                throw new Exception('配置项不存在：' . $field);
            }
            $config = $fileConfig[$field];
        }

        return $config;
    }

    /**
     * 得到文件配置
     * @param $file
     * @return string
     * @throws Exception
     */
    private static function getFileConfig($file)
    {
        $basePath = dirname(__DIR__, 4) . '/config/';
        $path = $basePath . $file . '.php';
        if (!file_exists($path)) {
            throw new Exception('配置文件不存在：' . $path);
        }
        return require $path;
    }
}