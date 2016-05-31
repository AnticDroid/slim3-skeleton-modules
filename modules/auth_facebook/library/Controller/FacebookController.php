<?php
namespace App\Modules\AuthFacebook\Controller;

use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;

use App\Modules\Auth\Model\Account;
use App\Modules\Auth\Controller\SessionController;
use App\Modules\Auth\Exception\InvalidAuthToken as InvalidAuthTokenException;

class FacebookController extends SessionController
{
    /**
     * POST /session/facebook
     */
    public function facebook()
    {
        // combine GET and POST params
        $params = array_merge($this->getQueryParams(), $this->getPost());

        // as the facebook button is a submit button, it will include username and
        // password (although probably empty). they don't have any purpose here and
        // we will strip them just to keep things clean and prevent unexpected issues
        $params = array_intersect_key($params, array_flip(array(
            'returnTo',
            'remember_me',
        )));

        // set default value for returnTo
        isset($params['returnTo']) or $params['returnTo'] = '/session';

        $container = $this->app->getContainer();
        $settings = $container->get('settings');

        // get the url for facebook, include our return to (which may also include
        // a nested returnTo for whatever app we wanna return to after authorization
        $helper = $this->get('facebook')->getRedirectLoginHelper();
        $returnTo = $settings['app_domain'] . '/session/facebook/callback?' . http_build_query($params);
        $permissions = array('email');
        $loginUrl = $helper->getLoginUrl($returnTo, $permissions);

        // redirect to facebook login url
        return $this->redirect($loginUrl);
    }

    /**
     * GET /session/facebook/callback
     */
    public function facebookCallback()
    {
        $params = $this->getQueryParams();
        $settings = $this->get('settings');

        // get the access token. if an error is thrown, forward to
        // login form with an error
        $helper = $this->get('facebook')->getRedirectLoginHelper();

        // get the access token. if an error occurs, return the user to the login
        // page with an "errors" flash
        try {

            // get the access token and store in session
            $accessToken = $helper->getAccessToken();
            $_SESSION['fb_access_token'] = (string) $accessToken;

            // using the access token, get the graph user details
            $response = $this->get('facebook')->get('/me?fields=id,name,first_name,last_name,email', $accessToken);

        } catch(\Exception $e) {

            if ($e instanceof FacebookResponseException) {
                $errorMsg = 'FacebookGraph returned an error: ' . $e->getMessage();
            } elseif ($e instanceof FacebookSDKException) {
                $errorMsg = 'FacebookSDK returned an error: ' . $e->getMessage();
            } else {
                $errorMsg = 'Unable to get Facebook access token: ' . $e->getMessage();
            }

            // When Graph returns an error
            $this->get('flash')->addMessage('errors', array($errorMsg));
            return $this->forward('index');

        }

        $graphUser = $response->getGraphUser()->asArray();


        // =================
        // Look for an existing user by email. If not found, create one with
        // details from the attributes (email, name -> username, etc ) and generate
        // a password. If user on this email exists, attach the Facebook ID to it

        // Look for an user with this facebook uid
        // we shouldn't ever rely upon email that we get back from facebook as the user
        // may have changed it and then it wouldn't tie up to an user anymore - but we
        // can rely upon "uid" (facebook id). we don't really care what their email is on
        // facebook actually, even if it differs from what we have for them.
        // might wanna use eloquent again for this but not sure how joins work there, although
        // they are supported. thankfully, PDO SELECT statements are working fine

        $fbId = $graphUser['id'];
        $name = $graphUser['name'];
        $email = $graphUser['email'];
        $firstName = $graphUser['first_name'];
        $lastName = $graphUser['last_name'];

        // TODO get from language cookie
        $lang = 'en';

        // pull out the user for this facebook_id, remember they can change their email in
        // facebook so we don't wanna go by that.
        $account = $this->get('model.account')->findByFacebookId($fbId);

        // if not found then we want to find this user by email address (which we
        // can be assured that it belongs to this user, as facebook will have validated
        // it - even if they changed it). if we have a user for this email address
        // we'll store the facebook_id in meta, otherwise we'll create a new user
        // from their facebook attributes. although they may not use it, it prevents
        // an unassociated user being created if they decided at a later date to
        // register with sso - in which case they'd have to reset their password or
        // refer to the generated pw in our welcome email (further sso email validation)

        if (! $account) {

            // fetch the user by email address if exists
            $account = $this->get('model.account')->findByEmail($email);

            // if found, upsert (insert/update) their facebook_id to meta table for this user
            // else insert a new user and insert a facebook_id to meta table
            if ($account) {

                // we'll be setting facebook_id, so next time the user is picked up by that
                $account->setMeta('facebook_id', $fbId);
            }
        }


        if (! $account) { // still no user, create one

            // user not found, create a new user for this email address, name, etc
            $account = $this->get('model.account')->create( array(
                'name' => $graphUser['name'],
                'first_name' => $graphUser['first_name'],
                'last_name' => $graphUser['last_name'],
                'email' => $graphUser['email'],
                'lang' => $lang, // TODO get this value from language_cookie
                'enabled' => 1,
            ) );

            $account->setMeta('facebook_id', $fbId);
        }


        // at this stage an $account with a verified email address for this user exists
        // we can proceed with the login and let the sp handle it from there

        // if they checked the remember_me box, let's store their auth_token in the db and
        // cookie so that they will be authenticated even with social media login (thanks
        // to the fact that we also silently create an account for sm login :)
        if (isset($params['remember_me'])) {
            $this->get('auth')->remember($account);
        } else {
            $this->get('auth')->forget($account);
        }

        // set session attributes. no desirable parameters will be filtered (e.g. password, salt)
        $this->get('auth')->setAttributes( array_merge($account->toArray(), array(
            'backend' => Account::BACKEND_FACEBOOK,
        )) );

        // redirect to returnTo if given
        isset($params['returnTo']) or $params['returnTo'] = $settings->get('defaultLoginRedirect', '/session');
        return $this->returnTo($params['returnTo']);
    }
}
