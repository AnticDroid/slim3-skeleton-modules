<?php
namespace Application\Controller;

use MartynBiz\Slim3Controller\Controller;

class BaseController extends Controller
{
    /**
     * Render the html and attach to the response
     * @param string $file Name of the template/ view to render
     * @param array $args Additional variables to pass to the view
     * @param Response?
     */
    public function render($file, $data=array())
    {
        $container = $this->app->getContainer();

        $data = array_merge([
            'messages' => $this->get('flash')->flushMessages(),
            'currentUser' => $this->get('auth')->getCurrentUser(),
            'router' => $this->app->getContainer()->get('router'),
        ], $data);

        if ($container->has('csrf')) {
            $data['csrfName'] = $this->request->getAttribute('csrf_name');
            $data['csrfValue'] = $this->request->getAttribute('csrf_value');
        }

        // generate the html
        $html = $container['renderer']->render($file, $data);

        // put the html in the response object
        $this->response->getBody()->write($html);

        return $this->response;
    }
}
