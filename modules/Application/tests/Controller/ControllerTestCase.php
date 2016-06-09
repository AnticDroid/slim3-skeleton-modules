<?php
namespace Application\Test\Controller;

use MartynBiz\Slim3Controller\Test\PHPUnit\TestCase;
use MartynBiz\Mongo\Connection;

class ControllerTestCase extends TestCase
{
    public function setUp()
    {
        // // Instantiate the app and container
        //
        // // 1) load global
        // $settings = require APPLICATION_PATH . '/config/global.php';
        //
        // // 2) TODO load modules config here before autoload
        //
        // // 3) autoload stuff, module config here will overwrite default
        // $configPath = APPLICATION_PATH . '/config/';
        // $autoloadPath = realpath($configPath . 'autoload/');
        // if ($autoloadPath) {
        //     foreach (scandir($autoloadPath) as $file) {
        //         if ('.' === $file) continue;
        //         if ('..' === $file) continue;
        //
        //         $settings = array_replace_recursive(
        //             $settings,
        //             require $autoloadPath . $file
        //         );
        //     }
        // }
        //
        // // 4) overwrite all with environment config
        // if (file_exists($configPath . APPLICATION_ENV . '.php')) {
        //     $settings = array_replace_recursive(
        //         $settings,
        //         require $configPath . APPLICATION_ENV . '.php'
        //     );
        // }
        //
        // // App
        //
        // // Instantiate the app
        // $classLoader = require APPLICATION_PATH . '/vendor/autoload.php';
        // $app = new \Slim\App($settings);

        $app = $GLOBALS["app"];
        $container = $app->getContainer();

        // Connection::getInstance()->init($settings['settings']['mongo']);

        // initialize all modules in settings > modules > autoload [...]
        $moduleInitializer = new \MartynBiz\Slim3Module\Initializer($app, $classLoader, $settings['settings']['module_initializer']);
        $moduleInitializer->initModules();

        $this->app = $app;
        $this->container = $app->getContainer();
    }
}
