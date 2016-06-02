<?php
namespace Application\Controller;

class IndexController extends BaseController
{
    public function index()
    {
        return $this->render('index/index');
    }

    public function portfolio()
    {
        return $this->render('index/portfolio');
    }

    public function contact()
    {
        if ($this->request->isPost()) {

        }

        return $this->render('index/contact');
    }

    // /**
    //  * Render the html and attach to the response
    //  * @param string $file Name of the template/ view to render
    //  * @param array $args Additional variables to pass to the view
    //  * @param Response?
    //  */
    // public function render($file, $args=array())
    // {
    //     $container = $this->app->getContainer();
    //
    //     // this will ensure that $data is available to all templates
    //     $container['renderer']->addData(array(
    //         'messages' => $this->get('flash')->flushMessages(),
    //         'currentUser' => $this->get('auth')->getCurrentUser(),
    //     ));
    //
    //     // generate the html
    //     $html = $container['renderer']->render($file, $args);
    //
    //     // put the html in the response object
    //     $this->response->getBody()->write($html);
    //
    //     return $this->response;
    // }
}
