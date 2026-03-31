<?php

namespace Sunqianhu\Helper;

use Exception;

class Config
{
    /**
     * 缓存
     * @var array
     */
    private $cache = [];

    /**
     * 得到配置
     * @param $name
     * @param null $default
     * @return array|mixed|null
     * @throws Exception
     */
    public function get($name, $default = null)
    {
        $keys = explode('.', $name);
        $file = array_shift($keys);

        //加载配置
        $config = $this->getFileCacheConfig($file);
        if(empty($keys)){
            return $config;
        }

        foreach ($keys as $key) {
            if(!isset($config[$key])){
                return $default;
            }
            $config = $config[$key];
        }

        return $config;
    }

    /**
     * 设置文件缓存配置
     * @param $file
     * @return void
     * @throws Exception
     */
    public function setFileCacheConfig($file)
    {
        $basePath = dirname(__DIR__, 4) . '/config/';
        $path = $basePath . $file . '.php';
        if (!file_exists($path)) {
            throw new Exception('配置文件不存在：' . $path);
        }
        $this->cache[$file] = require $path;
    }

    /**
     * 得到文件缓存
     * @param $file
     * @return mixed
     * @throws Exception
     */
    public function getFileCacheConfig($file)
    {
        if(isset($this->cache[$file])){
            return $this->cache[$file];
        }
        $this->setFileCacheConfig($file);

        return $this->cache[$file];
    }
}