<?php
namespace Blog\Controller;

use Application\Controller\BaseController;

class PhotosController extends BaseController
{
    /**
     * Will fetch a cached photo from the orginal photos
     * Photo will be fetched by the params in the in URL (routed to here)
     * @param string
     */
    public function cached($path)
    {
        $container = $this->app->getContainer();
        $settings = $container->get('settings');
        $moduleSettings = $settings['modules']['Blog'];

        // get the params from the $path (mainly just $id and $dim)
        // set the width and height from the dim string
        list($ym, $d, $id, $dim) = explode('/', $path);
        list($width, $height) = explode('x', $dim);

        // get photo by id
        $photo = $this->get('Blog\Model\Photo')->findOne(array(
            'id' => (int) $id,
        ));

        $cachedDir = $moduleSettings['photos_dir']['cache'] . $photo->getCachedDir();
        $cachedPath = $cachedDir . '/' . $photo->getCachedFileName($dim);

        // check if cached file exists for this photo
        // if (!$this->get('fs')->fileExists($cachedPath)) { // TODO uncomment this

            // this will generate a path to the cached file eg. 201601/31/100x100.jpg
            $origDir = $moduleSettings['photos_dir']['original'] . $photo->getOriginalDir();
            $origPath = $origDir . '/' . $photo->getOriginalFileName($dim);

            $this->get('Blog\PhotoManager')->createCacheImage($origPath, $cachedPath, $width, $height);
        // }

        // display image to browser
        $this->get('Blog\FileSystem')->readFile($cachedPath);

        // set content type
        return $container['response']->withHeader('Content-type', 'image/jpeg');
    }
}
