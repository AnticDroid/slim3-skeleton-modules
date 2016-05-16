<?php
namespace App\Model;

class Meta extends Base
{
    protected $table = 'meta';

    protected $fillable = array(
        'name',
        'value',
        'account_id',
    );

    public static $validNames = array(
        'facebook_id',
        'source',
    );

    public function account()
    {
        return $this->belongsTo('App\\Model\\Account'); //, 'account_id');
    }

    /**
     * Find meta facebook id, and return account
     * Makes testing easier when we don't have to chain eloquent methods
     * @param string $facebookId
     * @param App\Model\Meta $metaModel
     * @return Account|null
     */
    public function findFacebookIdByAccount(Account $account)
    {
        $meta = $metaModel->where('name', 'facebook_id')
            ->where('value', $facebookId)
            ->first();

        if ($meta) {
            return $meta->account;
        } else {
            return null;
        }
    }
}
