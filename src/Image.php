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
        $decodeContent = base64_decode($base64Content);

        $file = new File();
        $relativeDir = $file->makeModuleDir($module);
        $fileName = md5(time() .'_sun_'. rand(1000, 9999)) . '.' . $ext;
        $path = $relativeDir . $fileName; //文件相对路径
        $fullPath = $file->getFullPath($path); //全文件路径

        if (!file_put_contents($fullPath, $decodeContent)) {
            throw new Exception('文件保存到磁盘失败');
        }

        return $path;
    }
}