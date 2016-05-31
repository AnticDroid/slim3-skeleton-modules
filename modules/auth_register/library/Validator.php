<?php
namespace App\Modules\AuthRegister;

use App\Modules\Auth\Model\User;

/**
 * Extension of MartynBiz\Validator so we can define custom validation classes
 */
class Validator extends \MartynBiz\Validator
{
    /**
     * Check with our account model that the email is valid
     * @param string $message Custom message when validation fails
     * @param User $model This will be used to query the db
     * @return Validator
     */
    public function isUniqueEmail($message, User $model)
    {
        //check whether this email exists in the db
        $user = $model->findOne( array(
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
