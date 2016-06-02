<?php
namespace Tests\Models;

use App\Model\Meta;

class MetaTest extends \Tests\TestCase //\PHPUnit_Framework_TestCase
{
    // public function tearDown()
    // {
    //     // as we have foreign key constraints on meta, we cannot use
    //     // truncate (even if the table is empty). so we need to temporarily
    //     // turn off FOREIGN_KEY_CHECKS
    //
    //     $connection = (new Meta())->getConnection();
    //     $connection->statement('SET FOREIGN_KEY_CHECKS=0;');
    //
    //     // // clear tables
    //     Meta::truncate();
    //
    //     $connection->statement('SET FOREIGN_KEY_CHECKS=1;');
    // }

    public function test_instantiation()
    {
        $user = new Meta();
        $this->assertTrue($user instanceof Meta);
    }
}
