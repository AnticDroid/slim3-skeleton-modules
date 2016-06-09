<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../../../'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'testing'));

if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $file = __DIR__ . $_SERVER['REQUEST_URI'];
    if (is_file($file)) {
        return false;
    }
}



require APPLICATION_PATH . '/vendor/autoload.php';

// // Config
// $settings = require APPLICATION_PATH . '/config/global.php';
//
// // Instantiate the app
// $app = new Slim\App($settings);
// $container = $app->getContainer();
//
// MartynBiz\Mongo\Connection::getInstance()->init($settings['settings']['mongo']);
//
// // initialize all modules in settings > modules > autoload [...]
// $moduleInitializer = new \MartynBiz\Slim3Module\Initializer($app, $settings['settings']['module_initializer']);
// $moduleInitializer->initModules();

// // Run app
// $app->run();
