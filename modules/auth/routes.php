<?php

$app->group('/auth', function () use ($app) {

    $controller = new \Auth\Controller\SessionController($app);

    $app->post('', $controller('post'))->setName('auth_session_post');
    $app->delete('', $controller('delete'))->setName('auth_session_delete');

    $app->get('/login', $controller('index'))->setName('auth_session_login');

    $app->group('/register', function () use ($app) {

        $controller = new \Auth\Controller\UsersController($app);

        $app->get('', $controller('create'))->setName('auth_users_create');
        $app->post('', $controller('post'))->setName('auth_users_post');

        $app->get('/resetpassword', $controller('resetpassword'))->setName('auth_users_reset_password');
        $app->post('/resetpassword', $controller('resetpassword'))->setName('auth_users_reset_password_post');
    });
});
