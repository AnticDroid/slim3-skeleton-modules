<?php
namespace Application\Controller;

use Interop\Container\ContainerInterface;

class BaseController
{
    protected $ci;

    public function __construct(ContainerInterface $ci) {
       $this->ci = $ci;
    }

    /**
     * Render the html and attach to the response
     * @param string $file Name of the template/ view to render
     * @param array $args Additional variables to pass to the view
     * @param Response?
     */
    public function render($file, $data=array())
    {
        $container = $this->getContainer();

        $data = array_merge([
            'messages' => $container->get('flash')->flushMessages(),
            'currentUser' => $container->get('auth')->getCurrentUser(),
            'router' => $container->get('router'),
        ], $data);

        if ($container->has('csrf')) {
            $data['csrfName'] = $container->get('request')->getAttribute('csrf_name');
            $data['csrfValue'] = $container->get('request')->getAttribute('csrf_value');
        }

        // generate the html
        $html = $container->get('renderer')->render($file, $data);

        // put the html in the response object
        $response = $container->get('response');
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
        $container = $this->getContainer();
        $response = $container->get('response');

        // put the html in the response object
        $response->getBody()->write(json_encode($data));

        return $response;
    }

    /**
     * Shorthand method to get dependency from container
     * @param $name
     * @return mixed
     */
    protected function getContainer()
    {
        return $this->ci;
    }

    /**
     * Pass on the control to another action. Of the same class (for now)
     *
     * @param  string $actionName The redirect destination.
     * @param array $data
     * @return Controller
     * @internal param string $status The redirect HTTP status code.
     */
    public function forward($actionName, $data=array())
    {
        // update the action name that was last used
        if (method_exists($this->response, 'setActionName')) {
            $this->response->setActionName($actionName);
        }

        return call_user_func_array(array($this, $actionName), $data);
    }
}
