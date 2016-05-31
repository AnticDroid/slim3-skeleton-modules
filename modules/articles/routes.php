<?php
// Routes

use App\Middleware\Auth;

$container = $app->getContainer();

// homepage
$app->group('/', function () use ($app) {

    $controller = new App\Controller\IndexController($app);

    $app->get('', $controller('index'))->setName('index_index');
});
