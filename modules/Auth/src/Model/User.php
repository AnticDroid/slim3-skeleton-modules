<?php
namespace Auth\Model;

// use MartynBiz\Mongo;

use Application\Model\Base;

/**
 *
 */
class User extends Base
{
    const ROLE_ADMIN = 'admin';
    const ROLE_EDITOR = 'editor';
    const ROLE_CONTRIBUTOR = 'contributor';

    // collection this model refers to
    protected static $collection = 'users';

    // define on the fields that can be saved
    protected static $whitelist = array(
        'first_name',
        'last_name',
        'email',
        'password',
    );

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
    public function isContributor()
    {
        return (isset($this->data['role']) and $this->data['role'] == static::ROLE_CONTRIBUTOR);
    }

    /**
     * Encrypt password upon setting
     */
    public function setPassword($value)
    {
        $hash = password_hash($value, PASSWORD_BCRYPT, array(
            'cost' => 12,
        ));

        return $hash;
    }

    public function getRole($value)
    {
        switch ($this->data['role'])
        {
            case self::ROLE_ADMIN:
                return 'Admin';
                break;
            case self::ROLE_EDITOR:
                return 'Editor';
                break;
            case self::ROLE_CONTRIBUTOR:
                return 'Contributor';
                break;
        }
    }
}
