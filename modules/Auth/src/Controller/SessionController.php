<?php
namespace Auth\Controller;

use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Auth\Exception\InvalidAuthToken as InvalidAuthTokenException;

class SessionController extends BaseController
{
    /**
     * @var User
     */
    protected $currentUser;

    /**
     * Index will serve as a landing page for both GET login and logout
     * GET /session
     */
    public function index()
    {
        // GET and POST
        $params = array_merge($this->getQueryParams(), $this->getPost());

        // // check for remember me cookie.
        // // if found, get the account and set attributes
        // $request = $this->get('request');
        // $rememberMe = $request->getCookie('auth_token');
        //
        // // if the auth_token (remember me) cookie is set, and the user is not
        // // authenticated - handle the token (e.g. auto sign in)
        // if ($rememberMe and !$this->get('auth')->isAuthenticated()) {
        //
        //     @list($selector, $token) = explode('_', $rememberMe);
        //
        //     // check validity of token
        //     try {
        //
        //         // test 1. find a valid token (not expired) by selector
        //         $authToken = $this->get('model.auth_token')->findValidTokenBySelector($selector);
        //         if (! $authToken) {
        //
        //             // maybe the auth_token cookie has expired, and been cleaned from the
        //             // database. in any case, we'll just remove it from the client's machine
        //             $this->get('auth')->deleteAuthTokenCookie();
        //
        //             // throwing an exception will be caught and an error message displayed
        //             throw new InvalidAuthTokenException('Could not automatically signin with remember me token (0). Please login again.');
        //
        //         }
        //
        //         // test 2. ensure that this token matches the hashed token we have stored
        //         if (! $authToken->verifyToken($token)) {
        //
        //             // token string is invalid, this could be an attack at someone's account (or not)
        //             // remove the token from the database and the auth token and the client cookie
        //             $this->get('auth')->deleteAuthTokenCookie();
        //             $authToken->delete();
        //
        //             // throwing an exception will be caught and an error message displayed
        //             throw new InvalidAuthTokenException('Could not automatically signin with remember me token (1). Please login again.');
        //
        //         }
        //
        //         // test 3. get the account for this auth_token
        //         $account = $authToken->account;
        //         if (! $account) {
        //
        //             // account not found
        //             // remove the token from the database and the auth token and the client cookie
        //             $this->get('auth')->deleteAuthTokenCookie();
        //             $authToken->delete();
        //
        //             // throwing an exception will be caught and an error message displayed
        //             throw new InvalidAuthTokenException('Could not automatically signin with remember me token (2). Please login again.');
        //
        //         }
        //
        //
        //         // all good :) sign this person in using their auth_token...
        //
        //         // update remember me with new token
        //         $this->get('auth')->remember($account);
        //
        //         // set attributes. valid_attributes will only set the fields we
        //         // want to be avialable (e.g. not password)
        //         $this->get('auth')->setAttributes( array_merge($account->toArray(), array(
        //             'backend' => Account::BACKEND_JAPANTRAVEL,
        //         )) );
        //
        //         // redirect back to returnTo, or /session (logout page) if not provided
        //         isset($params['returnTo']) or $params['returnTo'] = '/session';
        //         return $this->returnTo($params['returnTo']);
        //
        //     } catch (\Exception $e) {
        //
        //         // delete any token that is associated with this $selector as it's invalid
        //         $this->get('model.auth_token')->deleteBySelector($selector);
        //
        //         // show error on next page - if passive login is used, this will be
        //         // returned in the returnTo url
        //         if (@$params['passive']) {
        //
        //             // // if we want to pass the error to the returnTo app then we ought to
        //             // // put it in the url here
        //             // if (parse_url($params['returnTo'], PHP_URL_QUERY)) {
        //             //     $params['returnTo'] .= '&loginError=' . urlencode($e->getMessage());
        //             // } else {
        //             //     $params['returnTo'] .= '?loginError=' . urlencode($e->getMessage());
        //             // }
        //
        //         } else {
        //
        //             // this will set an error message and continue to the login form
        //             $this->get('flash')->addMessage('errors', array(
        //                 $e->getMessage(),
        //             ));
        //
        //         }
        //     }
        // }
        //
        //
        // // =====================
        // // render login/ logout page
        //
        // if (@$params['passive']) {
        //
        //     // return user
        //     isset($params['returnTo']) or $params['returnTo'] = $settings->get('defaultLogoutRedirect', '/session');
        //     return $this->returnTo($params['returnTo']);
        //
        // } else {

            // if the user is authenticated then we will show the logout page which
            // will serve as a landing page, although most typically apps will send
            // a DELETE request which will be handled by the delete() method
            // if the user is not authenticated, the show the login page
            if ($this->get('auth')->isAuthenticated()) {
                return $this->render('auth/session/logout', compact('params'));
            } else {
                return $this->render('auth/session/login', compact('params'));
            }

        // }
    }

