<?php
/**
 * Handles file uploads
 */

namespace Blog\Controller\Admin;

// use MartynBiz\Mongo\Mongo;
// use Blog\Model\Article;
use Blog\Model\Photo;
// use Blog\Exception\PermissionDenied as PermissionDeniedException;

use Application\Controller\BaseController;

class FilesController extends BaseController
{
    public function upload()
    {
        $container = $this->app->getContainer();
        $settings = $container->get('settings')['modules']['Blog'];

        try {

            if (@$_FILES['name']) {

                // generate the photo dir from the target id
                // we'll use Photo::getCurrentDir to generate the dir from date
                // useful when managing thousands of photos/articles
                // e.g. /var/www/.../data/photos/201601/31/
                $dir = $settings['photos_dir']['original'] . '/' . Photo::getNewDir();
                var_dump($dir); exit;
                $fileExists = $this->get('fs')->fileExists($dir);
                if (!$fileExists and !$this->get('fs')->makeDir($dir, 0775, true)) {
                    throw new \Exception('Could not create directory');
                }

                // loop through photos and create in photos collection
                // also, attach the newly created photo to article
                foreach($photos['name'] as $i => $file) {

                    // get the parameters from the form submission
                    $name = $photos['name'][$i];
                    $tmpName = $photos['tmp_name'][$i];
                    $type = $photos['type'][$i];
                    $ext = pathinfo($name, PATHINFO_EXTENSION);

                    // if the file field is blank, move onto the next field
                    if (empty($file)) continue;

                    // build the file name and path, we'll store the filename in the db
                    $file = sprintf('%s.%s', substr(md5_file($tmpName), 0, 10), strtolower($ext));
                    $destpath = $dir . '/' . $file;

                    // handle the uploaded file
                    $this->get('photo_manager')->moveUploadedFile($tmpName, $destpath, $maxWidth=2000, $maxHeight=2000);

                    // create the photo in collection first so that we have an id to
                    // name the photo by
                    $photo = $this->get('Blog\Model\Photo')->create(array(
                        'original_file' => $file,
                        'type' => $type,
                        'width' => $width,
                        'height' => $height,
                    ));

                    // attach the photo to $article
                    $target->push( array(
                        'photos' => $photo,
                    ) );

                }
            }

            // var_dump($_FILES); exit;
            // // Undefined | Multiple Files | $_FILES Corruption Attack
            // // If this request falls under any of them, treat it invalid.
            // if (
            //     !isset($_FILES['upfile']['error']) ||
            //     is_array($_FILES['upfile']['error'])
            // ) {
            //     throw new RuntimeException('Invalid parameters.');
            // }
            //
            // // Check $_FILES['upfile']['error'] value.
            // switch ($_FILES['upfile']['error']) {
            //     case UPLOAD_ERR_OK:
            //         break;
            //     case UPLOAD_ERR_NO_FILE:
            //         throw new RuntimeException('No file sent.');
            //     case UPLOAD_ERR_INI_SIZE:
            //     case UPLOAD_ERR_FORM_SIZE:
            //         throw new RuntimeException('Exceeded filesize limit.');
            //     default:
            //         throw new RuntimeException('Unknown errors.');
            // }
            //
            // // You should also check filesize here.
            // if ($_FILES['upfile']['size'] > 1000000) {
            //     throw new RuntimeException('Exceeded filesize limit.');
            // }
            //
            // // DO NOT TRUST $_FILES['upfile']['mime'] VALUE !!
            // // Check MIME Type by yourself.
            // $finfo = new finfo(FILEINFO_MIME_TYPE);
            // if (false === $ext = array_search(
            //     $finfo->file($_FILES['upfile']['tmp_name']),
            //     array(
            //         'jpg' => 'image/jpeg',
            //         'png' => 'image/png',
            //         'gif' => 'image/gif',
            //     ),
            //     true
            // )) {
            //     throw new RuntimeException('Invalid file format.');
            // }
            //
            // // You should name it uniquely.
            // // DO NOT USE $_FILES['upfile']['name'] WITHOUT ANY VALIDATION !!
            // // On this example, obtain safe unique name from its binary data.
            // if (!move_uploaded_file(
            //     $_FILES['upfile']['tmp_name'],
            //     sprintf('./uploads/%s.%s',
            //         sha1_file($_FILES['upfile']['tmp_name']),
            //         $ext
            //     )
            // )) {
            //     throw new RuntimeException('Failed to move uploaded file.');
            // }
            //
            // echo 'File is uploaded successfully.';

        } catch (RuntimeException $e) {

            echo $e->getMessage();

        }
    }
}
