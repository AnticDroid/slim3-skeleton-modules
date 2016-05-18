<?php
namespace App\Modules\Auth\Controller;

use MartynBiz\Slim3Controller\Controller;
use App\Modules\Auth\Exception\InvalidReturnToUrl;
use App\Modules\Auth\Model\Account;

abstract class BaseController extends Controller
{
    /**
     * @var User
     */
    protected $currentUser;

    /**
     * Render the html and attach to the response
     * @param string $file Name of the template/ view to render
     * @param array $data Additional variables to pass to the view
     * @param Response?
     */
    public function render($file, $data=array())
    {
        $container = $this->app->getContainer();

        // add some additional view vars
        $data = array_merge($data, array(
            'messages' => $this->get('flash')->flushMessages(),
            'currentUser' => null,
        ));

        // generate the html
        $html = $container['renderer']->render($file, $data);

        // put the html in the response object
        $this->response->getBody()->write($html);

        return $this->response;
    }

    // /**
    //  * Will ensure that returnTo url is valid before doing redirect. Otherwise mean
    //  * people could use out login then redirect to a phishing site
    //  * @param string $returnTo The returnTo url that we want to check against our white list
    //  */
    // protected function returnTo($returnTo)
    // {
    //     $container = $this->app->getContainer();
    //     $settings = $container->get('settings');
    //
    //     // check returnTo
    //     $host = parse_url($returnTo, PHP_URL_HOST);
    //     if ($host and !preg_match($settings['valid_return_to'], $host)) {
    //         throw new InvalidReturnToUrl( $this->get('i18n')->translate('invalid_return_to') );
    //     }
    //
    //     return parent::redirect($returnTo);
    // }

    /**
     * Get the current sign in user account
     */
    protected function getSessionAccount()
    {
        $attributes = $this->get('auth')->getAttributes();
        return $this->get('model.account')->findByEmail($attributes['email']);
    }
}
