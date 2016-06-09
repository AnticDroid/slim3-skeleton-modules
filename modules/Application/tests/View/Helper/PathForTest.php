<?php
namespace Application\Test\View\Helper;

use Application\View\Helper\Translate;
use Slim\Container;

class PathForTest extends \PHPUnit_Framework_TestCase
{
    public function test_initialization()
    {
        $container = new Container();

        // mock objects
        $container['router'] = $this->getMockBuilder('Slim\Router')
            ->disableOriginalConstructor()
            ->getMock();

        $translate = new Translate($container);

        $this->assertTrue($translate instanceof Translate);
    }
}
