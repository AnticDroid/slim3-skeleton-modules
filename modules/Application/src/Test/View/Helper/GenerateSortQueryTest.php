<?php
namespace Application\Test\View\Helper;

use Application\View\Helper\GenerateSortQuery;
use Slim\Container;

class GenerateSortQueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Slim\Container_mock
     */
    private $container;

    public function setUp()
    {
        $this->container = new Container();
    }

    public function test_initialization()
    {
        $gsq = new GenerateSortQuery($this->container);

        $this->assertTrue($gsq instanceof GenerateSortQuery);
    }

    public function test_invoke_method_returns_query_string()
    {
        $gsq = new GenerateSortQuery($this->container);

        $query = $gsq('title', ['search' => 'hot dogs']);
        $pairs = explode('&', $query);

        $this->assertEquals(3, count($pairs));
        $this->assertTrue(in_array('search=hot+dogs', $pairs));
        $this->assertTrue(in_array('dir=1', $pairs));
        $this->assertTrue(in_array('sort=title', $pairs));
    }

    public function test_invoke_method_toggles_dir()
    {
        $gsq = new GenerateSortQuery($this->container);

        $query = $gsq('title', ['search' => 'hot dogs', 'dir' => 1]);
        $pairs = explode('&', $query);

        $this->assertEquals(3, count($pairs));
        $this->assertTrue(in_array('search=hot+dogs', $pairs));
        $this->assertTrue(in_array('dir=-1', $pairs));
        $this->assertTrue(in_array('sort=title', $pairs));
    }
}
