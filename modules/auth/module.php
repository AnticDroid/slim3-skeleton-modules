<?php
/**
 * this file should be included when loading the module
 * it will have access to $app which can be passed on to the
 * following files too
 */

// autoload classes
$classLoader->setPsr4("Auth\\", __DIR__ . "/src");

// merge config of this with app
$moduleSettings = require 'config/auth.config.php';
$container['settings']->__construct($moduleSettings);

require 'dependencies.php';
require 'routes.php';

// add home module's views dir
$templatePath = $settings['modules']['auth']['renderer']['template_path'];
$container['renderer']->addFolder('auth', $templatePath, true);
