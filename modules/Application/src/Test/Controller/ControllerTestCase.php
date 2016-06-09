<?php
namespace Application\Test\Controller;

use MartynBiz\Slim3Controller\Test\PHPUnit\TestCase;
use MartynBiz\Mongo\Connection;
use Auth\Model\User;

class ControllerTestCase extends TestCase
{
    /**
     * @var Auth\Model\User
     */
    protected $user;

    public function setUp()
    {
        // Config
        $settings = require APPLICATION_PATH . '/config/global.php';

        // Instantiate the app
        $this->app = new \Slim\App($settings);
        $this->container = $this->app->getContainer();

        \MartynBiz\Mongo\Connection::getInstance()->init($settings['settings']['mongo']);

        // initialize all modules in settings > modules > autoload [...]
        $moduleInitializer = new \MartynBiz\Slim3Module\Initializer($this->app, $settings['settings']['module_initializer']);

        // $moduleInitializer->initModules();
        $moduleInitializer->initModuleConfig();
        $moduleInitializer->initDependencies();
        $moduleInitializer->initMiddleware();

        // mock stuff
        $this->container['mail_manager'] = $this->getMockBuilder('Application\Mail')
            ->disableOriginalConstructor()
            ->getMock();

        $this->container['auth'] = $this->getMockBuilder('Auth\Auth')
            ->disableOriginalConstructor()
            ->getMock();

        // router must be handled last otherwise stuff gets frozen before we can mock it
        $moduleInitializer->initRoutes();


        // create fixtures
        $this->user = $this->findOrCreate(new User(), array(
            'first_name' => 'Martyn',
            'last_name' => 'Bissett',
            'email' => 'martyn@example.com',
            'password' => 'mypass',
        ), 'first_name');
    }

    public function tearDown()
    {
        // $this->container['Auth\Model\User']->remove();
    }

    public function login($user)
    {
        // return an identity (eg. email)
        $this->container['auth']
            ->method('getAttributes')
            ->willReturn( array_intersect_key($this->user->toArray(), array_flip(array(
                'id',
                'first_name',
                'last_name',
                'email',
            ))) );

        // by defaut, we'll make isAuthenticated return a false
        $this->container['auth']
            ->method('isAuthenticated')
            ->willReturn( true );

        // by defaut, we'll make isAuthenticated return a false
        $this->container['auth']
            ->method('getCurrentUser')
            ->willReturn( $user );
    }

    /**
     * Will try to find the fixture based on $queryColumn if given, otherwise
     * will create a new row.
     * "Integrity constraint violation" error when I simply use Eloquent's
     * create(...) method.
     */
    protected function findOrCreate($model, $values, $queryColumn=null)
    {
        // try to find if $queryColumn given
        if ($queryColumn) {
            $obj = $model->findOne([
                $queryColumn => $values[$queryColumn]
            ]);

            if ($obj) {
                return $obj;
            }
        }

        $obj = $model->create($values);

        return $obj;
    }
}
