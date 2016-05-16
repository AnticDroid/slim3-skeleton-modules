<?php
namespace Tests\Controllers;

use App\Model\Account;
use App\Model\Meta;
use App\Model\RecoveryToken;

abstract class ControllerTestCase extends \Tests\TestCase
{
    /**
     * @var Slim\Container
     */
    protected $container;

    /**
     * @var App\Model\Account
     */
    protected $account;

    /**
     * @var App\Model\PasswordRecovery
     */
    protected $passwordToken;

    /**
     * @var App\Model\Meta
     */
    protected $metaFacebookId;

    public function setUp()
    {
        parent::setUp();


        // =========================
        // create fixtures

        $this->account = $this->findOrCreate(new Account(), array(
            'first_name' => 'Martyn',
            'last_name' => 'Bissett',
            'name' => 'Martyn Bissett',
            'username' => 'martyn.bissett',
            'email' => 'martyn@example.com',
            'password' => 'mypass',
        ), 'first_name');

        $this->metaFacebookId = $this->findOrCreate(new Meta(), array(
            'account_id' => $this->account->id,
            'name' => 'facebook_id',
            'value' => '1234567890',
        ), 'value');

        $this->recoveryToken = $this->findOrCreate(new RecoveryToken(), array(
            'account_id' => $this->account->id,
            'selector' => '1234567890',
            'token' => 'qwertyuiop1234567890',
            'expire' => date('Y-m-d H:i:s', strtotime("+3 months", time())),
        ), 'selector');

        // =========================
        // Instantiate the app and container
        $settings = require APPLICATION_PATH . '/config/global.php';
        $this->app = $app = new \Slim\App($settings);
        $this->container = $app->getContainer();


        // =========================
        // Set up dependencies
        require APPLICATION_PATH . '/dependencies.php';


        // =========================
        // Create test stubs
        // In some cases, where services have become "frozen", we need to define
        // mocks before they are loaded

        //  auth service
        $this->container['auth'] = $this->getMockBuilder('App\\Auth\\Auth')
            ->disableOriginalConstructor()
            ->getMock();

        //  auth service
        $this->container['mail_manager'] = $this->getMockBuilder('App\\Mail\\Manager')
            ->disableOriginalConstructor()
            ->getMock();


        // =========================
        // Register middleware
        require APPLICATION_PATH . '/middleware.php';


        // =========================
        // Register routes
        require APPLICATION_PATH . '/routes.php';

        // Helper functions
        require APPLICATION_PATH . '/helpers.php';

        $this->app = $app;
    }

    public function login($user)
    {
        // return an identity (eg. email)
        $this->container['auth']
            ->method('getAttributes')
            ->willReturn( array_intersect_key($this->account->toArray(), array_flip(array(
                'first_name',
                'last_name',
                'email',
                'username',
                'name',
                'id',
            ))) );

        // by defaut, we'll make isAuthenticated return a false
        $this->container['auth']
            ->method('isAuthenticated')
            ->willReturn( true );
    }

    /**
     * Will generate an article stub for use in this test. Sometimes we want to
     * mock methods of the model instance such as save, and ensure values
     * are being set etc
     * @return Article_mock
     */
    public function factoryAccountStub()
    {
        return $this->getMockBuilder('App\Model\Account')
                    ->disableOriginalConstructor()
                    ->getMock();
    }
}
