<?php

namespace Sunqianhu\Helper;

use Exception;
use Throwable;
use ZipArchive;

class ZipArc
{
    /**
     * 创建压缩文件
     * @param $fullPath
     * @param $files
     * @return ZipArchive
     * @throws Exception
     */
    public function createZip($fullPath, $files){
        if(empty($files)){
            throw new Exception('压缩文件不能为空');
        }
        foreach ($files as $file) {
            if (!is_file($file['full_path']) || !is_readable($file['full_path'])) {
                throw new Exception('压缩源文件不存在或不可读：' . $file['full_path']);
            }
        }
        $zipArchive = new ZipArchive();
        $createResult = $zipArchive->open($fullPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if ($createResult !== true) {
            throw new Exception($createResult);
        }
        try {
            foreach ($files as $file) {
                $zipArchive->addFile($file['full_path'], $file['name']);
            }
        } catch (Throwable $exception) {
            $zipArchive->close();
            throw $exception;
        }
        if (!$zipArchive->close()) {
            throw new Exception('压缩文件保存失败');
        }
        return $zipArchive;
    }
}
