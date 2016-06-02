<?php
namespace Tests\Models;

use App\Model\Account;
use App\Model\Meta;
use App\Model\AuthToken;

class AccountTest extends \Tests\TestCase //\PHPUnit_Framework_TestCase  //\PHPUnit_Framework_TestCase
{
    // public function tearDown()
    // {
    //     // as we have foreign key constraints on meta, we cannot use
    //     // truncate (even if the table is empty). so we need to temporarily
    //     // turn off FOREIGN_KEY_CHECKS
    //
    //     $connection = (new Account())->getConnection();
    //     $connection->statement('SET FOREIGN_KEY_CHECKS=0;');
    //
    //     // // clear tables
    //     Account::truncate();
    //     Meta::truncate();
    //     AuthToken::truncate();
    //
    //     $connection->statement('SET FOREIGN_KEY_CHECKS=1;');
    // }

    public function test_instantiation()
    {
        $account = new Account();
        $this->assertTrue($account instanceof Account);
    }

    public function test_password_and_salt_set_when_password_is_set()
    {
        $account = new Account();

        $password = 'rawpass';
        $account->password = $password;

        $this->assertNotEquals($password, $account->password);
        $this->assertTrue(strpos('$2a$', $account->password) == 0);
        $this->assertTrue(strlen($account->salt) > 20); // i think it's set to 25 in the app
    }

    public function test_password_and_salt_set_when_password_is_empty()
    {
        $account = new Account();
        $account->save();

        $this->assertTrue(strpos('$2a$', $account->password) == 0);
    }

    public function test_username_generated_on_create()
    {
        $account = Account::create($this->getAccountData());

        $this->assertNotNull($account->username);

        $usernameBase = strtolower($account->first_name . '.' . $account->last_name);
        $this->assertTrue(strpos($usernameBase, $account->username) == 0); // should start with $usernameBase
    }

    public function test_username_generated_on_save()
    {
        $account = new Account($this->getAccountData());

        $account->save();

        $this->assertNotNull($account->username);

        $usernameBase = strtolower($account->first_name . '.' . $account->last_name);
        $this->assertTrue(strpos($usernameBase, $account->username) == 0); // should start with $usernameBase
    }

    public function test_name_generated_on_create_if_blank()
    {
        $account = Account::create($this->getAccountData());

        $this->assertNotNull($account->username);

        $expected = $account->first_name . ' ' . $account->last_name;
        $this->assertEquals($expected, $account->name);
    }

    public function test_name_generated_on_save_if_blank()
    {
        $account = new Account($this->getAccountData());

        $account->save();

        $this->assertNotNull($account->username);

        $expected = $account->first_name . ' ' . $account->last_name;
        $this->assertEquals($expected, $account->name);
    }

    public function test_different_username_on_each_save()
    {
        $account1 = new Account($this->getAccountData());
        $account1->save();

        $account2 = new Account($this->getAccountData());
        $account2->save();

        $usernameBase = strtolower($account1->first_name . '.' . $account1->last_name);
        $this->assertEquals($usernameBase, $account1->username);

        $this->assertNotEquals($account1->username, $account2->username);
    }

    public function test_meta_relationship()
    {
        $account = Account::create( $this->getAccountData() );
        $meta = $account->meta()->create(array(
            'name' => 'facebook_id',
            'value' => '0987654321',
        ));

        $this->assertTrue($meta instanceof Meta);
        $this->assertEquals('0987654321', $meta->value);
    }

    public function test_auth_token_relationship()
    {
        $account = Account::create( $this->getAccountData() );
        $authToken = $account->auth_token()->create(array(
            'selector' => '1234567890',
            'token' => 'qwertyuiop1234567890',
        ));

        $this->assertTrue($authToken instanceof AuthToken);
        $this->assertEquals('1234567890', $authToken->selector);
    }

    public function test_get_meta_method()
    {
        // fixtures
        $account = Account::create( $this->getAccountData() );
        $meta = $account->meta()->create(array(
            'name' => 'facebook_id',
            'value' => '0987654321',
        ));

        $this->assertEquals('0987654321', $account->getMeta('facebook_id'));
    }

    /**
     * @depends test_get_meta_method
     */
    public function test_set_meta_method_when_meta_exists()
    {
        // fixtures
        $account = Account::create( $this->getAccountData() );
        $meta = $account->meta()->create(array(
            'name' => 'facebook_id',
            'value' => '0987654321',
        ));

        $account->setMeta('facebook_id', '10101010');

        $this->assertEquals('10101010', $account->getMeta('facebook_id'));
    }

    /**
     * @depends test_get_meta_method
     */
    public function test_set_meta_method_when_meta_doesnt_exist()
    {
        // fixtures
        $account = Account::create( $this->getAccountData() );

        $account->setMeta('facebook_id', '10101010');

        $this->assertEquals('10101010', $account->getMeta('facebook_id'));
    }

    /**
     * @depends test_get_meta_method
     * @depends test_set_meta_method_when_meta_exists
     * @depends test_set_meta_method_when_meta_doesnt_exist
     */
    public function test_set_invalid_meta_returns_null()
    {
        // fixtures
        $account = Account::create( $this->getAccountData() );

        $account->setMeta('invalid', '(－‸ლ)');

        $this->assertEquals(null, @$account->getMeta('invalid'));
    }

    /**
     * @depends test_get_meta_method
     * @depends test_set_meta_method_when_meta_exists
     * @depends test_set_meta_method_when_meta_doesnt_exist
     */
    public function test_set_source_meta()
    {
        // fixtures
        $account = Account::create( $this->getAccountData() );

        $account->setMeta('source', 'evacomics');

        $this->assertEquals('evacomics', $account->getMeta('source'));
    }

    public function test_find_by_email()
    {
        // fixtures
        $accountData = $this->getAccountData();
        Account::create($accountData);

        $account = Account::findByEmail($accountData['email']);

        $this->assertEquals($accountData['email'], $account->email);
    }

    public function test_find_by_facebook_id()
    {
        // fixtures
        $accountData = $this->getAccountData();
        $account = Account::create( $accountData );
        $meta = $account->meta()->create(array(
            'name' => 'facebook_id',
            'value' => '0987654321',
        ));

        $account = Account::findByFacebookId('0987654321');

        // confirm on email value
        $this->assertEquals($accountData['email'], $account->email);
    }

    public function test_facebook_id_appended_to_array()
    {
        // fixtures
        $accountData = $this->getAccountData();
        $account = Account::create( $accountData );
        $meta = $account->meta()->create(array(
            'name' => 'facebook_id',
            'value' => '0987654321',
        ));

        // confirm on email value
        $this->assertTrue( array_key_exists('facebook_id', $account->toArray()) );
    }

    public function test_find_by_auth_token_selector()
    {
        // fixtures
        $accountData = $this->getAccountData();

        $account = Account::create( $accountData );

        $authToken = $account->auth_token()->create(array(
            'selector' => '1234567890',
            'token' => 'qwertyuiop1234567890',
        ));

        $account = Account::findByAuthTokenSelector('1234567890');

        // confirm on email value
        $this->assertEquals($accountData['email'], $account->email);
    }



    public function getAccountData($data=array())
    {
        return array_merge( array(
            'first_name' => 'Martyn',
            'last_name' => 'Bissett',
            'email' => 'martyn+' . uniqid() . '@metroworks.co.jp', // must be unique
            'password' => 'MyP@ssw0rd',
            'agreement' => '1',
            'more_info' => '', // honey pot, this should be empty
        ), $data );
    }
}
