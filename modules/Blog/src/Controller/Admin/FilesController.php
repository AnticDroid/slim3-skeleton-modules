<?php
/**
 * Handles file uploads
 */

namespace Blog\Controller\Admin;

use Blog\Model\Photo;

use Application\Controller\BaseController;

class FilesController extends BaseController
{
    public function upload()
    {
        $params = array_merge($this->getQueryParams(), $this->getPost());
        $container = $this->app->getContainer();
        $settings = $container->get('settings')['modules']['Blog'];

        $photoParams = $_FILES['upload'];

        try {

            // // generate the photo dir from the target id
            // // we'll use Photo::getCurrentDir to generate the dir from date
            // // useful when managing thousands of photos/articles
            // // e.g. /var/www/.../data/photos/201601/31/
            // $dir = $settings['photos_dir']['original'];
            //
            // $fileExists = $this->get('blog.FileSystem')->fileExists($dir);
            // if (!$fileExists and !$this->get('blog.FileSystem')->makeDir($dir, 0775, true)) {
            //     throw new \Exception('Could not create directory');
            // }

            // get the parameters from the form submission
            $name = $photoParams['name'];
            $tmpName = $photoParams['tmp_name'];
            $type = $photoParams['type'];
            $ext = pathinfo($name, PATHINFO_EXTENSION);

            // e.g. 12345.jpg
            $file = sprintf('%s.%s', substr(md5_file($tmpName), 0, 10), strtolower($ext));

            // e.g. path/to/12345.jpg
            $filepath = Photo::getNewDir() . '/' . $file;

            // e.g. /var/www/app/photos/path/to/12345.jpg
            $backendPath = $settings['photos_dir']['original'] . '/' . $filepath;

            $fileExists = $this->get('blog.file_system')->fileExists( dirname($backendPath) );
            if (!$fileExists and !$this->get('blog.file_system')->makeDir(dirname($backendPath), 0775, true)) {
                throw new \Exception('Could not create directory');
            }

            // handle the uploaded file
            if ($this->get('blog.photo_manager')->moveUploadedFile($tmpName, $backendPath, $maxWidth=2000, $maxHeight=2000)) {

                // create the photo in collection first so that we have an id to
                // name the photo by
                $photo = $this->get('blog.model.photo')->create(array(
                    'filepath' => $filepath,
                    'type' => $type,
                    // 'width' => $width,
                    // 'height' => $height,
                ));

                // attach the photo to the current article
                $articleId = $this->get('session')->get('current_article_id');
                if ($articleId) {
                    $article = $this->get('blog.model.article')->findOneOrFail(array(
                        'id' => (int) $articleId,
                    ));

                    // attach the photo to $article
                    $article->push( array(
                        'photos' => $photo,
                    ) );
                }

                // the path to the file name from a front end (e.g. /images/...)
                $imageSize = $this->get('blog.image')->getImageSize($backendPath);
                list($width, $height) = $this->get('blog.photo_manager')->getMaxWidthHeight($imageSize[0], $imageSize[1], 300);
                $frontendPath = $settings['photos_dir']['public'] . $photo->getCachedDir() . '/' . $photo->getCachedFileName( sprintf('%sx%s', $width, $height) );

                $message = 'File uploaded successfully';

            } else {
                throw new \Exception('Could not copy file to destination');
            }

        } catch (\Exception $e) {

            $message = $e->getMessage();

        }

        return $this->response->getBody()->write('<script type="text/javascript">window.parent.CKEDITOR.tools.callFunction(' . $params['CKEditorFuncNum'] . ', "' . $frontendPath . '", "' . $message . '");</script>');
    }
}
