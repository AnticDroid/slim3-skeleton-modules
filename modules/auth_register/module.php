<?php

// this file should be included when loading the module
// it will have access to $app which can be passed on to the
// following files too

$container = $app->getContainer();

// merge config of this with app
$moduleSettings = require 'config/module.php';
$container['settings']->__construct($moduleSettings);

// routes
$app->group('/auth/register', function () use ($app) {

    $controller = new App\Modules\AuthRegister\Controller\UsersController($app);

    $app->get('', $controller('create'))->setName('auth_users_create');
    $app->post('', $controller('post'))->setName('auth_users_post');

    $app->get('/resetpassword', $controller('resetpassword'))->setName('auth_users_reset_password');
    $app->post('/resetpassword', $controller('resetpassword'))->setName('auth_users_reset_password_post');
});

// add home module's views dir
$templatePath = $settings['modules']['auth_register']['renderer']['template_path'];
$container['renderer']->addFolder('auth_register', $templatePath, true);
