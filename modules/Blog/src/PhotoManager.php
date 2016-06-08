<?php
/**
 * A tool for handling the heavy lifting of storing uploaded photos and
 * generating photo paths
 */

namespace Blog;

use Blog\Image;
use Blog\FileSystem;

class PhotoManager
{
    /**
     * @var Wordup\Image
     */
    protected $image;

    /**
     * @var Wordup\FileSystem
     */
    protected $fsService;

    /**
     * @var array
     */
    protected $settings;

    /**
     * Get the size of an image
     * @see http://php.net/manual/en/function.getimagesize.php
     */
    public function __construct(Image $imageService, FileSystem $fsService, $settings=array())
    {
        $this->imageService = $imageService;
        $this->fsService = $fsService;

        $this->settings = $settings;
    }

    /**
     * Get the re-calculated width/height based on max w/h
     * @param int $widthOrig
     * @param int $heightOrig
     * @param int $maxWidth Max width to recalculate to
     * @param int $maxHeight Max height to recalculate to
     */
    public function getMaxWidthHeight($widthOrig, $heightOrig, $maxWidth, $maxHeight=null)
    {
        // if $maxHeight is null, set to $maxWidth
        $maxHeight or $maxHeight = $maxWidth;

        // Set a maximum height and width
        $ratioOrig = $widthOrig/$heightOrig;

        if ($maxWidth/$maxHeight > $ratioOrig) {
           $maxWidth = ceil($maxHeight*$ratioOrig);
        } else {
           $maxHeight = ceil($maxWidth/$ratioOrig);
        }

        return [$maxWidth, $maxHeight];
    }

    /**
     * Create a new true color image
     * @param string $srcPath
     * @param string $destPath
     * @return void
     */
    public function moveUploadedFile($srcPath, $destPath, $maxWidth=null, $maxHeight=null)
    {
        $imageService = $this->imageService;

        // get the dimensions so we can calculate the width/height ratio
        // throw an exception if this fails
        list($widthOrig, $heightOrig) = $imageService->getImageSize($srcPath);
        if (!$widthOrig or !$heightOrig)
            throw new \Exception('Could not get image size from uploaded image.');

        // Set a maximum height and width
        list($maxWidth, $maxHeight) = $this->getMaxWidthHeight($widthOrig, $heightOrig, $maxWidth, $maxHeight);

        // Create a new image from the uploaded file
        $src = $imageService->createImageFromJpeg($srcPath);
        if (!$src)
            throw new \Exception('Only JPEG images are allowed for photos.');

        // Create a new true color image and copy and resize part of an image
        // with resampling
        $tmp = $imageService->createTrueColorImage($maxWidth, $maxHeight);
        $imageService->copyImageWithResampling($tmp, $src, 0, 0, 0, 0, $maxWidth, $maxHeight, $widthOrig, $heightOrig);

        $imageService->outputJpeg($tmp, $destPath);
        $imageService->destroyImage($tmp);
    }

    /**
     * Create a new true color image
     * @param string $origPath
     * @param string $cachedPath
     * @param int $width Resulting cached image width
     * @param int $height Resulting cached image height
     * @return void
     */
    public function createCacheImage($origPath, $cachedPath, $width, $height)
    {
        // get the dimensions so we can calculate the width/height ratio
        // throw an exception if this fails
        list($width_orig, $height_orig) = $this->imageService->getImageSize($origPath);
        if (!$width_orig or !$height_orig)
            throw new \Exception('Could not get image size from uploaded image.');

        // calculate new image size with ratio if exceeds max
        // TODO put this into Photo as static, unit test
        $ratio_orig = $width_orig/$height_orig;

        // check dimensions are valid
        // valid dimansions are:
        //   - 100x100 (set both height and width)
        //   - 100x (set width, calculate height)
        //   - x100 (set height, calculate width)

        if (empty($width) and empty($height)) {
            throw new \Exception('Photo dimensions not defined properly.');
        } elseif (empty($width)) {
            $width = ceil($height*$ratio_orig);
        } elseif (empty($height)) {
            $height = ceil($width/$ratio_orig);
        }

        // Create a new image from the uploaded file
        $src = $this->imageService->createImageFromJpeg($origPath);
        if (!$src)
            throw new \Exception('Only JPEG images are allowed for photos.');

        // Create a new true color image and copy and resize part of an image
        // with resampling
        $tmp = $this->imageService->createTrueColorImage($width, $height);
        $this->imageService->copyImageWithResampling($tmp, $src, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);

        // generate the photo dir from the target id
        // we'll use Photo::getCurrentDir to generate the dir from date
        // useful when managing thousands of photos/articles
        // e.g. /var/www/.../data/photos/201601/31/
        $cachedDir = pathinfo($cachedPath, PATHINFO_DIRNAME);
        $fileExists = $this->fsService->fileExists($cachedDir);
        if (!$fileExists and !$this->fsService->makeDir($cachedDir, 0775, true)) {
            throw new \Exception('Could not create directory');
        }

        // write the cached file to disk, destroy image
        $this->imageService->outputJpeg($tmp, $cachedPath);
        $this->imageService->destroyImage($tmp);
    }
}
