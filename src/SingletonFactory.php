<?php

namespace Sunqianhu\Helper;

use Exception;

class SingletonFactory
{
    static public $instances = [];

    /**
     * 得到实例
     * @param class-string<T> $class
     * @param ...$args
     * @return T
     * @throws Exception
     * @template T
     */
    static public function getInstance($class, ...$args)
    {
        if (!class_exists($class)) {
            throw new Exception("{$class}类不存在");
        }

        $key = md5($class);
        if (isset(self::$instances[$key])) {
            return self::$instances[$key];
        }

        $instance = new $class(...$args);
        self::$instances[$key] = $instance;
        return $instance;
    }
}