<?php

$controller = new App\Controller\IndexController($app);

$app->get('', $controller('index'))->setName('index_index');
