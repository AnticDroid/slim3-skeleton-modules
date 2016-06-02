<?php
namespace Tests\Library;

use App\Auth\Auth;

// TODO we need our Session manager/container that will allow us to mock session
//   getter setter actions. this will be useful to have under unit testing

class AuthTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var App\Auth\Adapter\Eloquent
     */
    protected $apapterMock;

    public function setUp()
    {
        // mock the adapter
        $this->apapterMock = $this->getMockBuilder('App\Auth\Adapter\Eloquent')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testInstantiation()
    {
        $auth = new Auth($this->apapterMock);

        $this->assertTrue($auth instanceof Auth);
    }
}
