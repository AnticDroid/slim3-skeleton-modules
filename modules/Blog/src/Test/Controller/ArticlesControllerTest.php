<?php
namespace Blog\Test\Controller;

use Application\Test\Controller\ControllerTestCase;

class ArticlesControllerTest extends ControllerTestCase
{
    public function test_index_route()
    {
        // dispatch
        $this->get('/articles');

        // assertions
        $this->assertControllerClass('Blog\Controller\ArticlesController');
        $this->assertAction('index');
        $this->assertStatusCode(200);
    }
}
