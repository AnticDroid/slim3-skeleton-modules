<?php
namespace Tests\Controllers;

use Wordup\Model\Account;

class AccountsControllerTest extends ControllerTestCase
{
    public function test_create_route()
    {
        // =================================
        // dispatch

        $this->get('/accounts');

        // =================================
        // assertions

        $this->assertController('accounts');
        $this->assertAction('create');
        $this->assertStatusCode(200);
    }

    public function test_post_action_redirects_to_home_with_valid_params()
    {
        // =================================
        // mock method stack, in order

        // we need to mock this for the condition in the method to be true
        $this->container['mail_manager']
            ->expects( $this->once() )
            ->method('sendWelcomeEmail');

        // =================================
        // dispatch

        $this->post('/accounts', $this->getPostData( array() ) );

        // =================================
        // assertions

        $this->assertController('accounts');
        $this->assertAction('post');
        $this->assertRedirectsTo('/');
    }

    public function test_post_action_redirects_to_return_to_with_valid_params()
    {
        // =================================
        // mock method stack, in order

        // we need to mock this for the condition in the method to be true
        $this->container['auth']
            ->expects( $this->once() )
            ->method('setAttributes');

        // we need to mock this for the condition in the method to be true
        $this->container['mail_manager']
            ->expects( $this->once() )
            ->method('sendWelcomeEmail');

        // =================================
        // dispatch

        $this->post('/accounts', $this->getPostData( array(
            'returnTo' => 'http://www.jt.testing/resource/99',
        ) ));

        // =================================
        // assertions

        $this->assertController('accounts');
        $this->assertAction('post');
        $this->assertRedirectsTo('http://www.jt.testing/resource/99');
    }

    /**
     * @dataProvider getInvalidPostData
     */
    public function test_post_forwards_to_create_with_invalid_params($params)
    {
        // =================================
        // mock method stack, in order

        // we need to mock this for the condition in the method to be true
        $this->container['mail_manager']
            ->expects( $this->never() )
            ->method('sendWelcomeEmail');

        // =================================
        // dispatch

        $this->post('/accounts', $params);

        // =================================
        // assertions

        $this->assertController('accounts');
        $this->assertAction('create');
    }

    /**
     * @expectedException App\Exception\InvalidReturnToUrl
     */
    public function test_post_throws_exception_when_bad_return_to()
    {
        // =================================
        // mock method stack, in order

        // actually, although this an invalid returnTo, we stil will accept
        // the registration and an email will be sent. perhaps we ought to have a
        // default returnto
        $this->container['mail_manager']
            ->expects( $this->once() )
            ->method('sendWelcomeEmail');

        // =================================
        // dispatch

        $this->post('/accounts', $this->getPostData( array(
            'returnTo' => 'http://www.iamshifty.com/',
        )) );
    }

    public function getPostData($data=array())
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

    public function getInvalidPostData()
    {
        return array(
            array(
                array(
                    // 'first_name' => 'Martyn',
                    'last_name' => 'Bissett',
                    'email' => 'martyn@metroworks.co.jp',
                    'password' => 'MyP@ssw0rd',
                    'agreement' => '1',
                    'more_info' => '',
                ),
            ),
            array(
                array(
                    'first_name' => 'Martyn',
                    // 'last_name' => 'Bissett',
                    'email' => 'martyn@metroworks.co.jp',
                    'password' => 'MyP@ssw0rd',
                    'agreement' => '1',
                    'more_info' => '',
                ),
            ),
            array(
                array(
                    'first_name' => 'Martyn',
                    'last_name' => 'Bissett',
                    // 'email' => 'martyn@metroworks.co.jp',
                    'password' => 'MyP@ssw0rd',
                    'agreement' => '1',
                    'more_info' => '',
                ),
            ),
            array(
                array(
                    'first_name' => 'Martyn',
                    'last_name' => 'Bissett',
                    'email' => 'martyn@metroworks.co.jp',
                    // 'password' => 'MyP@ssw0rd',
                    'agreement' => '1',
                    'more_info' => '',
                ),
            ),
            array(
                array(
                    'first_name' => 'Martyn',
                    'last_name' => 'Bissett',
                    'email' => 'martyn@example', // invalid email
                    'password' => 'MyP@ssw0rd',
                    'more_info' => '',
                ),
            ),
            array(
                array(
                    'first_name' => 'Martyn',
                    'last_name' => 'Bissett',
                    'email' => 'martyn@metroworks.co.jp',
                    'password' => 'MyP@ssw0rd',
                    // 'agreement' => '1',
                    'more_info' => '',
                ),
            ),
            array(
                array(
                    'first_name' => 'Martyn',
                    'last_name' => 'Bissett',
                    'email' => 'martyn@metroworks.co.jp',
                    'password' => 'MyP@ssw0rd',
                    'agreement' => '1',
                    'more_info' => 'I am a bot, and stupidly filled this field',
                ),
            ),
        );
    }
}
