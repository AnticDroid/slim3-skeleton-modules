<?php
/**
 * A mockable replacement for gd image functions of this slim3-module
 */

namespace Blog;

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
     * Get the type of an image
     * @see http://php.net/manual/en/function.exif-imagetype.php
     */
    public function getImageType($filename)
    {
        return exif_imagetype($filename);
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
     * @see http://php.net/manual/en/function.imagecreatefromjpeg.php
     */
    public function createImageFromJpeg($filename)
    {
        return imagecreatefromjpeg($filename);
    }

    /**
     * Create a new image from file or URL
     * @see http://php.net/manual/en/function.imagecreatefrompng.php
     */
    public function createImageFromPng($filename)
    {
        return imagecreatefrompng($filename);
    }

    /**
     * Create a new image from file or URL
     * @see http://php.net/manual/en/function.imagecreatefromgif.php
     */
    public function createImageFromGif($filename)
    {
        return imagecreatefromgif($filename);
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
     * @see http://php.net/manual/en/function.imagepng.php
     */
    public function outputPng($image, $filename=null, $quality=90)
    {
        return imagepng($image, $filename, $quality);
    }

    /**
     * @see http://php.net/manual/en/function.imagegif.php
     */
    public function outputGif($image, $filename=null, $quality=90)
    {
        return imagegif($image, $filename, $quality);
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
