<?php

// /auth/session
$app->group('/session', function () use ($app) {

    $controller = new App\Controller\SessionController($app);

    $app->get('', $controller('index'))->setName('session_index');
    $app->post('', $controller('post'))->setName('session_post');
    $app->delete('', $controller('delete'))->setName('session_delete');

    $app->post('/facebook', $controller('facebook'))->setName('session_facebook');
    $app->get('/facebook/callback', $controller('facebookCallback'))->setName('session_facebook_callback');
});

// /auth/users 
$app->group('/users', function () use ($app) {

    $controller = new App\Controller\AccountsController($app);

    $app->get('', $controller('create'))->setName('accounts_create');
    $app->post('', $controller('post'))->setName('accounts_post');

    $app->get('/resetpassword', $controller('resetpassword'))->setName('accounts_reset_password');
    $app->post('/resetpassword', $controller('resetpassword'))->setName('accounts_reset_password_post');
});
