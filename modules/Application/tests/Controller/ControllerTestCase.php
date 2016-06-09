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
        $app = $GLOBALS["app"];

        $this->app = $app;
        $this->container = $app->getContainer();

        // mock stuff
        $this->container['mail_manager'] = $this->getMockBuilder('Application\Mail')
            ->disableOriginalConstructor()
            ->getMock();

        $this->container['auth'] = $this->getMockBuilder('Auth\Auth')
            ->disableOriginalConstructor()
            ->getMock();

        // create fixtures
        $this->user = $this->findOrCreate(new User(), array(
            'first_name' => 'Martyn',
            'last_name' => 'Bissett',
            'email' => 'martyn@example.com',
            'password' => 'mypass',
        ), 'first_name');
    }

    public function login($user)
    {
        // return an identity (eg. email)
        $this->container['auth']
            ->method('getAttributes')
            ->willReturn( array_intersect_key($this->account->toArray(), array_flip(array(
                'id',
                'first_name',
                'last_name',
                'email',
            ))) );

        // by defaut, we'll make isAuthenticated return a false
        $this->container['auth']
            ->method('isAuthenticated')
            ->willReturn( true );
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
