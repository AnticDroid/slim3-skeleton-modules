<?php
namespace Blog\Controller;

use Application\Controller\BaseController;

class ArticlesController extends BaseController
{
    public function index()
    {
        $container = $this->app->getContainer();

        $articles = $container->get('blog.model.article')->find([
            //..
        ]);

        $this->render('blog/articles/index', compact('articles'));
    }

    public function show($id)
    {
        $articles = $this->container->get('blog.model.article')->findOneOrFail([
            'id' => (int) $id,
        ]);

        $this->render('blog/articles/show');
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
