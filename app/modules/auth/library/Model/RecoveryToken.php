<?php
namespace App\Model;

class RecoveryToken extends Base
{
    /**
    * @var array
    */
    protected $fillable = array(
        'selector',
        'token',
        'expire',
        'account_id',
    );

    /**
     * Encrypt password upon setting, set salt too
     */
    public function account()
    {
        return $this->belongsTo('App\\Model\\Account');
    }

    /**
     * Encrypt password upon setting, set salt too
     */
    public function setTokenAttribute($token)
    {
        $this->attributes['token'] = $this->hashToken($token);
    }

    /**
     * Encrypt password upon setting, set salt too
     */
    public function verifyToken($token)
    {
        return ($this->attributes['token'] == $this->hashToken($token));
    }

    /**
     * A method for both setTokenAttribute and verifyToken
     */
    protected function hashToken($token)
    {
        return hash('sha256', $token);
    }

    /**
     * A method for both setTokenAttribute and verifyToken
     * @param string $selector
     * @return App\Model\AuthToken
     */
    public function findValidTokenBySelector($selector)
    {
        $now = date('Y-m-d H:i:s');

        return self::where('selector', $selector)
            ->where('expire', '>=', $now)
            ->first();
    }

    /**
     * Delete all auth tokens of selector
     * @param string $selector
     */
    public function deleteBySelector($selector)
    {
        return self::where('selector', $selector)
            ->delete();
    }
}
