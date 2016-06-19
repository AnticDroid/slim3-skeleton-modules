<?php
namespace Auth\Controller;

use Auth\Model\User;
// use Auth\Model\RecoveryToken;
// use Auth\Exception\InvalidRecoveryToken;
// use Auth\Exception\UserNotFound;
use Auth\Validator\RegisterValidator;

use Auth\Validator;

class UsersController extends BaseController
{
    public function create()
    {
        $user = new User( $this->getPost() );

        return $this->render('auth/users/create', compact('user'));
    }

    public function post()
    {
        $params = $this->getPost();
        $userModel = $this->get('auth.model.user');

        $validator = new RegisterValidator($userModel, $params);
        $validator->setData($params);

        // new users are set s ROLE_CONTRIBUTOR
        $params['role'] = User::ROLE_CONTRIBUTOR;

        // if valid, create account
        if ($validator->isValid() and $user = $userModel->create($params)) {

            // set session attributes w/ backend (method of signin)
            $this->get('auth')->setAttributes($user->toArray());

            // send welcome email
            $this->get('mail_manager')->sendWelcomeEmail($user);

            // redirect
            isset($params['returnTo']) or $params['returnTo'] = '/';
            return $this->returnTo($params['returnTo']);
        }

        $this->get('flash')->addMessage('errors', $validator->getErrors());

        return $this->forward('create');
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
    //         throw new InvalidReturnToUrl('Invalid return to URL');
    //     }
    //
    //     return parent::redirect($returnTo);
    // }

    /**
     * This action is used to handle the process when a user wished to reset their
     * password. It will , ,
     * 3) , 4)
     * GET /accounts/resetpassword -- stage 1. show a form
     * POST /accounts/resetpassword -- stage 2. handle post (email, store reset_token in db)
     * GET /accounts/resetpassword?token=... -- stage 3. handle click from email (verify token, show change form)
     * POST /accounts/resetpassword {token=...} -- stage 4. handle change pw
     */
    public function resetpassword()
    {
        $params = array_merge($this->getQueryParams(), $this->getPost());
        $settings = $this->get('settings')['recovery_token'];

        // just so we can have a script that keeps the process in order (for ease
        // of reading) i'll determine the stage here and then use a switch to keep
        // things in order.
        if ($this->request->isPost()) {
            if (isset($params['token'])) {
                $stage = 4; // handle change password form
            } else {
                $stage = 2; // handle email address submission
            }
        } else { // GET
            if (isset($params['token'])) {
                $stage = 3; // handle link from email, change password form
            } else {
                $stage = 1; // email address form
            }
        }

        // from here we'll use $stage and guide the user through the process of
        // changing their password
        switch($stage) {
            case 1: // email address form

                // enter email form
                return $this->render('accounts.resetpassword_enteremail', compact('params'));

                break;

            case 2: // handle email address submission

                $validator = new Validator($params);
                $i18n = $this->get('i18n');

                // more_info
                // more info is a invisible field (not type=hidden, use css) that humans won't see
                // however, when bots turn up they don't know that and fill it in. so, if it's filled in,
                // we know this is a bot
                if ($validator->has('more_info')) {
                    $validator->check('more_info')
                        ->isEmpty( $i18n->translate('email_invalid') ); // misleading msg ;)
                }

                // email
                $validator->check('email')
                    ->isNotEmpty( $i18n->translate('email_missing') )
                    ->isEmail( $i18n->translate('email_invalid') );

                if ($validator->isValid()) {

                    try {

                        // find account by email
                        $user = $this->get('model.account')->findByEmail($params['email']);
                        if (! $user) {
                            throw new UserNotFound('An account of this email address was not found.');
                        }

                        // delete old recovery_token if exists
                        $recoveryToken = $user->recovery_token;
                        if ($recoveryToken) {
                            $recoveryToken->delete();
                        }

                        // create new recovery token for this account
                        // the token will be hashed when we store it in the db so
                        // we wanna keep a note of it here
                        $selector = uniqid();
                        $token = bin2hex(random_bytes(20));
                        $expire = date('Y-m-d: H:i:s', $settings['expire']);
                        $recoveryToken = $user->recovery_token()->create( array(
                            'selector' => $selector,
                            'token' => $token,
                            'expire' => $expire,
                        ) );

                        // send an email with the link and token
                        $user = $recoveryToken->account;
                        $emailRecoveryToken = $selector . '_' . $token;
                        $this->get('mail_manager')->sendPasswordRecoveryToken($user, $emailRecoveryToken);

                        // success - check email
                        return $this->render('accounts.resetpassword_checkemail', compact('params'));

                    } catch(\Exception $e) {

                        $this->get('flash')->addMessage('errors', array(
                            'error' => $e->getMessage(),
                        ) );

                    }

                } else {

                    $this->get('flash')->addMessage('errors', $validator->getErrors() );

                }

                // with errors
                return $this->render('accounts.resetpassword_enteremail', compact('params'))
                    ->withStatus(400);

                break;


            case 3: // handle link from email, change password form

                try {

                    @list($selector, $token) = explode('_', $params['token']);

                    // get the recovery_token entry
                    $recoveryToken = $this->get('model.recovery_token')->findValidTokenBySelector($selector);
                    if (! $recoveryToken) {
                        throw new InvalidRecoveryToken('Invalid recovery token.');
                    }

                    // ensure that this token matches the hashed token we have stored
                    if (! $recoveryToken->verifyToken($token)) {
                        $recoveryToken->delete();
                        throw new InvalidRecoveryToken('Invalid recovery token.');
                    }

                    // change password form
                    return $this->render('accounts.resetpassword_changepassword', compact('params'));

                } catch(\Exception $e) {

                    $this->get('flash')->addMessage('errors', array(
                        'error' => $e->getMessage(),
                    ) );

                }

                // with errors
                return $this->render('accounts.resetpassword_enteremail', compact('params'))
                    ->withStatus(400);

                break;

            case 4: // handle change password form

                $validator = new Validator($params);
                $i18n = $this->get('i18n');

                // password
                $message = $i18n->translate('password_must_contain');
                $validator->check('password')
                    ->isNotEmpty($message)
                    ->hasLowerCase($message)
                    ->hasUpperCase($message)
                    ->isMinimumLength($message, 8);

                // passwords are the same
                $validator->check('password')
                    ->isSameAs('password_confirmation', 'Passwords do not match');

                if ($validator->isValid()) {

                    try {

                        @list($selector, $token) = explode('_', $params['token']);

                        // get the recovery_token entry
                        $recoveryToken = $this->get('model.recovery_token')->findValidTokenBySelector($selector);
                        if (! $recoveryToken) {
                            throw new InvalidRecoveryToken('Recovery token was not found.');
                        }

                        // ensure that this token matches the hashed token we have stored
                        if (! $recoveryToken->verifyToken($token)) {
                            $recoveryToken->delete();
                            throw new InvalidRecoveryToken('Recovery token was not found.');
                        }

                        // finally, update password :)
                        $user = $recoveryToken->account;
                        $user->password = $params['password'];
                        $user->save();

                        // delete the token as it's no longer needed
                        $recoveryToken->delete();

                        // show change successful form
                        return $this->render('accounts.resetpassword_complete', compact('params'));

                    } catch(\Exception $e) {

                        $this->get('flash')->addMessage('errors', array(
                            'error' => $e->getMessage(),
                        ) );

                    }

                } else {

                    $this->get('flash')->addMessage('errors', $validator->getErrors() );

                }

                // with errors
                return $this->render('accounts.resetpassword_changepassword', compact('params'))
                    ->withStatus(400);

                break;

        }
    }
}
