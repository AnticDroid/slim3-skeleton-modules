<?php
namespace Auth\Test\Controller;

use Application\Test\Controller\ControllerTestCase;

class IndexControllerTest extends ControllerTestCase
{
    public function test_login_route()
    {
        // dispatch
        $this->get('/auth/login');

        // assertions
        $this->assertControllerClass('Auth\Controller\SessionController');
        $this->assertAction('index');
        $this->assertStatusCode(200);
    }

    public function test_logout_route()
    {
        // dispatch
        $this->get('/auth/logout');

        // assertions
        $this->assertControllerClass('Auth\Controller\SessionController');
        $this->assertAction('index');
        $this->assertStatusCode(200);
    }
}
