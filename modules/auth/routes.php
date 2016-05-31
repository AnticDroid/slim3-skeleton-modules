<?php

$app->group('/auth', function () use ($app) {

    $controller = new \Auth\Controller\SessionController($app);

    $app->get('/login', $controller('index'))->setName('auth_session_index');
    $app->post('', $controller('post'))->setName('auth_session_post');
    $app->delete('', $controller('delete'))->setName('auth_session_delete');
});
