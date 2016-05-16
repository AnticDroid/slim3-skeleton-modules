<?php
namespace App\Modules\Auth\Adapter;

// use Zend\Authentication\Adapter\AdapterInterface;
// use Zend\Authentication\Result;

use App\Modules\Auth\Model\Account;

class Eloquent implements AdapterInterface
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
     * @var App\Model\Account
     */
    protected $model;

    /**
     * Sets accountname and password for authentication
     *
     * @return void
     */
    public function __construct(Account $model)
    {
        $this->model = $model;
    }

    /**
     * Performs an authentication attempt
     */
    public function authenticate($identity, $password)
    {
        // look up $account from the database
        $account = $this->model->where('email', $identity)
            ->orWhere('username', $identity)
            ->first();

        if (!$account) return false;

        $level = "08";
		$salt = '$2a$' . $level . '$' . $account->salt . '$';
		$hashed = crypt($password, $salt);

        return ($account and ($hashed === $account->password));
    }
}
