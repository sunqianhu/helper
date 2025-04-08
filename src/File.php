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
        if($path === ''){
            throw new Exception('路径参数不能为空');
        }
        return Config::get('file.access_prefix') . $path;
    }

    /**
     * 得到全路径
     * @param $path
     * @return string
     */
    public function getFullPath($path){
        if($path === ''){
            throw new Exception('路径参数不能为空');
        }
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
            throw new Exception('只能删除文件');
        }
        unlink($fullPath);
    }

    /**
     * 创建模块目录
     * @param $module
     * @return string
     * @throws Exception
     */
    public function makeModuleDir($module = ''){
        $rootPath = Config::get('file.root_path'); // 磁盘根目录
        if (empty($rootPath)) {
            throw new Exception('没有配置文件目录');
        }

        $relativeDir = '';
        if ($module != '') {
            $relativeDir = $module . '/';
        }
        $relativeDir .= date('Y/m/d') . '/'; // 目录
        $fullDir = $rootPath . $relativeDir;
        if (!file_exists($fullDir)) {
            if (!@mkdir($fullDir, 0755, true)) {
                throw new Exception('目录创建失败');
            }
        }

        return $relativeDir;
    }
}