<?php
namespace Blog\Controller\Admin;

use Blog\Utils;
use Blog\Exception\PermissionDenied;

use Blog\Controller\BaseController;

class DataController extends BaseController
{
    // public function init()
    // {
    //     // only admin can do anything here
    //     $currentUser = $this->get('auth')->getCurrentUser();
    //     if (! $currentUser->isAdmin() ) {
    //         throw new PermissionDenied('Permission denied to manage tags.');
    //     }
    // }

    public function import($request, $response, $args)
    {
        if ($this->request->isPost()) {

            // Wipe the collections so we can start new (optional)
            // TODO make these optional
            $this->get('model.article')->remove( array() );
            $this->get('model.tag')->remove( array() );

            $file = @$_FILES['file']['tmp_name'];

            if (!$file) {

                $this->get('flash')->addMessage('errors', 'File missing.');

            } else {

                // get string xml from file
                $xml = $this->get('fs')->getFileContents($file);
                $encoding = mb_detect_encoding($xml);
                $xml = mb_convert_encoding($xml, 'HTML-ENTITIES', $encoding);

                $dom = new \DOMDocument();
                $dom->preserveWhiteSpace = false;
                $dom->loadXML($xml);

                // TODO set author
                $author = $this->get('auth')->getCurrentUser();

                // $xmlPath = new \DOMXPath($dom);
                // $items = $xmlPath->query('*/item');
                $items = $dom->getElementsByTagName('item');
                foreach ($items as $item) {
                    $childNodes = $item->childNodes;

                    // get values from xml
                    $title = $childNodes->item(0)->nodeValue;
                    $content = $childNodes->item(6)->nodeValue;
                    $publishedAt = new \MongoDate( strtotime($childNodes->item(9)->nodeValue ) );

                    // additional values
                    $slug = Utils::slugify($title);
                    $status = Article::STATUS_APPROVED; // TODO set this from xml
                    $type = Article::TYPE_ARTICLE;

                    $article = $this->get('model.article')->factory(array(
                        'title' => $title,
                        'content' => $content,
                        'published_at' => $publishedAt,
                    ));

                    $article->status = Article::STATUS_APPROVED;
                    $article->type = Article::TYPE_ARTICLE;
                    $article->author = $author;

                    $article->save();

                    // import tags

                    // extract tags from xml
                    $categories = $item->getElementsByTagName('category');
                    $tags = array();
                    foreach($categories as $category) {
                        array_push($tags, "{$category->nodeValue}");
                    }
                    $tags = array_unique($tags);

                    // find or create tag in database
                    foreach ($tags as $name) {

                        // get tag from the database by this name
                        $tag = $this->get('model.tag')->findOne(array(
                            'name' => $name,
                        ));

                        // if tag doesn't exist, create one
                        if (!$tag) {
                            $tag = $this->get('model.tag')->create(array(
                                'name' => $name,
                                'slug' => Utils::slugify($name),
                            ));
                        }

                        $article->push(array(
                            'tags' => $tag,
                        ));
                    }
                }

                $this->get('flash')->addMessage('success', 'Articles imported successfully.');

                return $this->redirect('/admin/data/import');

            }
        }

        return $this->render('admin.data.import');
    }
}
