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



// config

/*
TODO
$config = new Application\Config();

$config->mergeFile([
    APPLICATION_PATH . '/config/global.php',
]);
$config->merge( $moduleInitializer->getModulesConfig() );
$config->mergeFile([
    APPLICATION_PATH . '/config/autoload/',
    APPLICATION_PATH . '/config/' . APPLICATION_ENV . '.php'
]);

$settings = $config->get();
*/

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


// // Session
// session_start();


// App

// Instantiate the app
$classLoader = require APPLICATION_PATH . '/vendor/autoload.php';
$app = new Slim\App($settings);
$container = $app->getContainer();

MartynBiz\Mongo\Connection::getInstance()->init($settings['settings']['mongo']);

// initialize all modules in settings > modules > autoload [...]
$moduleInitializer = new \MartynBiz\Slim3Module\Initializer($app, $classLoader, $settings['settings']['module_initializer']);
$moduleInitializer->initModules();

// Run app
$app->run();
