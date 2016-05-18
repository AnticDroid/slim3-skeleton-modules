<?php

// this file should be included when loading the module
// it will have access to $app which can be passed on to the
// following files too

$container = $app->getContainer();

// merge config of this with app
$moduleSettings = require 'config/module.php';
$container['settings']->__construct($moduleSettings);

require 'dependencies.php';
require 'routes.php';
