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

require APPLICATION_PATH . '/../../vendor/autoload.php';

$settings = require APPLICATION_PATH . '/config/global.php';

// ================
// Session

// server should keep session data for AT LEAST 1 hour
ini_set('session.gc_maxlifetime', 3600);

// each client should remember their session id for EXACTLY 1 hour
session_set_cookie_params(3600);

// set session settings before session_start
ini_set('session.cookie_domain', @$settings['settings']['session']['cookie_domain']);
session_start();

// ================
// Eloquent

// initiate database connection
// setup eloquent for the job
$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($settings['settings']['eloquent']);
$capsule->setEventDispatcher( new \Illuminate\Events\Dispatcher( new \Illuminate\Container\Container ));
$capsule->bootEloquent();

// ================
// App

// Instantiate the app
$app = new Slim\App($settings);


// Set up dependencies
require APPLICATION_PATH . '/dependencies.php';

// Register middleware
require APPLICATION_PATH . '/middleware.php';

// Register routes
require APPLICATION_PATH . '/routes.php';

// Helper functions
require APPLICATION_PATH . '/helpers.php';

// Run app
$app->run();
