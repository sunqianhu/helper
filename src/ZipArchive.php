<?php

namespace sunqianhu\helper;

use Exception;
use ZipArchive as BaseZipArchive;

class ZipArchive
{
    /**
     * 创建压缩文件
     * @param $fullPath
     * @param $files
     * @return void
     * @throws Exception
     */
    public function createZip($fullPath, $files){
        if(empty($files)){
            throw new Exception('压缩文件不能为空');
        }
        $baseZipArchive = new BaseZipArchive();
        $createResult = $baseZipArchive->open($fullPath, BaseZipArchive::CREATE | BaseZipArchive::OVERWRITE);
        if ($createResult !== true) {
            throw new Exception($createResult);
        }
        foreach ($files as $file) {
            $baseZipArchive->addFile($file['full_path'], $file['entry_name']);
        }
        $baseZipArchive->close();
    }
}