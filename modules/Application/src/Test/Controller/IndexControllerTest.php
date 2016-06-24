<?php
namespace Application\Test\Controller;

class IndexControllerTest extends ControllerTestCase
{
    // public function test_index_route()
    // {
    //     // dispatch
    //     $this->get('/');
    //
    //     // assertions
    //     $this->assertControllerClass('Application\Controller\IndexController');
    //     $this->assertAction('index');
    //     $this->assertStatusCode(200);
    // }

    public function test_portfolio_route()
    {
        // dispatch
        $this->get('/portfolio');

        // assertions
        $this->assertControllerClass('Application\Controller\IndexController');
        $this->assertAction('portfolio');
        $this->assertStatusCode(200);
    }

    // public function test_contact_route()
    // {
    //     // dispatch
    //     $this->get('/contact');
    //
    //     // assertions
    //     $this->assertControllerClass('Application\Controller\IndexController');
    //     $this->assertAction('contact');
    //     $this->assertStatusCode(200);
    // }
}
