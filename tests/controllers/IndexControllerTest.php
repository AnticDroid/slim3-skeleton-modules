<?php
namespace Tests\Controllers;

use Wordup\Model\Account;

class IndexControllerTest extends ControllerTestCase
{
    public function testIndexAction()
    {
        // =================================
        // dispatch

        $this->get('/');

        // =================================
        // assertions

        $this->assertController('index');
        $this->assertAction('index');
        $this->assertStatusCode(200);
    }
}
