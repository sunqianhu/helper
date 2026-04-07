<?php

namespace Sunqianhu\Helper;

use Exception;

class Image
{
    /**
     * 生成缩略图
     * @param $sourcePath
     * @param $thumbnailPath
     * @param $maxSize
     * @param int $quality
     * @throws Exception
     */
    public function generateThumbnail($sourcePath, $thumbnailPath, $maxSize, $quality = 100)
    {
        //获取原始图片的信息
        list($sourceWidth, $sourceHeight, $sourceType) = getimagesize($sourcePath);
        if (!$sourceWidth || !$sourceHeight) {
            throw new Exception('原图片宽度高度获取失败');
        }
        if (
            $sourceType !== IMAGETYPE_JPEG &&
            $sourceType !== IMAGETYPE_PNG &&
            $sourceType !== IMAGETYPE_GIF
        ) {
            throw new Exception('原图片类型只支持JPG、PNG、GIF格式');
        }

        $thumbnailExt = pathinfo($thumbnailPath, PATHINFO_EXTENSION);
        if(!empty($thumbnailExt)){
            $thumbnailExt = strtolower($thumbnailExt);
        }
        switch ($thumbnailExt) {
            case 'jpg':
            case 'jpeg':
                $thumbnailType = IMAGETYPE_JPEG;
                break;
            case 'png':
                $thumbnailType = IMAGETYPE_PNG;
                break;
            case 'gif':
                $thumbnailType = IMAGETYPE_GIF;
                break;
            default:
                $thumbnailType = $sourceType;
        }

        //缩略图的宽度和高度
        if ($sourceWidth <= $maxSize && $sourceHeight <= $maxSize) {
            if($sourceType === $thumbnailType){
                copy($sourcePath, $thumbnailPath);
                return;
            }
            $newWidth = $sourceWidth;
            $newHeight = $sourceHeight;
        }else{
            //计算缩放比例
            if ($sourceWidth > $sourceHeight) {
                $ratio = $maxSize / $sourceWidth;
            } else {
                $ratio = $maxSize / $sourceHeight;
            }
            $newWidth = intval($sourceWidth * $ratio);
            $newHeight = intval($sourceHeight * $ratio);
        }

        //创建源图片的资源
        $sourceImage = null;
        switch ($sourceType) {
            case IMAGETYPE_JPEG:
                $sourceImage = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $sourceImage = imagecreatefrompng($sourcePath);
                break;
            case IMAGETYPE_GIF:
                $sourceImage = imagecreatefromgif($sourcePath);
                break;
        }
        if (!$sourceImage) {
            throw new Exception('原图像画布创建失败');
        }

        //创建缩略图的资源
        $thumbnailImage = imagecreatetruecolor($newWidth, $newHeight);

        //处理透明背景
        if ($thumbnailType === IMAGETYPE_PNG) {
            /*
             * 关闭颜色混合模式
             * 开启（默认）：你在墙上刷漆，新油漆会和旧油漆混在一起。
             * 关闭（false）：我在墙上刷漆，等漆干了再刷新漆，新漆是什么就是什么，不喝底层颜色混合成新色，是透明就直接透明。
             */
            imagealphablending($thumbnailImage, false);
            //默认情况下，PHP 为了节省内存，保存 PNG 时会把透明信息扔掉。这句代码强制要求：“最后保存文件时，必须把透明信息（Alpha通道）保留下来”。
            imagesavealpha($thumbnailImage, true);
            //为图像分配颜色，分配一个透明色。
            $transparent = imagecolorallocatealpha($thumbnailImage, 255, 255, 255, 127);
            //填充透明色
            imagefill($thumbnailImage, 0, 0, $transparent);
        } elseif ($thumbnailType === IMAGETYPE_GIF) {
            // GIF：GIF 不支持 Alpha 通道，也不能先填充透明色。
            // 它的逻辑是：画布默认是黑色的 -> 我们把黑色指定为“透明色”。
            // 关闭混合模式，防止复制时颜色混合
            imagealphablending($thumbnailImage, false);
            // 获取画布默认的黑色索引
            $black = imagecolorallocate($thumbnailImage, 0, 0, 0);
            // 将黑色设置为透明色
            imagecolortransparent($thumbnailImage, $black);
        }else{
            $white = imagecolorallocate($thumbnailImage, 255, 255, 255);
            imagefilledrectangle($thumbnailImage, 0, 0, $newWidth, $newHeight, $white);
        }

        //将原始图片缩放到缩略图尺寸
        imagecopyresampled(
            $thumbnailImage,
            $sourceImage,
            0,
            0,
            0,
            0,
            $newWidth,
            $newHeight,
            $sourceWidth,
            $sourceHeight
        );

        //保存缩略图
        switch ($thumbnailType) {
            case IMAGETYPE_JPEG:
                imagejpeg($thumbnailImage, $thumbnailPath, $quality);
                break;
            case IMAGETYPE_PNG:
                imagepng($thumbnailImage, $thumbnailPath);
                break;
            case IMAGETYPE_GIF:
                imagegif($thumbnailImage, $thumbnailPath);
                break;
        }

        //释放资源
        imagedestroy($thumbnailImage);
        imagedestroy($sourceImage);
    }

    /**
     * 保存base64内容到文件
     * @param $module
     * @param $content
     * @return string
     * @throws Exception
     */
    public function saveBase64ContentToFile($module, $content)
    {
        $allowMimeTypes = [
            'image/jpg',
            'image/jpeg',
            'image/png',
            'image/gif'
        ];
        $extMap = [
            'image/jpg'=>'jpg',
            'image/jpeg'=>'jpeg',
            'image/png'=>'png',
            'image/gif'=>'gif'
        ];
        if (empty($content)) {
            throw new Exception('图片base64内容不能为空');
        }
        $contents = explode(';base64,', $content);
        $mimeType = $contents[0];
        $mimeType = str_replace('data:', '', $mimeType);
        if(empty($mimeType)){
            throw new Exception('没有获取到图片的mime类型');
        }
        if(!in_array($mimeType, $allowMimeTypes)){
            throw new Exception('此图片mime类型不支持');
        }
        $ext = $extMap[$mimeType];
        $base64Content = $contents[1] ?? '';
        if(empty($base64Content)){
            throw new Exception('图片内容不能为空');
        }
        $decodeContent = base64_decode($base64Content);

        $file = new File();
        $path = $file->makeModuleFilePath($module, $ext);
        $fullPath = $file->getFullPath($path); //全文件路径

        if (!file_put_contents($fullPath, $decodeContent)) {
            throw new Exception('文件保存到磁盘失败');
        }

        return $path;
    }
}