    /**
     * POST /session -- login
     */
    public function post()
    {
        // GET and POST
        $params = array_merge($this->getQueryParams(), $this->getPost());
        $settings = $this->get('settings');

        // authentice with the email (might even be username, which is fine) and pw
        if ($this->get('auth')->authenticate($params['email'], $params['password'])) {

            // as authentication has passed, get the user by email OR username
            $user = $this->get('Auth\Model\User')->findOne(array('$or' => array(
                array('email' => $params['email']),
                array('username' => $params['email'])
            )));

            // // if requested (remember me checkbox), create remember me token cookie
            // // else, remove the cookie (if exists)
            // if (isset($params['remember_me'])) {
            //     $this->get('auth')->remember($account);
            // } else {
            //     $this->get('auth')->forget($account);
            // }

            // set attributes. valid_attributes will only set the fields we
            // want to be avialable (e.g. not password)
            $this->get('auth')->setAttributes( $user->toArray() );

            // redirect back to returnTo, or /session (logout page - default) if not provided
            isset($params['returnTo']) or $params['returnTo'] = '/';
            return $this->returnTo($params['returnTo']);

        } else {

            // forward them to the login page with errors to try again
            $this->get('flash')->addMessage('errors', array(
                'Invalid username or password',
            ));
            return $this->forward('index');

        }
    }

    /**
     * DELETE /session -- logout
     */
    public function delete()
    {
        // combine GET and POST params
        $params = array_merge($this->getQueryParams(), $this->getPost());
        $settings = $this->get('settings');

        // // also, delete any auth_token we have for the account and cookie
        // $account = $this->getSessionAccount();
        // if ($account) {
        //     $this->get('auth')->forget($account);
        // } else { // just delete cookie then - if exists
        //     $this->get('auth')->deleteAuthTokenCookie();
        // }

        // this will effective end the "session" by clearning out the session vars
        $this->get('auth')->clearAttributes();

        // redirect back to returnTo, or /session (logout page) if not provided
        isset($params['returnTo']) or $params['returnTo'] = '/';
        return $this->returnTo($params['returnTo']);
    }

