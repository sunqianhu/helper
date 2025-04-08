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

        if ($sourceType !== IMAGETYPE_JPEG && $sourceType !== IMAGETYPE_PNG && $sourceType !== IMAGETYPE_GIF) {
            throw new Exception('此图片类型不支持缩略');
        }

        //创建源图片的资源
        switch ($sourceType) {
            case IMAGETYPE_JPEG:
                $sourceImage = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $sourceImage = imagecreatefrompng($sourcePath);
                // 保持PNG透明度
                imagealphablending($sourceImage, false);
                imagesavealpha($sourceImage, true);
                break;
            case IMAGETYPE_GIF:
                $sourceImage = imagecreatefromgif($sourcePath);
                break;
            default:
                throw new Exception('此图片类型不支持缩略');
        }
        if (!$sourceImage) {
            throw new Exception('原图像画布创建失败');
        }

        //计算缩放比例
        if ($sourceWidth > $sourceHeight) {
            $ratio = $maxSize / $sourceWidth;
        } else {
            $ratio = $maxSize / $sourceHeight;
        }

        $newWidth = intval($sourceWidth * $ratio);
        $newHeight = intval($sourceHeight * $ratio);

        //创建缩略图的资源
        $thumbnailImage = imagecreatetruecolor($newWidth, $newHeight);

        //对于PNG文件，设置透明度处理
        if ($sourceType == IMAGETYPE_PNG) {
            imagealphablending($thumbnailImage, false);
            imagesavealpha($thumbnailImage, true);
            $transparent = imagecolorallocatealpha($thumbnailImage, 255, 255, 255, 127);
            imagefill($thumbnailImage, 0, 0, $transparent);
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
        switch ($sourceType) {
            case IMAGETYPE_JPEG:
                imagejpeg($thumbnailImage, $thumbnailPath, $quality);
                break;
            case IMAGETYPE_PNG:
                imagepng($thumbnailImage, $thumbnailPath);
                break;
            case IMAGETYPE_GIF:
                imagegif($thumbnailImage, $thumbnailPath);
                break;
            default:
                throw new Exception('此图片类型不支持缩略');
        }

        //释放资源
        imagedestroy($thumbnailImage);
        imagedestroy($sourceImage);
    }
}