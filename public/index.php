<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../'));

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

$loader = require APPLICATION_PATH . '/vendor/autoload.php';
$settings = require APPLICATION_PATH . '/config/global.php';


// Session

// // server should keep session data for AT LEAST 1 hour
// ini_set('session.gc_maxlifetime', 3600);
//
// // each client should remember their session id for EXACTLY 1 hour
// session_set_cookie_params(3600);
//
// // set session settings before session_start
// ini_set('session.cookie_domain', @$settings['settings']['session']['cookie_domain']);
session_start();


// App

// Instantiate the app
$app = new Slim\App($settings);
$container = $app->getContainer();

MartynBiz\Mongo\Connection::getInstance()->init($settings['settings']['mongo']);

$module = new \MartynBiz\Slim3Module\Module($app, $loader, $settings['settings']['modules']);

// // Register dependencies
// require APPLICATION_PATH . '/dependencies.php';
//
// // Register middleware
// require APPLICATION_PATH . '/middleware.php';
//
// // Register routes
// require APPLICATION_PATH . '/routes.php';

// Run app
$app->run();
