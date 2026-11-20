<?php

use Sunqianhu\Helper\Installer;

if (in_array($argv[1] ?? '', ['--help', '-h'], true)) {
    echo '用法：php vendor/bin/sunqianhu_helper_install.php [配置目录]' . PHP_EOL;
    echo '默认安装到宿主项目的 config 目录，保留已有配置文件。' . PHP_EOL;
    exit(0);
}
if ($argc > 2) {
    fwrite(STDERR, '用法：php vendor/bin/sunqianhu_helper_install.php [配置目录]' . PHP_EOL);
    exit(1);
}

// 优先加载宿主项目的自动加载器，最后兼容工具包源码目录中的直接运行
$autoloadPaths = [
    $_composer_autoload_path ?? '', // Composer 2.2+ 提供的宿主项目路径
    __DIR__ . '/../../../autoload.php', // 标准 vendor/sunqianhu/helper/bin 安装目录
    __DIR__ . '/../vendor/autoload.php', // 工具包源码目录中的本地开发入口
];
foreach ($autoloadPaths as $autoloadPath) {
    if ($autoloadPath !== '' && is_file($autoloadPath)) {
        require $autoloadPath;
        break;
    }
}

if (!class_exists(Installer::class)) {
    fwrite(STDERR, '无法加载助手包，请先在宿主项目中通过 Composer 安装 sunqianhu/helper。' . PHP_EOL);
    exit(1);
}

try {
    Installer::install($argv[1] ?? null);
} catch (Throwable $exception) {
    fwrite(STDERR, $exception->getMessage() . PHP_EOL);
    exit(1);
}
