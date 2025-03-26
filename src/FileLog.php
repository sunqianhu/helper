<?php

namespace Sunqianhu\Helper;

use Exception;

class FileLog
{
    private $fullPath;

    /**
     * 构造函数
     * @param $config
     * @throws Exception
     */
    public function __construct($config = [])
    {
        if (empty($config)) {
            $config = new Config();
            $config = $config->get('file_log');
        }
        if(empty($config['full_path'])){
            throw new Exception('请配置文件日志路径');
        }

        $this->fullPath = $config['full_path'];
    }

    /**
     * 消息日志
     */
    public function info($content){
        $content = date('Y-m-d H:i:s').' info '.$content.PHP_EOL;
        file_put_contents($this->fullPath, $content, FILE_APPEND);
    }

    /**
     * 预警日志
     * @param $content
     * @return void
     */
    public function warning($content)
    {
        $content = date('Y-m-d H:i:s').' warning '.$content.PHP_EOL;
        file_put_contents($this->fullPath, $content, FILE_APPEND);
    }

    /**
     * 错误日志
     * @param $content
     * @return void
     */
    public function error($content){
        $content = date('Y-m-d H:i:s').' error '.$content.PHP_EOL;
        file_put_contents($this->fullPath, $content, FILE_APPEND);
    }

    /**
     * 成功日志
     * @param $content
     * @return void
     */
    public function success($content){
        $content = date('Y-m-d H:i:s').' success '.$content.PHP_EOL;
        file_put_contents($this->fullPath, $content, FILE_APPEND);
    }
}