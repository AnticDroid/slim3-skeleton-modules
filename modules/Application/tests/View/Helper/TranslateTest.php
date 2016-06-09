<?php
namespace Application\Test\View\Helper;

use Application\View\Helper\Translate;
use Slim\Container;

class TranslateTest extends \PHPUnit_Framework_TestCase
{
    public function test_initialization()
    {
        $container = new Container();

        // mock objects
        $container['i18n'] = $this->getMockBuilder('Zend\I18n\Translator\Translator')
            ->disableOriginalConstructor()
            ->getMock();

        $translate = new Application\View\Helper\Translate($container);

        $this->assertTrue($translate instanceof Translate);
    }
}
