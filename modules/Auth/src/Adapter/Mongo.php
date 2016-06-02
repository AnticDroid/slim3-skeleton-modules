<?php
namespace Auth\Adapter;

// use Zend\Authentication\Adapter\AdapterInterface;
// use Zend\Authentication\Result;

use Auth\Model\User;

class Mongo implements AdapterInterface
{
    /**
     * @var string
     */
    protected $identity;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var App\Model\User
     */
    protected $model;

    /**
     * Sets username and password for authentication
     *
     * @return void
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * Performs an authentication attempt
     */
    public function authenticate($identity, $password)
    {
        // look up $user from the database
        $user = $this->model->findOne( array(
            'email' => $identity,
        ) );

        return ($user and password_verify($password, $user->password));
    }

    /**
     * This is the identity (e.g. username) stored for this user
     * @return string
     */
    public function getUser($query)
    {
        return $this->model->findOne($query);
    }
}
