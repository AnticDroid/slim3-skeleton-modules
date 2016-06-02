<?php
namespace Auth\Controller;

use MartynBiz\Slim3Controller\Controller;
use Auth\Exception\InvalidReturnToUrl;
use Auth\Model\Account;

abstract class BaseController extends Controller
{
    /**
     * Render the html and attach to the response
     * @param string $file Name of the template/ view to render
     * @param array $data Additional variables to pass to the view
     * @param Response?
     */
    public function render($file, $args=array())
    {
        $container = $this->app->getContainer();

        // this will ensure that $data is available to all templates
        $data = array(
            'messages' => $this->get('flash')->flushMessages(),
            'currentUser' => $this->get('auth')->getCurrentUser(),
        );
        $container['renderer']->addData($data);

        // generate the html
        $html = $container['renderer']->render($file, $args);

        // put the html in the response object
        $this->response->getBody()->write($html);

        return $this->response;
    }

    /**
     * Will ensure that returnTo url is valid before doing redirect. Otherwise mean
     * people could use out login then redirect to a phishing site
     * @param string $returnTo The returnTo url that we want to check against our white list
     */
    protected function returnTo($returnTo)
    {
        $container = $this->app->getContainer();
        $settings = $container->get('settings');

        // check returnTo
        $host = parse_url($returnTo, PHP_URL_HOST);
        if ($host and !preg_match($settings['valid_return_to'], $host)) {
            throw new InvalidReturnToUrl( $this->get('i18n')->translate('invalid_return_to') );
        }

        return parent::redirect($returnTo);
    }
}
