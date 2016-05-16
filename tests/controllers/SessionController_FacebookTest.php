<?php
namespace Tests\Controllers;

/**
 * This test is slightly different from sessionscontroller as it mocks models
 * It is important that we assert how users are being picked up from the database
 * when they sign in with social media.
 */
class SessionController_FacebookTest extends ControllerTestCase
{
    /**
     * @var Facebook\Helpers\RedirectLoginHelper
     */
    protected $facebookRedirectLoginHelper;

    /**
     * @var Facebook\Response
     */
    protected $facebookResponse;

    public function setUp()
    {
        parent::setUp();

        // stub Facebook\..\FacebookRedirectLoginHelper
        $this->facebookRedirectLoginHelper = $this->getMockBuilder('Facebook\\Helpers\\FacebookRedirectLoginHelper')
            ->disableOriginalConstructor()
            ->getMock();

        // stub Facebook\Response
        $this->facebookResponse = $this->getMockBuilder('Facebook\\FacebookResponse')
            ->disableOriginalConstructor()
            ->getMock();

        // stub Facebook\Response
        $this->facebookGraphUser = $this->getMockBuilder('Facebook\GraphNodes\GraphUser')
            ->disableOriginalConstructor()
            ->getMock();

        // stub Facebook\Facebook
        $this->container['facebook'] = $this->getMockBuilder('Facebook\\Facebook')
            ->disableOriginalConstructor()
            ->getMock();

        // mock method facebook::getRedirectLoginHelper
        $this->container['facebook']
            ->method('getRedirectLoginHelper')
            ->willReturn($this->facebookRedirectLoginHelper);

        // mock method facebook::get
        $this->container['facebook']
            ->method('get')
            ->willReturn($this->facebookResponse);

        // mock method response::getGraphUser
        $this->facebookResponse
            ->method('getGraphUser')
            ->willReturn($this->facebookGraphUser);

        $this->facebookGraphUser
            ->method('asArray')
            ->willReturn( $this->getGraphUserData() );
    }


    // ====================================
    // GET /session/facebook

    public function test_facebook_route_redirects_to_login_url()
    {
        $container = $this->app->getContainer();
        $settings = $container->get('settings');

        $queryParams = array(
            'returnTo' => 'http://jt.testing/home',
        );
        $permissions = array('email');

        // =================================
        // mock method stack, in order

        $loginUrl = $settings['app_domain'] . '/session/facebook/callback?' . http_build_query($queryParams);
        $this->facebookRedirectLoginHelper
            ->method('getLoginUrl')
            // ->with($loginUrl, $permissions) // TODO fix getQueryParams in tests
            ->willReturn('http://facebook.com/path/to/login');

        // =================================
        // dispatch

        $this->post('/session/facebook?' . http_build_query($queryParams) );

        // =================================
        // assertions

        $this->assertController('session');
        $this->assertAction('facebook');
        $this->assertRedirectsTo('http://facebook.com/path/to/login');
    }


    // ====================================
    // GET /session/facebook/callback

    public function test_callback_forwards_when_access_token_exception_thrown()
    {
        $container = $this->app->getContainer();

        // =================================
        // mock method stack, in order

        $this->facebookRedirectLoginHelper
            ->method('getAccessToken')
            ->will( $this->throwException(new \Exception('uh-oh, there was an error')) );

        // =================================
        // dispatch

        $this->get('/session/facebook/callback');

        // =================================
        // assertions

        $this->assertController('session');
        $this->assertAction('index');
    }

    public function test_callback_forwards_when_get_exception_thrown()
    {
        $container = $this->app->getContainer();

        // =================================
        // mock method stack, in order

        $this->facebookRedirectLoginHelper
            ->method('getAccessToken')
            ->willReturn('1234567890');

        $this->container['facebook']
            ->method('get')
            ->will( $this->throwException(new \Exception('uh-oh, there was an error')) );

        // =================================
        // dispatch

        $this->get('/session/facebook/callback');

        // =================================
        // assertions

        $this->assertController('session');
        $this->assertAction('index');
    }

    /**
     * @depends test_callback_forwards_when_get_exception_thrown
     */
    public function test_callback_get_method_accepts_attributes_and_token_as_argument()
    {
        $container = $this->app->getContainer();

        // =================================
        // mock method stack, in order

        $this->facebookRedirectLoginHelper
            ->method('getAccessToken')
            ->willReturn('1234567890');

        // throw an exception just to halt the script
        $this->container['facebook']
            ->method('get')
            ->with('/me?fields=id,name,first_name,last_name,email', '1234567890')
            ->will( $this->throwException(new \Exception('uh-oh, there was an error')) );

        // =================================
        // dispatch

        $this->get('/session/facebook/callback');
    }


    // ====================================
    // from this point onwards we're testing that the correct user is picked up
    // from a successful Facebook login

    public function test_callback_finds_account_when_facebook_id_found()
    {
        $container = $this->app->getContainer();
        $graphUserData = $this->getGraphUserData();

        // =================================
        // mock method stack, in order

        // we're gonna mock the account model so we can control the flow of things
        $this->container['model.account'] = $this->getMockBuilder('App\\Model\\Account')
            ->disableOriginalConstructor()
            ->getMock();

        $this->container['model.account']
            ->expects( $this->once() )
            ->method('findByFacebookId')
            ->with($graphUserData['id'])
            ->willReturn( $this->account );

        $this->container['model.account']
            ->expects( $this->never() )
            ->method('create');

        // =================================
        // dispatch

        $this->get('/session/facebook/callback');

        // =================================
        // assertions

        $this->assertController('session');
        $this->assertAction('facebookCallback');
    }

