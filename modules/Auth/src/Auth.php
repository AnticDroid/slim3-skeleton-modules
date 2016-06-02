<?php
namespace Auth;

use Auth\Adapter\AdapterInterface;
use Auth\Model\User;

/**
 * This handles authentication queries such as comparing username/password,
 * session and remember me functonality
 * @author Martyn Bissett
 */
class Auth
{
    /**
     * @var App\Auth\Adapter\AdapterInterface
     */
    protected $adapter;

    /**
     * @var ArrayAccess
     */
    protected $storage;

    /**
     * @var array
     */
    protected $settings = array();

    /**
     * @var User
     */
    protected $currentUser;

    /**
     * We need to pass in the auth library that we're using
     * @param App\Auth\Adapter\AdapterInterface $adapter
     * @param array $settings
     */
    public function __construct(AdapterInterface $adapter, $settings=array())
    {
        // set settings e.g. namespace
        $this->settings = array_merge(array(
            'namespace' => 'sso__',
        ), $settings);

        // set adapter
        $this->adapter = $adapter;

        // set storage
        $this->storage = &$_SESSION;
    }

    /**
     * Allows strogage to be replaced (mainly useful for testing)
     * @param ArrayObject $storage The replacement storage object
     */
    public function setStorage(\ArrayObject $storage)
    {
        $this->storage = $storage;
    }

    /**
     * This is the identity (e.g. username) stored for this user
     * @return string
     */
    public function getAttributes()
    {
        // get attributes from the session
        $namespace = $this->settings['namespace'];
        $attributes = @$this->storage[$namespace];

        if ($attributes instanceof \ArrayObject) {
            $attributes = $attributes->getArrayCopy();
        }

        return $attributes;
    }

    /**
     * Will write attributes to the session
     * @param array $attributes
     * @return string
     */
    public function setAttributes($attributes)
    {
        // ensure that whitelist is set
        if (! isset($this->settings['valid_attributes'])) {
            throw new Exception('valid_attributes must be set in settings');
        }

        // filter attributes based on valid_attributes
        $attributes = array_intersect_key($attributes, array_flip($this->settings['valid_attributes']));

        // first of all, clear attributes
        $this->clearAttributes();

        // get attributes from the session
        $namespace = $this->settings['namespace'];
        $this->storage[$namespace] = $attributes;
    }

    /**
     * This is the identity (e.g. username) stored for this user
     * @return boolean
     */
    public function isAuthenticated()
    {
        // get identity from the session
        return (! empty( $this->getAttributes() ));
    }

    /**
     * Clear the identity of the user (logout)
     * @return void
     */
    public function clearAttributes()
    {
        // clear session
        $namespace = $this->settings['namespace'];
        unset($this->storage[$namespace]);
    }

    /**
     * This is the identity (e.g. username) stored for this user
     * @param string $identity Username/ email
     * @param string $password
     * @return array
     */
    public function authenticate($identity, $password)
    {
        return $this->adapter->authenticate($identity, $password);
    }

    /**
     * Will create/update remember me token for this $account, and store the
     * hashed token in the database
     * @param App\Model\User $account
     * @return void
     */
    public function remember(User $account)
    {
        // we just need a unique id for selector
        $selector = uniqid();

        // create token (even if token exists in db, we'll overwrite it)
        // this ought to create a string with sufficient randomness
        $token = bin2hex(random_bytes(20));

        // look for remember_me entry in database for this account
        // we wanna update (rather than delete) because we don't want to change
        // the data - or do we? reset each time to 1/2/3 months?
        $authToken = $account->auth_token;
        if ($authToken) {

            // update with new selector and token value
            $authToken->selector = $selector;
            $authToken->token = $token;
            // $authToken->expire = date('Y-m-d: H:i:s', time() + $this->settings['expire']);
            $authToken->save();
        } else {

            $expire = date('Y-m-d: H:i:s', $this->settings['auth_token']['expire']);

            // create a new selector to identify this user
            $authToken = $account->auth_token()->create( array(
                'selector' => $selector,
                'token' => $token,
                'expire' => $expire,
            ) );
        }

        // write new value to cookie
        $name = $this->settings['auth_token']['cookie_name'];
        $expire = $this->settings['auth_token']['expire']; // seconds
        $path = $this->settings['auth_token']['path'];
        $domain = $this->settings['cookie_domain'];
        setcookie($name, "{$selector}_{$token}", $expire, $path, $domain);
    }

    /**
     * Will destroy remember me token for this $account, and delete the token in the db
     * @param App\Model\User $account
     * @return void
     */
    public function forget(User $account)
    {
        // look for remember_me entry in database for this account, delete it
        $authToken = $account->auth_token;
        if ($authToken) {
            $authToken->delete();
        }

        // and delete any auth_token cookie this client may have
        $this->deleteAuthTokenCookie();
    }

    /**
     * This will remove the auth_token cookie only for this client, any database
     * process should be done in forget() method - that method will call this method
     * once removed the token from database
     */
    public function deleteAuthTokenCookie()
    {
        // remove the cookie
        $name = $this->settings['auth_token']['cookie_name'];
        $expire = time() - 3600; // past
        $path = $this->settings['auth_token']['path'];
        $domain = $this->settings['cookie_domain'];
        if (isset($_COOKIE[$name])) {
            unset($_COOKIE[$name]);
            setcookie($name, '', $expire, $path, $domain); // empty value and old timestamp
        }
    }

    /**
     * This is the identity (e.g. username) stored for this user
     * @return string
     */
    public function getCurrentUser()
    {
        if (! $this->currentUser) {

            // attributes will be null/empty if not authenticated
            $attributes = $this->getAttributes();
            if (empty($attributes)) {
                return null;
            }

            // lookup the user by identity
            $this->currentUser = $this->adapter->getUser(array(
                'email' => $attributes['email'],
            ));
        }

        return $this->currentUser;
    }
}
