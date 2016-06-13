<?php
namespace Application\Test\View\Helper;

use Application\View\Helper\PathFor;
use Slim\Container;

class PathForTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Slim\Container_mock
     */
    private $container;

    public function setUp()
    {
        $this->container = new Container();

        // mock objects
        $this->container['router'] = $this->getMockBuilder('Slim\Router')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function test_initialization()
    {
        $pathFor = new PathFor($this->container);

        $this->assertTrue($pathFor instanceof PathFor);
    }

    public function test_invoke_calls_router_pathfor_method()
    {
        $pathFor = new PathFor($this->container);

        $this->container['router']
            ->expects($this->once())
            ->method('pathFor')
            ->with('articles_index', [ 'name' => 'Martyn' ])
            ->willReturn('/articles');

        $this->assertEquals('/articles', $pathFor('articles_index', [ 'name' => 'Martyn' ]));
    }
}
