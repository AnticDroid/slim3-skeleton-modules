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
        $request = $this->get('request');
        $response = $this->get('response');

        $data = array_merge([
            'messages' => $this->get('flash')->flushMessages(),
            'currentUser' => $this->get('auth')->getCurrentUser(),
            'router' => $this->get('router'),
        ], $data);

        if ($this->container->has('csrf')) {
            $data['csrfName'] = $request->getAttribute('csrf_name');
            $data['csrfValue'] = $request->getAttribute('csrf_value');
        }

        // generate the html
        $html = $this->get('renderer')->render($file, $data);

        // put the html in the response object
        $response->getBody()->write($html);

        return $response;
    }

    /**
     * Render the html and attach to the response
     * @param string $file Name of the template/ view to render
     * @param array $args Additional variables to pass to the view
     * @param Response?
     */
    public function renderJson($data=array())
    {
        $response = $this->get('response');
        $response->getBody()->write( json_encode($data) );

        return $response;
    }
}
