<?php

namespace Sunqianhu\Helper;

class Captcha
{
    /**
     * @var null 画布
     */
    private $image = null;

    /**
     * @var int 宽度
     */
    private $width = 110;

    /**
     * @var int 高度
     */
    private $height = 35;

    /**
     * @var string 验证码
     */
    private $code = '';

    public function __construct()
    {
    }

    /**
     * 创建图片
     */
    public function createImage()
    {
        $this->code = $this->getRandomCode(4);

        $this->image = imagecreate($this->width, $this->height);
        imagecolorallocate($this->image, 255, 255, 255);

        // 干扰点
        $color = imagecolorallocate($this->image, rand(0, 255), rand(0, 255), rand(0, 255));
        for ($i = 0; $i < 200; $i++) {
            imagesetpixel($this->image, rand(0, $this->width), rand(0, $this->height), $color);
        }

        // 验证码
        $color = imagecolorallocate($this->image, rand(0, 150), rand(0, 150), rand(0, 150));
        $font = __DIR__ . '/asset/heiti.ttf';
        imagettftext($this->image, rand(15, 25), rand(-15, 15), rand(5, 10), rand(15, 30), $color, $font, $this->code[0]);
        imagettftext($this->image, rand(15, 25), rand(-15, 15), rand(30, 40), rand(15, 30), $color, $font, $this->code[1]);
        imagettftext($this->image, rand(15, 25), rand(-15, 15), rand(60, 70), rand(15, 30), $color, $font, $this->code[2]);
        imagettftext($this->image, rand(15, 25), rand(-15, 15), rand(80, 90), rand(15, 30), $color, $font, $this->code[3]);

        // 干扰线
        for ($i = 0; $i < 5; $i++) {
            //imageline($this->image, rand(0, $this->width), rand(0, $this->height), rand(0, $this->width), rand(0, $this->height), $color);
        }
        $this->writeCurve($color);

        ob_start();
        imagepng($this->image);
        $content = ob_get_clean();
        imagedestroy($this->image);

        return $content;
    }

    /**
     * 干扰线
     * @return void
     */
    protected function writeCurve($color)
    {
        $px = $py = 0;

        // 曲线前部分
        $A = mt_rand(1, (int)($this->height / 2)); // 振幅
        $b = mt_rand((int)(-$this->height / 4), (int)($this->height / 4)); // Y轴方向偏移量
        $f = mt_rand((int)(-$this->height / 4), (int)($this->height / 4)); // X轴方向偏移量
        $T = mt_rand((int)$this->height, (int)$this->width * 2); // 周期
        $w = (2 * M_PI) / $T;

        $px1 = 0; // 曲线横坐标起始位置
        $px2 = mt_rand((int)($this->width / 2), (int)($this->width * 0.8)); // 曲线横坐标结束位置

        for ($px = $px1; $px <= $px2; $px = $px + 1) {
            if (0 != $w) {
                $py = $A * sin($w * $px + $f) + $b + $this->height / 2; // y = Asin(ωx+φ) + b
                $i = (int)(15 / 5);
                while ($i > 0) {
                    imagesetpixel($this->image, (int)($px + $i), (int)($py + $i), $color); // 这里(while)循环画像素点比imagettftext和imagestring用字体大小一次画出（不用这while循环）性能要好很多
                    $i--;
                }
            }
        }

        // 曲线后部分
        $A = mt_rand(1, (int)($this->height / 2)); // 振幅
        $f = mt_rand((int)(-$this->height / 4), (int)($this->height / 4)); // X轴方向偏移量
        $T = mt_rand((int)$this->height, (int)$this->width * 2); // 周期
        $w = (2 * M_PI) / $T;
        $b = $py - $A * sin($w * $px + $f) - $this->height / 2;
        $px1 = $px2;
        $px2 = $this->width;

        for ($px = $px1; $px <= $px2; $px = $px + 1) {
            if (0 != $w) {
                $py = $A * sin($w * $px + $f) + $b + $this->height / 2; // y = Asin(ωx+φ) + b
                $i = (int)(15 / 5);
                while ($i > 0) {
                    imagesetpixel($this->image, (int)($px + $i), (int)($py + $i), $color);
                    $i--;
                }
            }
        }
    }

    /**
     * 创建base64图片
     * @param $code 验证码
     * @param $this- >width 宽度
     * @param $this- >height 高度
     * @return string
     */
    public function createBase64Image()
    {
        $this->image = $this->createImage();
        return 'data:image/png;base64,' . base64_encode($this->image);
    }

    /**
     * 得到随机字符
     */
    public function getRandomCode($length = 4)
    {
        $codes = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'k', 'm', 'n', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'K', 'M', 'N', 'P', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', '3', '4', '5', '6', '7', '8', '9');
        shuffle($codes);

        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $codes[$i];
        }
        return $code;
    }

    /**
     * 获取验证码
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }
}