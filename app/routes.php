<?php
// Routes

$modules = new \MartynBiz\Slim3Modules\Loader($app);

// homepage
$app->group('/', function () use ($app) {
    $modules->load('home');
});
