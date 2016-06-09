<?php
namespace Application\Test\Controller\Admin;

use MartynBiz\Mongo\Connection;
use Application\Test\Controller\ControllerTestCase;

class IndexControllerTest extends ControllerTestCase
{
    public function test_index_route()
    {
        // sign in user
        $this->login( $this->user );

        // dispatch
        $this->get('/admin');

        // assertions
        $this->assertController('index');
        $this->assertAction('index');
        $this->assertStatusCode(200);
    }
}
