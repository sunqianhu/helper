<?php

namespace sunqianhu\helper;

class Image
{
    /**
     * 缩略图
     * @param $sourcePath 原图片路径
     * @param $thumbnailPath 缩略图路径
     * @param $width 缩略到的宽度
     * @param $height 缩略到的高度
     * @param $quality 图片质量，当图片是png或gif时无效
     */
    public function thumbnail($sourcePath, $thumbnailPath, $width, $height, $quality = 100)
    {
        // 获取原始图片的信息
        list($sourceWidth, $sourceHeight, $sourceType) = getimagesize($sourcePath);

        // 创建源图片的资源
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
            default:
                throw new Exception('此图片类型不支持缩略');
        }

        if (!$sourceImage) {
            throw new Exception('原图像画布创建失败');
        }

        // 创建缩略图的资源
        $thumbnailImage = imagecreatetruecolor($width, $height);

        // 将原始图片缩放到缩略图尺寸
        imagecopyresampled(
            $thumbnailImage,
            $sourceImage,
            0,
            0,
            0,
            0,
            $width,
            $height,
            $sourceWidth,
            $sourceHeight
        );

        // 保存缩略图
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

        // 释放资源
        imagedestroy($thumbnailImage);
        imagedestroy($sourceImage);
    }
}