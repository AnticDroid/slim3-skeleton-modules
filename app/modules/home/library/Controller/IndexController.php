<?php
namespace App\Modules\Home\Controller;

use MartynBiz\Slim3Controller\Controller;

class IndexController extends Controller
{
    public function index()
    {
        return $this->render('index/index');
    }

    /**
     * Render the html and attach to the response
     * @param string $file Name of the template/ view to render
     * @param array $args Additional variables to pass to the view
     * @param Response?
     */
    public function render($file, $args=array())
    {
        $container = $this->app->getContainer();

        // generate the html
        $html = $container['renderer']->render($file, $args);

        // put the html in the response object
        $this->response->getBody()->write($html);

        return $this->response;
    }
}
