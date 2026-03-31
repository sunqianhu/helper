<?php

namespace Sunqianhu\Helper;

use Exception;
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
        $zipArchive = new ZipArchive();
        $createResult = $zipArchive->open($fullPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if ($createResult !== true) {
            throw new Exception($createResult);
        }
        foreach ($files as $file) {
            $zipArchive->addFile($file['full_path'], $file['name']);
        }
        $zipArchive->close();
        return $zipArchive;
    }
}