<?php
namespace Application\Test\Controller;

use MartynBiz\Slim3Controller\Test\PHPUnit\TestCase;

class IndexControllerTest extends TestCase
{
    public function setUp()
    {
        // Instantiate the app and container

        // 1) load global
        $settings = require APPLICATION_PATH . '/config/global.php';

        // 2) TODO load modules config here before autoload

        // 3) autoload stuff, module config here will overwrite default
        $configPath = APPLICATION_PATH . '/config/';
        $autoloadPath = realpath($configPath . 'autoload/');
        if ($autoloadPath) {
            foreach (scandir($autoloadPath) as $file) {
                if ('.' === $file) continue;
                if ('..' === $file) continue;

                $settings = array_replace_recursive(
                    $settings,
                    require $autoloadPath . $file
                );
            }
        }

        // 4) overwrite all with environment config
        if (file_exists($configPath . APPLICATION_ENV . '.php')) {
            $settings = array_replace_recursive(
                $settings,
                require $configPath . APPLICATION_ENV . '.php'
            );
        }

        $this->app = $app = new \Slim\App($settings);
        $this->container = $app->getContainer();
    }

    public function test_index_action()
    {
        // dispatch
        $this->get('/');

        // assertions
        $this->assertController('index');
        $this->assertAction('index');
        $this->assertStatusCode(200);
    }
}
