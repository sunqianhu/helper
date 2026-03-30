<?php

namespace Sunqianhu\Helper;

use Exception;

class File
{
    /**
     * 得到访问url
     * @param string $path 路径
     * @throws Exception
     */
    public function getUrl($path)
    {
        return Config::get('file.access_prefix') . $path;
    }

    /**
     * 得到全路径
     * @param $path
     * @return string
     * @throws Exception
     */
    public function getFullPath($path){
        return Config::get('file.root_path') . $path;
    }

    /**
     * 删除文件
     * @param $path
     * @return void
     * @throws Exception
     */
    public function unlinkFile($path){
        if($path === ''){
            throw new Exception('路径参数不能为空');
        }
        $fullPath = $this->getFullPath($path);
        if(!file_exists($fullPath)){
            throw new Exception('文件不存在');
        }
        if(!is_file($fullPath)){
            throw new Exception('不是一个文件');
        }
        unlink($fullPath);
    }

    /**
     * 创建模块目录路径
     * @param $module
     * @return string
     * @throws Exception
     */
    public function makeModuleDirPath($module = ''){
        $path = '';
        if ($module != '') {
            $path = $module . '/';
        }
        $path .= date('Y/m/d') . '/'; // 目录

        $fullPath = $this->getFullPath($path);
        if (!file_exists($fullPath)) {
            if (!mkdir($fullPath, 0755, true)) {
                throw new Exception('目录创建失败');
            }
        }

        return $path;
    }

    /**
     * 创建模块文件路径
     * @param string $module
     * @param string $ext
     * @return string
     * @throws Exception
     */
    public function makeModuleFilePath($module = '', $ext = '')
    {
        $dirPath = $this->makeModuleDirPath($module);
        $fileName = md5(time().'_sun_'.rand(1000, 9999));
        if($ext !== ''){
            $fileName .= '.'.$ext;
        }
        $path = $dirPath . $fileName;
        return $path;
    }
}