<?php
namespace Application\Test\Controller;

use MartynBiz\Mongo\Connection;

class IndexControllerTest extends ControllerTestCase
{
    public function test_index_route()
    {
        // dispatch
        $this->get('/');

        // assertions
        $this->assertController('index');
        $this->assertAction('index');
        $this->assertStatusCode(200);
    }
}
