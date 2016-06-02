<?php
namespace Auth\Validator;

use MartynBiz\Validator;
use Auth\Model\User;

/**
 * Extension of MartynBiz\Validator so we can define custom validation classes
 */
class RegisterValidator extends Validator
{
    /**
     * @var User
     */
    protected $userModel;

    public function __construct(User $userModel)
    {
        $this->userModel = $userModel;
    }

    /**
     * Check with our account model that the email is valid
     * @param string $message Custom message when validation fails
     * @param User $model This will be used to query the db
     * @return Validator
     */
    public function isUniqueEmail($message)
    {
        //check whether this email exists in the db
        $user = $this->userModel->findOne( array(
            'email' => $this->value,
        ) );

        // log error
        if ($user) {
            $this->logError($this->key, $message);
        }

        // return instance
        return $this;
    }

    /**
     * This is just a re-usable method for this module, so we can use it again (register and lost/ change pw)
     * @param string $message Custom message when validation fails
     * @return Validator
     */
    public function isValidPassword($message)
    {
        return $this->isNotEmpty($message)
            ->isMinimumLength($message, 8)
            ->hasUpperCase($message)
            ->hasLowerCase($message)
            ->hasNumber($message);
    }

    /**
     * This just allows us to declare all our rules here instead of the controller
     * @return boolean
     */
    public function isValid()
    {
        // first_name
        $this->check('first_name')
            ->isNotEmpty('First name missing');

        // last_name
        $this->check('last_name')
            ->isNotEmpty('Last name missing');

        // email
        $this->check('email')
            ->isNotEmpty('Email missing')
            ->isEmail('Invalid email address')
            ->isUniqueEmail('Email address is already in the system');

        // password
        $message = 'Password must contain upper and lower case letters, and numbers';
        $this->check('password')
            ->isNotEmpty($message)
            ->hasLowerCase($message)
            ->hasUpperCase($message)
            ->isMinimumLength($message, 8);

        // agreement
        $this->check('agreement'); // $i18n->translate('please_agree_to_tc');

        // more_info
        // more info is a invisible field (not type=hidden, use css) that humans won't see
        // however, when bots turn up they don't know that and fill it in. so, if it's filled in,
        // we know this is a bot
        if ($this->has('more_info')) {
            $this->check('more_info')
                ->isEmpty('Something went wrong'); // misleading msg ;)
        }

        return parent::isValid();
    }

    // /**
    //  * Will compare two fields are the same (e.g. password, password_confirm)
    //  * @param string $message Custom message when validation fails
    //  * @param string $compareKey The other value key to compare with
    //  * @return Validator
    //  */
    // public function isSameAs($message, $compareKey)
    // {
    //     //check whether this email exists in the db
    //     if ($this->params[$this->key] != $this->params[$compareKey]) {
    //         $this->logError($this->key, $message);
    //     }
    //
    //     // return instance
    //     return $this;
    // }
}
