<?php

namespace Sunqianhu\Helper;

use Exception;

class Installer
{
    /**
     * 复制默认配置文件，已有配置不会覆盖
     * @param string|null $configFullRootPath 配置目录，不传则使用宿主项目的 config 目录
     * @throws Exception
     */
    public static function install(?string $configFullRootPath = null): void
    {
        // 1. 确定配置文件要复制到哪个目录
        if ($configFullRootPath === null) {
            $config = new Config();
            $configFullRootPath = $config->getFullRootPath();
        }

        // 2. 目标目录不存在时，创建目录
        if (!is_dir($configFullRootPath)) {
            if (!@mkdir($configFullRootPath, 0755, true) && !is_dir($configFullRootPath)) {
                throw new Exception("无法创建配置文件目录：{$configFullRootPath}");
            }
        }

        // 3. 复制三个配置文件，已有的文件直接跳过
        $files = ['db.php', 'file.php', 'file_log.php'];
        foreach ($files as $file) {
            $targetPath = rtrim($configFullRootPath, '/\\') . '/' . $file;
            if (is_file($targetPath)) {
                continue;
            }
            if (file_exists($targetPath) || is_link($targetPath)) {
                throw new Exception("配置文件目标路径已被占用：{$targetPath}");
            }
            $sourcePath = __DIR__ . '/config/' . $file;
            if (!@copy($sourcePath, $targetPath)) {
                throw new Exception("拷贝配置文件失败：{$sourcePath} > {$targetPath}");
            }
            echo "配置文件安装成功：{$sourcePath} > {$targetPath}" . PHP_EOL;
        }

        echo '配置文件安装完成' . PHP_EOL;
    }
}
