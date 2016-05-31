<?php

// module routes
$app->group('', function () use ($app) {

    $controller = new App\Controller\IndexController($app);

    $app->get('/', $controller('index'))->setName('home_index');
    $app->get('/portfolio', $controller('portfolio'))->setName('home_portfolio');

    $app->get('/contact', $controller('contact'))->setName('home_contact');
    $app->post('/contact', $controller('contact'))->setName('home_contact');
});

$module->load('auth');
// $module->load('articles');
