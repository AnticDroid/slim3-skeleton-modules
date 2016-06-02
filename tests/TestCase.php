<?php
namespace Tests;

use App\Model\Account;
use App\Model\Meta;
use App\Model\AuthToken;
use App\Model\RecoveryToken;

abstract class TestCase extends \MartynBiz\Slim3Controller\Test\PHPUnit\TestCase
{
    public function setUp()
    {
        $settings = require APPLICATION_PATH . '/config/global.php';

        // =========================
        // Init database

        $capsule = new \Illuminate\Database\Capsule\Manager;
        $capsule->addConnection($settings['settings']['eloquent']);
        $capsule->bootEloquent();
    }

    public function tearDown()
    {
        $settings = require APPLICATION_PATH . '/config/global.php';

        // as we have foreign key constraints on meta, we cannot use
        // truncate (even if the table is empty). so we need to temporarily
        // turn off FOREIGN_KEY_CHECKS

        $connection = (new Account())->getConnection();

        // in vagrant, we have an sqlite db. we may still want to run tests there too
        // to ensure the installation is working ok. so we need to disable foreign keys
        // different from mysql
        switch($settings['settings']['eloquent']['driver']) {
            case 'sqlite':
                $connection->statement('PRAGMA foreign_keys = OFF;');
                break;
            case 'mysql':
            default:
                $connection->statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        // clear tables
        Account::truncate();
        Meta::truncate();
        AuthToken::truncate();
        RecoveryToken::truncate();

        // turn foreign key checks back on
        switch($settings['settings']['eloquent']['driver']) {
            case 'sqlite':
                $connection->statement('PRAGMA foreign_keys = ON;');
                break;
            case 'mysql':
            default:
                $connection->statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }

    /**
     * Will try to find the fixture based on $queryColumn if given, otherwise
     * will create a new row.
     * "Integrity constraint violation" error when I simply use Eloquent's
     * create(...) method.
     */
    protected function findOrCreate($model, $values, $queryColumn=null)
    {
        // try to find if $queryColumn given
        if ($queryColumn) {
            $fixture = $model->where($queryColumn, $values[$queryColumn])
                ->first();

            if ($fixture) {
                return $fixture;
            }
        }

        $fixture = $model->create($values);

        return $fixture;
    }
}
