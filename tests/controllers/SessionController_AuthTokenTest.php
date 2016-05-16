<?php
namespace Tests\Controllers;

/**
 * TODO Cannot mock cookies, yet. Can't really do much here until then
 */
class SessionController_AuthTokenTest extends ControllerTestCase
{
    public function setUp()
    {
        parent::setUp();

        // stub App\Auth\Auth
        $this->container['auth'] = $this->getMockBuilder('App\\Auth\\Auth')
            ->disableOriginalConstructor()
            ->getMock();
    }


    // ====================================
    // GET /session

    public function test_login_form_shows_when_auth_token_missing()
    {


        // =================================
        // mock method stack, in order

        // $this->container['auth']
        //     ->method('getCookie')
        //     ->with('auth_token')
        //     ->willReturn('1234567890_qwertyuiop1234567890');

        // =================================
        // dispatch

        $this->get('/session', array(
            'auth_token' => '1234567890_qwertyuiop1234567890',
        ) );

        // =================================
        // assertions

        $this->assertController('session');
        $this->assertAction('index');
    }
}
