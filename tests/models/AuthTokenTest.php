<?php
namespace Tests\Models;

use App\Model\Account;
use App\Model\AuthToken;

class AuthTokenTest extends \Tests\TestCase //\PHPUnit_Framework_TestCase
{
    // public function tearDown()
    // {
    //     // as we have foreign key constraints on meta, we cannot use
    //     // truncate (even if the table is empty). so we need to temporarily
    //     // turn off FOREIGN_KEY_CHECKS
    //
    //     $connection = (new AuthToken())->getConnection();
    //     $connection->statement('SET FOREIGN_KEY_CHECKS=0;');
    //
    //     // // clear tables
    //     Account::truncate();
    //     AuthToken::truncate();
    //
    //     $connection->statement('SET FOREIGN_KEY_CHECKS=1;');
    // }

    public function test_instantiation()
    {
        $authToken = new AuthToken();
        $this->assertTrue($authToken instanceof AuthToken);
    }

    public function test_token_is_hashed_and_verified()
    {
        $authToken = new AuthToken();

        $token = 'rawtoken';
        $authToken->token = $token;

        $this->assertNotEquals($token, $authToken->token);
        $this->assertTrue($authToken->verifyToken($token));
    }

    public function test_account_relationship()
    {
        // due to foreign key constraints, we need to create account first
        $authToken = Account::create( array(
            'first_name' => 'Martyn',
            'last_name' => 'Bissett',
            'email' => 'martyn+' . uniqid() . '@metroworks.co.jp',
            'password' => 'MyP@ssw0rd',
            'agreement' => '1',
            'more_info' => '',
        ) )->auth_token()->create( array( // this create() will return $authToken
            'selector' => '1234567890',
            'token' => 'qwertyuiop1234567890',
        ) );

        $account = $authToken->account;

        $this->assertTrue($account instanceof Account);
        $this->assertEquals('Martyn', $account->first_name);
    }

    public function test_find_valid_token_by_selector_returns_auth_token()
    {
        // due to foreign key constraints, we need to create account first
        Account::create( array(
            'first_name' => 'Martyn',
            'last_name' => 'Bissett',
            'email' => 'martyn+' . uniqid() . '@metroworks.co.jp',
            'password' => 'MyP@ssw0rd',
            'agreement' => '1',
            'more_info' => '',
        ) )->auth_token()->create( array( // this create() will return $authToken
            'selector' => '1234567890',
            'token' => 'qwertyuiop1234567890',
            'expire' => '2099-01-01 00:00:00',
        ) );

        $authToken = AuthToken::findValidTokenBySelector('1234567890');

        $this->assertTrue($authToken instanceof AuthToken);
        $this->assertEquals('1234567890', $authToken->selector);
    }

    public function test_find_valid_token_by_selector_returns_null_when_expired()
    {
        // due to foreign key constraints, we need to create account first
        Account::create( array(
            'first_name' => 'Martyn',
            'last_name' => 'Bissett',
            'email' => 'martyn+' . uniqid() . '@metroworks.co.jp',
            'password' => 'MyP@ssw0rd',
            'agreement' => '1',
            'more_info' => '',
        ) )->auth_token()->create( array( // this create() will return $authToken
            'selector' => '1234567890',
            'token' => 'qwertyuiop1234567890',
            'expire' => '2000-01-01 00:00:00',
        ) );

        $authToken = AuthToken::findValidTokenBySelector('1234567890');

        $this->assertNull($authToken);
    }

    public function test_delete_by_selector_deletes_auth_token()
    {
        // due to foreign key constraints, we need to create account first
        Account::create( array(
            'first_name' => 'Martyn',
            'last_name' => 'Bissett',
            'email' => 'martyn+' . uniqid() . '@metroworks.co.jp',
            'password' => 'MyP@ssw0rd',
            'agreement' => '1',
            'more_info' => '',
        ) )->auth_token()->create( array( // this create() will return $authToken
            'selector' => '1234567890',
            'token' => 'qwertyuiop1234567890',
            'expire' => '2000-01-01 00:00:00',
        ) );

        AuthToken::deleteBySelector('1234567890');

        $authToken = AuthToken::where('selector', '1234567890')->first();

        $this->assertNull($authToken);
    }



    public function getAuthTokenData($data=array())
    {
        return array_merge( array(
            'selector' => uniqid(),
            'token' => md5(time()),
        ), $data );
    }
}
