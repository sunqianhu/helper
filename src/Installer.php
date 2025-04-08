<?php

namespace Sunqianhu\Helper;

use Exception;

class Installer
{
    /**
     * 安装
     * @throws Exception
     */
    public static function install()
    {
        $vendorFullPath = dirname(__DIR__, 4);
        $configFullPath = $vendorFullPath . '/config';
        if (!is_dir($configFullPath)) {
            if (!mkdir($configFullPath, 0755) && !is_dir($configFullPath)) {
                throw new Exception("无法创建配置文件目录：{$configFullPath}");
            }
        }

        //数据库
        $targetDbPath = $configFullPath . '/databases.php';
        if(!file_exists($targetDbPath)){
            $sourceDbPath = __DIR__ . '/config/databases.php';
            if(!copy($sourceDbPath, $targetDbPath)){
                throw new Exception("拷贝数据库配置文件失败：{$sourceDbPath} > {$targetDbPath}");
            }
        }

        //文件
        $targetFileLogPath = $configFullPath . '/file.php';
        if(!file_exists($targetFileLogPath)){
            $sourceFileLogPath = __DIR__ . '/config/file.php';
            if(!copy($sourceFileLogPath, $targetFileLogPath)){
                throw new Exception("拷贝文件配置失败：{$sourceFileLogPath} > {$targetFileLogPath}");
            }
        }

        //文件日志
        $targetFileLogPath = $configFullPath . '/file_log.php';
        if(!file_exists($targetFileLogPath)){
            $sourceFileLogPath = __DIR__ . '/config/file_log.php';
            if(!copy($sourceFileLogPath, $targetFileLogPath)){
                throw new Exception("拷贝日志配置文件失败：{$sourceFileLogPath} > {$targetFileLogPath}");
            }
        }

        echo '配置文件安装成功';
    }
}