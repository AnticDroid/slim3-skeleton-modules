<?php
namespace App\Modules\Auth\Model;

use App\Modules\Auth\Model\Meta;
use App\Modules\Auth\Model\AuthToken;

class Account extends Base
{
    const BLOWFISH_LEVEL = '08';

    const BACKEND_JAPANTRAVEL = 'japantravel';
    const BACKEND_FACEBOOK = 'facebook';

    /**
    * @var array
    */
    protected $fillable = array(
        'name',
        'first_name',
        'last_name',
        'username',
        'email',
        'password',
        'salt',
        'lang',
        'enabled',
    );

    /**
     * Requires a matching get*Attribute() method (e.g. getFacebookIdAttribute)
     */
    protected $appends = array(
        'facebook_id',
    );

    public function meta()
    {
        return $this->hasMany('App\\Model\\Meta'); //, 'account_id');
    }

    public function auth_token()
    {
        return $this->hasOne('App\\Model\\AuthToken'); //, 'account_id');
    }

    public function recovery_token()
    {
        return $this->hasOne('App\\Model\\RecoveryToken'); //, 'account_id');
    }

    public static function boot()
    {
        parent::boot();

        Account::saving(function ($account) {

            // set the password to a random string if empty, this is typically the
            // case when a silent account is created during a facebook login
            if (empty($account->password)) {
                $account->password = uniqid(); // it'll get hashed anyway
            }

            // username, if not set, generate from first and last name - ensure it's unique
            if (empty($account->username)) {

                $base = strtolower($account->first_name . '.' . $account->last_name);

                do {
                    $username = $base . @$suffix;
                    $duplicate = Account::where('username', '=', $username)->first();
                } while($duplicate and $suffix = rand(1000, 9999));

                // return the original/ generated username
                $account->username = $username;
            }

            // if name is not set, generate it from first and last name
            if (empty($account->name)) {
                $account->name = $account->first_name . ' ' . $account->last_name;
            }
        });
    }

    /**
     * Encrypt password upon setting, set salt too
     */
    public function setPasswordAttribute($value)
    {
        // TODO as Text_Password is only being used to generate the salt, we can replace/remove it :)
        $generator = new \Text_Password();
        $salt = $generator->create(25, 'unpronounceable', 'alphabetical');

        $blowfish_salt = '$2a$' . self::BLOWFISH_LEVEL . '$' . $salt . '$';

        $this->attributes['salt'] = $salt;
        $this->attributes['password'] = crypt($value, $blowfish_salt);
    }

    /**
     * This is required for the append
     */
    public function getFacebookIdAttribute()
    {
        return $this->getMeta('facebook_id');
    }

    /**
     * This will look up the value from meta table
     * @return string
     */
    public function getMeta($name)
    {
        $meta = $this->meta()
            ->where('name', $name)
            ->first();

        return ($meta) ? $meta->value : null;
    }

    /**
     * This will look up the value from meta table
     * @param string $facebookId
     */
    public function setMeta($name, $value)
    {
        // not sure the best means to whitelist yet, might need to ask on SO
        // anyway for now, just put in a chack here - should use validation in Meta
        if (!in_array($name, Meta::$validNames)) {
            return false;
        }

        $meta = $this->meta()
            ->where('name', $name)
            ->first();

        if ($meta) { // if found, update
            $meta->value = $value;
            $meta->save();
        } else { // otherwise, create
            $this->meta()->create(array(
                'name' => $name,
                'value' => $value,
            ));
        }
    }

    /**
     * Scope a query to find a user by email
     * Makes testing easier when we don't have to chain eloquent methods
     * @param Query? $query
     * @param string $email
     * @return Account|null
     */
    public function findByEmail($email)
    {
        return self::where('email', $email)->first();
    }

    /**
     * Scope a query to find a user by email
     * Makes testing easier when we don't have to chain eloquent methods
     * @param Query? $query
     * @param string $email
     * @return Account|null
     */
    public function findByFacebookId($facebookId)
    {
        $meta = Meta::where('name', 'facebook_id')
            ->where('value', $facebookId)
            ->first();

        if ($meta) {
            return $meta->account;
        } else {
            return null;
        }
    }

    /**
     * Scope a query to find a user by email
     * Makes testing easier when we don't have to chain eloquent methods
     * @param Query? $query
     * @param string $email
     * @return Account|null
     */
    public function findByAuthTokenSelector($selector)
    {
        $authToken = AuthToken::where('selector', $selector)
            ->first();

        if ($authToken) {
            return $authToken->account;
        } else {
            return null;
        }
    }
}
