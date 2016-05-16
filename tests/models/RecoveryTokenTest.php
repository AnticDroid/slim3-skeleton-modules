<?php
namespace Tests\Models;

use App\Model\Account;
use App\Model\RecoveryToken;

class RecoveryTokenTest extends \Tests\TestCase
{
    // public function tearDown()
    // {
    //     // as we have foreign key constraints on meta, we cannot use
    //     // truncate (even if the table is empty). so we need to temporarily
    //     // turn off FOREIGN_KEY_CHECKS
    //
    //     $connection = (new RecoveryToken())->getConnection();
    //     $connection->statement('SET FOREIGN_KEY_CHECKS=0;');
    //
    //     // // clear tables
    //     Account::truncate();
    //     RecoveryToken::truncate();
    //
    //     $connection->statement('SET FOREIGN_KEY_CHECKS=1;');
    // }

    public function test_instantiation()
    {
        $passwordToken = new RecoveryToken();
        $this->assertTrue($passwordToken instanceof RecoveryToken);
    }

    public function test_token_is_hashed_and_verified()
    {
        $passwordToken = new RecoveryToken();

        $token = 'rawtoken';
        $passwordToken->token = $token;

        $this->assertNotEquals($token, $passwordToken->token);
        $this->assertTrue($passwordToken->verifyToken($token));
    }

    public function test_account_relationship()
    {
        // due to foreign key constraints, we need to create account first
        $passwordToken = Account::create( array(
            'first_name' => 'Martyn',
            'last_name' => 'Bissett',
            'email' => 'martyn+' . uniqid() . '@metroworks.co.jp',
            'password' => 'MyP@ssw0rd',
            'agreement' => '1',
            'more_info' => '',
        ) )->recovery_token()->create( array( // this create() will return $passwordToken
            'selector' => '1234567890',
            'token' => 'qwertyuiop1234567890',
        ) );

        $account = $passwordToken->account;

        $this->assertTrue($account instanceof Account);
        $this->assertEquals('Martyn', $account->first_name);
    }

    public function test_find_valid_token_by_selector_returns_recovery_token()
    {
        // due to foreign key constraints, we need to create account first
        Account::create( array(
            'first_name' => 'Martyn',
            'last_name' => 'Bissett',
            'email' => 'martyn+' . uniqid() . '@metroworks.co.jp',
            'password' => 'MyP@ssw0rd',
            'agreement' => '1',
            'more_info' => '',
        ) )->recovery_token()->create( array( // this create() will return $passwordToken
            'selector' => '1234567890',
            'token' => 'qwertyuiop1234567890',
            'expire' => '2099-01-01 00:00:00',
        ) );

        $passwordToken = RecoveryToken::findValidTokenBySelector('1234567890');

        $this->assertTrue($passwordToken instanceof RecoveryToken);
        $this->assertEquals('1234567890', $passwordToken->selector);
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
        ) )->recovery_token()->create( array( // this create() will return $passwordToken
            'selector' => '1234567890',
            'token' => 'qwertyuiop1234567890',
            'expire' => '2000-01-01 00:00:00',
        ) );

        $passwordToken = RecoveryToken::findValidTokenBySelector('1234567890');

        $this->assertNull($passwordToken);
    }

    public function test_delete_by_selector_deletes_recovery_token()
    {
        // due to foreign key constraints, we need to create account first
        Account::create( array(
            'first_name' => 'Martyn',
            'last_name' => 'Bissett',
            'email' => 'martyn+' . uniqid() . '@metroworks.co.jp',
            'password' => 'MyP@ssw0rd',
            'agreement' => '1',
            'more_info' => '',
        ) )->recovery_token()->create( array( // this create() will return $passwordToken
            'selector' => '1234567890',
            'token' => 'qwertyuiop1234567890',
            'expire' => '2000-01-01 00:00:00',
        ) );

        RecoveryToken::deleteBySelector('1234567890');

        $passwordToken = RecoveryToken::where('selector', '1234567890')->first();

        $this->assertNull($passwordToken);
    }



    public function getRecoveryTokenData($data=array())
    {
        return array_merge( array(
            'selector' => uniqid(),
            'token' => md5(time()),
        ), $data );
    }
}