    public function test_callback_finds_account_when_email_found()
    {
        $container = $this->app->getContainer();
        $graphUserData = $this->getGraphUserData();

        // =================================
        // mock method stack, in order

        // we're gonna mock the account model so we can control the flow of things
        $this->container['model.account'] = $this->getMockBuilder('App\\Model\\Account')
            ->disableOriginalConstructor()
            ->getMock();

        $this->container['model.account']
            ->expects( $this->once() )
            ->method('findByFacebookId')
            ->with($graphUserData['id'])
            ->willReturn( null );

        $this->container['model.account']
            ->expects( $this->once() )
            ->method('findByEmail')
            ->with($graphUserData['email'])
            ->willReturn( $this->account );

        $this->container['model.account']
            ->expects( $this->never() )
            ->method('create');

        // =================================
        // dispatch

        $this->get('/session/facebook/callback');

        // =================================
        // assertions

        $this->assertController('session');
        $this->assertAction('facebookCallback');

        // assert that facebook id was set
        $this->assertEquals($graphUserData['id'], $this->account->getMeta('facebook_id'));
    }

    public function test_callback_with_remember_me_calls_auth_remember_method()
    {
        $container = $this->app->getContainer();
        $graphUserData = $this->getGraphUserData();

        // =================================
        // mock method stack, in order

        // we're gonna mock the account model so we can control the flow of things
        $this->container['model.account'] = $this->getMockBuilder('App\\Model\\Account')
            ->disableOriginalConstructor()
            ->getMock();

        $this->container['model.account']
            ->expects( $this->once() )
            ->method('findByFacebookId')
            ->with($graphUserData['id'])
            ->willReturn( null );

        $this->container['model.account']
            ->expects( $this->once() )
            ->method('findByEmail')
            ->with($graphUserData['email'])
            ->willReturn( $this->account );

        $this->container['model.account']
            ->expects( $this->never() )
            ->method('create');

        // handle remember_me

        $this->container['auth']
            ->expects( $this->once() )
            ->method('remember');

        // this method should never be called
        $this->container['auth']
            ->expects( $this->never() )
            ->method('forget');

        // =================================
        // dispatch

        $this->get('/session/facebook/callback?remember_me=1');

        // =================================
        // assertions

        $this->assertController('session');
        $this->assertAction('facebookCallback');

        // assert that facebook id was set
        $this->assertEquals($graphUserData['id'], $this->account->getMeta('facebook_id'));
    }

    public function test_callback_without_remember_me_calls_auth_forget_method()
    {
        $container = $this->app->getContainer();
        $graphUserData = $this->getGraphUserData();

        // =================================
        // mock method stack, in order

        // we're gonna mock the account model so we can control the flow of things
        $this->container['model.account'] = $this->getMockBuilder('App\\Model\\Account')
            ->disableOriginalConstructor()
            ->getMock();

        $this->container['model.account']
            ->expects( $this->once() )
            ->method('findByFacebookId')
            ->with($graphUserData['id'])
            ->willReturn( null );

        $this->container['model.account']
            ->expects( $this->once() )
            ->method('findByEmail')
            ->with($graphUserData['email'])
            ->willReturn( $this->account );

        $this->container['model.account']
            ->expects( $this->never() )
            ->method('create');

        // handle remember_me

        // we need to mock this for the condition in the method to be true
        $this->container['auth']
            ->expects( $this->never() )
            ->method('remember');

        // we need to mock this for the condition in the method to be true
        $this->container['auth']
            ->expects( $this->once() )
            ->method('forget');

        // =================================
        // dispatch

        $this->get('/session/facebook/callback');

        // =================================
        // assertions

        $this->assertController('session');
        $this->assertAction('facebookCallback');

        // assert that facebook id was set
        $this->assertEquals($graphUserData['id'], $this->account->getMeta('facebook_id'));
    }

    // TODO how to mock create() ??
    // public function test_callback_creates_account_when_user_not_found()
    // {
    //     $container = $this->app->getContainer();
    //     $graphUserData = $this->getGraphUserData();
    //
    //     // =================================
    //     // mock method stack, in order
    //
    //     // we're gonna mock the account model so we can control the flow of things
    //     $this->container['model.account'] = $this->getMockBuilder('App\\Model\\Account')
    //         ->disableOriginalConstructor()
    //         ->getMock();
    //
    //     $this->container['model.account']
    //         ->expects( $this->once() )
    //         ->method('findByFacebookId')
    //         ->with($graphUserData['id'])
    //         ->willReturn( null );
    //
    //     $this->container['model.account']
    //         ->expects( $this->once() )
    //         ->method('findByEmail')
    //         ->with($graphUserData['email'])
    //         ->willReturn( null );
    //
    //     $this->container['model.account']
    //         ->expects( $this->once() )
    //         ->method('create')
    //         // ->with(...)
    //         ->willReturn( $this->account );
    //
    //     // =================================
    //     // dispatch
    //
    //     $this->get('/session/facebook/callback');
    //
    //     // =================================
    //     // assertions
    //
    //     $this->assertController('session');
    //     $this->assertAction('facebookCallback');
    //
    //     // assert that facebook id was set
    //     $this->assertEquals($graphUserData['id'], $this->account->getMeta('facebook_id'));
    // }


    // data providers

    protected function getGraphUserData($data=array())
    {
        return array_merge( array(
            'id' => 'fb1234567890',
            'name' => $this->account->name,
            'first_name' => $this->account->first_name,
            'last_name' => $this->account->last_name,
            'email' => $this->account->email,
        ), $data );
    }
}
