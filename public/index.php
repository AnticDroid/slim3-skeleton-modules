<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../app'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $file = __DIR__ . $_SERVER['REQUEST_URI'];
    if (is_file($file)) {
        return false;
    }
}

$loader = require APPLICATION_PATH . '/../vendor/autoload.php';
$settings = require APPLICATION_PATH . '/config/global.php';

// ================
// App

// Instantiate the app
$app = new Slim\App($settings);
$container = $app->getContainer();
$module = new \MartynBiz\Slim3Module\Module($app, $loader);

MartynBiz\Mongo\Connection::getInstance()->init($settings['settings']['mongo']);

// Register dependencies
require APPLICATION_PATH . '/dependencies.php';

// Register middleware
require APPLICATION_PATH . '/middleware.php';

// Register routes
require APPLICATION_PATH . '/routes.php';

// if you want layouts outside the modules, this will add a new
// view directory for our layouts
$container['renderer']->addFolder('shared', APPLICATION_PATH . '/views/', true);

// Run app
$app->run();
