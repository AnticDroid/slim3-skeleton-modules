<?php

namespace App\Modules\Auth\Model;

use MartynBiz\Mongo;
use MartynBiz\Validator;
// use App\Modules\Auth\Model\Article;

/**
 *
 */
class User extends Base
{
    const ROLE_ADMIN = 'admin';
    const ROLE_EDITOR = 'editor';
    const ROLE_MEMBER = 'member';

    // collection this model refers to
    protected static $collection = 'users';

    // define on the fields that can be saved
    protected static $whitelist = array(
        'first_name',
        'last_name',
        'email',
        'password',
    );

    public static function encryptPassword($value)
    {
        // even null will be encrypted it seems
        if (is_null($value)) {
            return null;
        }

        return password_hash($value, PASSWORD_BCRYPT, array(
            'cost' => 12,
        ));
    }

    public function validate()
    {
        $this->resetErrors();

        $validator = new Validator($this->data);

        $validator->check('first_name')
            ->isNotEmpty('First name is missing');

        $validator->check('last_name')
            ->isNotEmpty('First name is missing');

        $validator->check('email')
            ->isNotEmpty('Email address is missing')
            ->isEmail('Invalid email address');

        $validator->check('password')
            ->isNotEmpty('Password is missing');

        // update the model's errors with the validators
        $this->setError( $validator->getErrors() );

        return empty($this->getErrors());
    }

    // ===============================
    // ACL methods
    // The following methods are to grant an authenticated user access to
    // a particular role on a resource (e.g. view a given article ). These are
    // also quite readable when written out e.g. $user->canEdit($article)

    // roles

    /**
     * Return true if "admin" user
     * @return boolean
     */
    public function isAdmin()
    {
        return (isset($this->data['role']) and $this->data['role'] == static::ROLE_ADMIN);
    }

    /**
     * Return true if "editor" user
     * @return boolean
     */
    public function isEditor()
    {
        return (isset($this->data['role']) and $this->data['role'] == static::ROLE_EDITOR);
    }

    /**
     * Return true if "member" user
     * @return boolean
     */
    public function isMember()
    {
        return (isset($this->data['role']) and $this->data['role'] == static::ROLE_MEMBER);
    }

    // permissions
    // TODO use Zend\Acl
    // TODO create Ownable interface for $resource with getOwner()

    /**
     * Return true if user is owner of resource
     * @param Ownable $resource
     * @return boolean
     */
    public function isOwnerOf($resource)
    {
        // no id, no life
        if (! isset($this->data['id']))
            return false;

        // this will fetch objects so we know what we're working with rather
        // than possibly messing about with DBRefs
        $author = $resource->author;

        if ($author instanceof Mongo) {
            return ($author->id == $this->data['id']);
        }

        return false;
    }

    /**
     * Return true if user can view a given resource
     * @param Ownable $resource
     * @return boolean
     */
    public function canView($resource)
    {
        if ($this->isAdmin())
            return true;

        if ($this->isEditor())
            return true; //$this->isEditorFor($resource->author);

        return $this->isOwnerOf($resource);
    }

    /**
     * Return true if user can view a given resource
     * @param Ownable $resource
     * @return boolean
     */
    public function canEdit($resource)
    {
        if ($this->isAdmin())
            return true;

        if ($this->isEditor())
            return true;

        return $this->isOwnerOf($resource);
    }

    /**
     * Return true if user can view a given resource
     * @param Ownable $resource
     * @return boolean
     */
    public function canDelete($resource)
    {
        if ($this->isAdmin())
            return true;

        if ($this->isEditor())
            return true;

        return $this->isOwnerOf($resource);
    }

    // /**
    //  * Return true if user can view a given resource
    //  * @param Article $article
    //  * @return boolean
    //  */
    // public function canSubmit($article)
    // {
    //     if ($this->isAdmin())
    //         return true;
    //
    //     if ($this->isEditor())
    //         return true;
    //
    //     return $this->isOwnerOf($article);
    // }
    //
    // /**
    //  * Return true if user can view a given resource
    //  * @param Article $article
    //  * @return boolean
    //  */
    // public function canApprove($article)
    // {
    //     if ($this->isAdmin())
    //         return true;
    //
    //     if ($this->isEditor())
    //         return true;
    //
    //     return $this->isOwnerOf($article);
    // }
}
