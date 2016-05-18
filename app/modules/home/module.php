<?php
/**
 * This is the script that the app runs when loading the module
 * put ny bootstrappy stuff in here
 */

$container = $app->getContainer();

// module settings
$moduleSettings = require 'config/module.php';
$container['settings']->__construct($moduleSettings);

// module routes
$app->group('/', function () use ($app) {
    $controller = new App\Modules\Home\Controller\IndexController($app);

    $app->get('', $controller('index'))->setName('index_index');
});

// module dependencies

// add home module's views dir
$templatePath = $settings['modules']['home']['renderer']['template_path'];
$container['renderer']->addFolder('home', $templatePath, true);
