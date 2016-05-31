<?php
namespace Application\Controller;

use MartynBiz\Slim3Controller\Controller;

class IndexController extends Controller
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

    /**
     * Render the html and attach to the response
     * @param string $file Name of the template/ view to render
     * @param array $args Additional variables to pass to the view
     * @param Response?
     */
    public function render($file, $data=array())
    {
        $container = $this->app->getContainer();

        // add some additional view vars
        $data = array_merge($data, array(
            'messages' => $this->get('flash')->flushMessages(),
            'currentUser' => $this->get('auth')->getCurrentUser(),
        ));

        // generate the html
        $html = $container['renderer']->render($file, $data);

        // put the html in the response object
        $this->response->getBody()->write($html);

        return $this->response;
    }
}
