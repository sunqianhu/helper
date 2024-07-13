<?php

namespace Sunqianhu\Helper;

use Exception;
use ZipArchive as BaseZipArchive;

class ZipArchive
{
    /**
     * 创建压缩文件
     * @param $fullPath
     * @param $files
     * @return BaseZipArchive
     * @throws Exception
     */
    public function createZip($fullPath, $files){
        if(empty($files)){
            throw new Exception('压缩文件不能为空');
        }
        $zipArchive = new BaseZipArchive();
        $createResult = $zipArchive->open($fullPath, BaseZipArchive::CREATE | BaseZipArchive::OVERWRITE);
        if ($createResult !== true) {
            throw new Exception($createResult);
        }
        foreach ($files as $file) {
            $zipArchive->addFile($file['full_path'], $file['entry_name']);
        }
        $zipArchive->close();
        return $zipArchive;
    }
}