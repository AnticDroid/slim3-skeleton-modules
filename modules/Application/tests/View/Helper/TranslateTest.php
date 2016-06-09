<?php
namespace Application\Test\View\Helper;

use Application\View\Helper\Translate;
use Slim\Container;

class TranslateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Slim\Container_mock
     */
    private $container;

    public function setUp()
    {
        $this->container = new Container();

        // mock objects
        $this->container['i18n'] = $this->getMockBuilder('Zend\I18n\Translator\Translator')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function test_initialization()
    {
        $translate = new Translate($this->container);

        $this->assertTrue($translate instanceof Translate);
    }

    public function test_invoke_method_calls_i18n_translate_method()
    {
        $translate = new Translate($this->container);

        $this->container['i18n']
            ->expects($this->once())
            ->method('translate')
            ->with('hello')
            ->willReturn('bonjour');

        $this->assertEquals('bonjour', $translate('hello'));
    }
}