    // /**
    //  * POST /session/facebook
    //  */
    // public function facebook()
    // {
    //     // combine GET and POST params
    //     $params = array_merge($this->getQueryParams(), $this->getPost());
    //
    //     // as the facebook button is a submit button, it will include username and
    //     // password (although probably empty). they don't have any purpose here and
    //     // we will strip them just to keep things clean and prevent unexpected issues
    //     $params = array_intersect_key($params, array_flip(array(
    //         'returnTo',
    //         'remember_me',
    //     )));
    //
    //     // set default value for returnTo
    //     isset($params['returnTo']) or $params['returnTo'] = '/session';
    //
    //     $container = $this->app->getContainer();
    //     $settings = $container->get('settings');
    //
    //     // get the url for facebook, include our return to (which may also include
    //     // a nested returnTo for whatever app we wanna return to after authorization
    //     $helper = $this->get('facebook')->getRedirectLoginHelper();
    //     $returnTo = $settings['app_domain'] . '/session/facebook/callback?' . http_build_query($params);
    //     $permissions = array('email');
    //     $loginUrl = $helper->getLoginUrl($returnTo, $permissions);
    //
    //     // redirect to facebook login url
    //     return $this->redirect($loginUrl);
    // }
    //
    // /**
    //  * GET /session/facebook/callback
    //  */
    // public function facebookCallback()
    // {
    //     $params = $this->getQueryParams();
    //     $settings = $this->get('settings');
    //
    //     // get the access token. if an error is thrown, forward to
    //     // login form with an error
    //     $helper = $this->get('facebook')->getRedirectLoginHelper();
    //
    //     // get the access token. if an error occurs, return the user to the login
    //     // page with an "errors" flash
    //     try {
    //
    //         // get the access token and store in session
    //         $accessToken = $helper->getAccessToken();
    //         $_SESSION['fb_access_token'] = (string) $accessToken;
    //
    //         // using the access token, get the graph user details
    //         $response = $this->get('facebook')->get('/me?fields=id,name,first_name,last_name,email', $accessToken);
    //
    //     } catch(\Exception $e) {
    //
    //         if ($e instanceof FacebookResponseException) {
    //             $errorMsg = 'FacebookGraph returned an error: ' . $e->getMessage();
    //         } elseif ($e instanceof FacebookSDKException) {
    //             $errorMsg = 'FacebookSDK returned an error: ' . $e->getMessage();
    //         } else {
    //             $errorMsg = 'Unable to get Facebook access token: ' . $e->getMessage();
    //         }
    //
    //         // When Graph returns an error
    //         $this->get('flash')->addMessage('errors', array($errorMsg));
    //         return $this->forward('index');
    //
    //     }
    //
    //     $graphUser = $response->getGraphUser()->asArray();
    //
    //
    //     // =================
    //     // Look for an existing user by email. If not found, create one with
    //     // details from the attributes (email, name -> username, etc ) and generate
    //     // a password. If user on this email exists, attach the Facebook ID to it
    //
    //     // Look for an user with this facebook uid
    //     // we shouldn't ever rely upon email that we get back from facebook as the user
    //     // may have changed it and then it wouldn't tie up to an user anymore - but we
    //     // can rely upon "uid" (facebook id). we don't really care what their email is on
    //     // facebook actually, even if it differs from what we have for them.
    //     // might wanna use eloquent again for this but not sure how joins work there, although
    //     // they are supported. thankfully, PDO SELECT statements are working fine
    //
    //     $fbId = $graphUser['id'];
    //     $name = $graphUser['name'];
    //     $email = $graphUser['email'];
    //     $firstName = $graphUser['first_name'];
    //     $lastName = $graphUser['last_name'];
    //
    //     // TODO get from language cookie
    //     $lang = 'en';
    //
    //     // pull out the user for this facebook_id, remember they can change their email in
    //     // facebook so we don't wanna go by that.
    //     $account = $this->get('model.account')->findByFacebookId($fbId);
    //
    //     // if not found then we want to find this user by email address (which we
    //     // can be assured that it belongs to this user, as facebook will have validated
    //     // it - even if they changed it). if we have a user for this email address
    //     // we'll store the facebook_id in meta, otherwise we'll create a new user
    //     // from their facebook attributes. although they may not use it, it prevents
    //     // an unassociated user being created if they decided at a later date to
    //     // register with sso - in which case they'd have to reset their password or
    //     // refer to the generated pw in our welcome email (further sso email validation)
    //
    //     if (! $account) {
    //
    //         // fetch the user by email address if exists
    //         $account = $this->get('model.account')->findByEmail($email);
    //
    //         // if found, upsert (insert/update) their facebook_id to meta table for this user
    //         // else insert a new user and insert a facebook_id to meta table
    //         if ($account) {
    //
    //             // we'll be setting facebook_id, so next time the user is picked up by that
    //             $account->setMeta('facebook_id', $fbId);
    //         }
    //     }
    //
    //
    //     if (! $account) { // still no user, create one
    //
    //         // user not found, create a new user for this email address, name, etc
    //         $account = $this->get('model.account')->create( array(
    //             'name' => $graphUser['name'],
    //             'first_name' => $graphUser['first_name'],
    //             'last_name' => $graphUser['last_name'],
    //             'email' => $graphUser['email'],
    //             'lang' => $lang, // TODO get this value from language_cookie
    //             'enabled' => 1,
    //         ) );
    //
    //         $account->setMeta('facebook_id', $fbId);
    //     }
    //
    //
    //     // at this stage an $account with a verified email address for this user exists
    //     // we can proceed with the login and let the sp handle it from there
    //
    //     // if they checked the remember_me box, let's store their auth_token in the db and
    //     // cookie so that they will be authenticated even with social media login (thanks
    //     // to the fact that we also silently create an account for sm login :)
    //     if (isset($params['remember_me'])) {
    //         $this->get('auth')->remember($account);
    //     } else {
    //         $this->get('auth')->forget($account);
    //     }
    //
    //     // set session attributes. no desirable parameters will be filtered (e.g. password, salt)
    //     $this->get('auth')->setAttributes( array_merge($account->toArray(), array(
    //         'backend' => Account::BACKEND_FACEBOOK,
    //     )) );
    //
    //     // redirect to returnTo if given
    //     isset($params['returnTo']) or $params['returnTo'] = $settings->get('defaultLoginRedirect', '/session');
    //     return $this->returnTo($params['returnTo']);
    // }

    // /**
    //  * Get the current sign in user account
    //  */
    // protected function getSessionAccount()
    // {
    //     $attributes = $this->get('auth')->getAttributes();
    //     return $this->get('model.account')->findByEmail($attributes['email']);
    // }
}
