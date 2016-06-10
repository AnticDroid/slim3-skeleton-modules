<?php
namespace Auth\Test\Middleware;

use Auth\Middleware\RoleAccess;
use Auth\Model\User;
use Slim\Container;
// use Slim\Http\Request;
// use Slim\Http\Response;
// use Slim\Route;

class RoleAccessTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Slim\Container_mock
     */
    private $container;

    /**
     * @var Slim\Route_mock
     */
    private $next;

    public function setUp()
    {
        $this->container = new Container();

        // mock objects
        $this->container['auth'] = $this->getMockBuilder('Auth\Auth')
            ->disableOriginalConstructor()
            ->getMock();

        $this->container['request'] = $this->getMockBuilder('Slim\Http\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $this->container['response'] = $this->getMockBuilder('Slim\Http\Response')
            ->disableOriginalConstructor()
            ->getMock();

        $this->next = $this->getMockBuilder('Slim\Route')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function test_initialization()
    {
        $roleAccess = new RoleAccess($this->container, []);

        $this->assertTrue($roleAccess instanceof RoleAccess);
    }

    public function test_invoke_with_string_role_when_access_granted()
    {
        // mock
        $user = new User();
        $user->role = "member";

        $this->container['auth']
            ->expects( $this->once() )
            ->method('getCurrentUser')
            ->willReturn($user);

        $roleAccess = new RoleAccess($this->container, "member");

        $roleAccess($this->container['request'], $this->container['response'], $this->next);
    }

    public function test_invoke_with_array_role_when_access_granted()
    {
        // mock
        $user = new User();
        $user->role = "member";

        $this->container['auth']
            ->expects( $this->once() )
            ->method('getCurrentUser')
            ->willReturn($user);

        $roleAccess = new RoleAccess($this->container, ["member"]);

        $roleAccess($this->container['request'], $this->container['response'], $this->next);
    }

    public function test_invoke_with_multiple_roles_when_access_granted()
    {
        // mock
        $user = new User();
        $user->role = "member";

        $this->container['auth']
            ->expects( $this->once() )
            ->method('getCurrentUser')
            ->willReturn($user);

        $roleAccess = new RoleAccess($this->container, ["member", "editor"]);

        $roleAccess($this->container['request'], $this->container['response'], $this->next);
    }

    /**
     * @expectedException Auth\Exception\PermissionDenied
     */
    public function test_invoke_with_when_access_not_granted()
    {
        // mock
        $user = new User();
        $user->role = "member";

        $this->container['auth']
            ->expects( $this->once() )
            ->method('getCurrentUser')
            ->willReturn($user);

        $roleAccess = new RoleAccess($this->container, "admin");

        $roleAccess($this->container['request'], $this->container['response'], $this->next);
    }
}
