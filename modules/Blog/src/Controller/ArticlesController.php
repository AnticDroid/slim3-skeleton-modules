<?php
namespace Blog\Controller;

use Application\Controller\BaseController;

class ArticlesController extends BaseController
{
    public function index()
    {
        // set query
        $query = [];
        if ($search = $this->getQueryParam('search')) {
            $query = array_merge_recursive($query, [
                '$text' => [
                    '$search' => $search,
                ]
            ]);
        }

        // set params
        $limit = (int) $this->getQueryParam('limit', 10);
        $page = (int) $this->getQueryParam('page', 1);
        $skip = $limit * ($page - 1);
        $options = array_intersect_key(array_merge([
            'limit' => $limit,
            'skip' => $skip,
        ], $this->getQueryParams()), array_flip(['limit', 'skip']));

        $articles = $this->get('blog.model.article')->find($query, $options);

        $this->render('blog/articles/index', compact('articles'));
    }

    public function show($id)
    {
        $article = $this->get('blog.model.article')->findOneOrFail([
            'id' => (int) $id,
        ]);

        $otherArticles = $this->get('blog.model.article')->find([
            'id' => [ '$ne' => $article->id ],
        ], [ 'limit' => 5 ]);

        $this->render('blog/articles/show', compact('article', 'otherArticles'));
    }

    // public function create()
    // {
    //     $this->render('blog/articles/create');
    // }
    //
    // public function edit($id)
    // {
    //     $this->render('blog/articles/edit');
    // }

    // /**
    //  * Render the html and attach to the response
    //  * @param string $file Name of the template/ view to render
    //  * @param array $args Additional variables to pass to the view
    //  * @param Response?
    //  */
    // public function render($file, $data=array())
    // {
    //     $container = $this->app->getContainer();
    //
    //     $data = array_merge([
    //         'messages' => $this->get('flash')->flushMessages(),
    //         'currentUser' => $this->get('auth')->getCurrentUser(),
    //         'router' => $this->app->getContainer()->get('router'),
    //     ], $data);
    //
    //     if ($container->has('csrf')) {
    //         $data['csrfName'] = $this->request->getAttribute('csrf_name');
    //         $data['csrfValue'] = $this->request->getAttribute('csrf_value');
    //     }
    //
    //     // generate the html
    //     $html = $container['renderer']->render($file, $data);
    //
    //     // put the html in the response object
    //     $this->response->getBody()->write($html);
    //
    //     return $this->response;
    // }
}
