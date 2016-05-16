<?php
namespace App\Auth\Adapter;

// use Zend\Authentication\Adapter\AdapterInterface;
// use Zend\Authentication\Result;

use App\Model\User;

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
            // 'password' => User::encryptPassword($password),
        ) );
// var_dump($user); exit;
        return ($user and password_verify($password, $user->password));
    }
}
