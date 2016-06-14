<?php
namespace Blog\Test\Controller\Admin;

use Application\Test\Controller\ControllerTestCase;

class ArticlesControllerTest extends ControllerTestCase
{
    public function test_index_route()
    {
        // sign in user
        $this->login( $this->user );

        // dispatch
        $this->get('/admin/articles');

        // assertions
        $this->assertControllerClass('Blog\Controller\Admin\ArticlesController');
        $this->assertAction('index');
        $this->assertStatusCode(200);
    }
}
