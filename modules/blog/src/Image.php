<?php

/**
 * A mockable replacement for gd image functions
 */
namespace Wordup;

class Image
{
    /**
     * Get the size of an image
     * @see http://php.net/manual/en/function.getimagesize.php
     */
    public function getImageSize($filename)
    {
        return getimagesize($filename);
    }

    /**
     * Create a new true color image
     * @see http://php.net/manual/en/function.imagecreatetruecolor.php
     */
    public function createTrueColorImage($width, $height)
    {
        return imagecreatetruecolor($width, $height);
    }

    /**
     * Create a new image from file or URL
     * @see http://php.net/manual/en/function.imagecreatetruecolor.php
     */
    public function createImageFromJpeg($filename)
    {
        return imagecreatefromjpeg($filename);
    }

    /**
     * Copy and resize part of an image with resampling
     * @see http://php.net/manual/en/function.imagecopyresampled.php
     */
    public function copyImageWithResampling($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w , $src_h)
    {
        return imagecopyresampled($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w , $src_h);
    }

    /**
     * @see http://php.net/manual/en/function.imagejpeg.php
     */
    public function outputJpeg($image, $filename=null, $quality=90)
    {
        return imagejpeg($image, $filename, $quality);
    }

    /**
     * Destroy an image
     * @see http://php.net/manual/en/function.imagedestroy.php
     */
    public function destroyImage($image)
    {
        return imagedestroy($image);
    }
}
