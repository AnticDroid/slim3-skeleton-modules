<?php

$app->group('/', function () use ($app) {
    $controller = new App\Modules\Home\Controller\IndexController($app);
    
    $app->get('', $controller('index'))->setName('index_index');
});
