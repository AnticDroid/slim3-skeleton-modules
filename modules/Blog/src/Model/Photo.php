<?php

namespace Wordup\Model;

use Application\Model\Base;

/**
 *
 */
class Photo extends Base
{
    // collection this model refers to
    protected static $collection = 'photos';

    // define on the fields that can be saved
    protected static $whitelist = array(
        'original_file',
        'type',
        'width',
        'height',
    );

    /**
     * Will generate a new directory based on the date now
     * @return string
     */
    public static function getNewDir()
    {
        return date('Ym/d');
    }

    /**
     * Will return directory of this photo
     * @return string
     */
    public function getDir()
    {
        return date('Ym/d', $this->data['created_at']->sec);
    }

    /**
     * Will return directory of this photo
     * @return string
     */
    public function getCachedDir()
    {
        $datePart = date('Ym/d', $this->data['created_at']->sec);

        return '/' . $datePart . '/' . $this->data['id'];
    }

    /**
     * Will get the path to the cached photo by given dimensions
     * @param string $dim Dimensions to get (e.g. "100x100")
     */
    public function getCachedFileName($dim)
    {
        return $dim . '.jpg';
    }

    /**
     * Will get the path to the cached photo by given dimensions
     * @param string $dim Dimensions to get (e.g. "100x100")
     */
    public function getCachedPath($dim)
    {
        return $this->getCachedDir() . '/' . $this->getCachedFileName($dim);
    }

    /**
     * Will get the path to the cached photo by given dimensions
     * @param string $dim Dimensions to get (e.g. "100x100")
     */
    public function getCachedHref($dim)
    {
        return $dim . '.jpg';
    }

    /**
     */
    public function getOriginalDir()
    {
        $datePart = date('Ym/d', $this->data['created_at']->sec);

        return '/' . $datePart;
    }

    /**
     * Will get the path to the original photo (not cached)
     */
    public function getOriginalFileName()
    {
        return $this->data['original_file'];
    }
}
