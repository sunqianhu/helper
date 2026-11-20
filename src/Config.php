<?php

namespace Sunqianhu\Helper;

use Composer\InstalledVersions;
use Exception;

class Config
{
    /**
     * 得到以 / 结尾的配置目录完整路径，优先使用 Composer 提供的宿主项目路径
     * @return string
     */
    public function getFullRootPath(): string
    {
        if (class_exists(InstalledVersions::class)) {
            $root = InstalledVersions::getRootPackage();
            if (!empty($root['install_path'])) {
                return $root['install_path'] . '/config/';
            }
        }

        // 兼容不提供 InstalledVersions 的旧版 Composer 标准安装目录
        return dirname(__DIR__, 4) . '/config/';
    }

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
        $rootPath = $this->getFullRootPath();
        $path = $rootPath . $file . '.php';
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
