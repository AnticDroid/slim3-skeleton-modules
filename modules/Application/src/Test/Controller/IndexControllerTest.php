<?php
namespace Application\Test\Controller;

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

    public function test_portfolio_route()
    {
        // dispatch
        $this->get('/portfolio');

        // assertions
        $this->assertController('index');
        $this->assertAction('portfolio');
        $this->assertStatusCode(200);
    }

    public function test_contact_route()
    {
        // dispatch
        $this->get('/contact');

        // assertions
        $this->assertController('index');
        $this->assertAction('contact');
        $this->assertStatusCode(200);
    }
}
