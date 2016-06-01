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

// admin routes -- invokes auth middleware
$app->group('/admin', function () use ($app) {

    // admin/users routes
    $app->group('/users', function () use ($app) {

        $controller = new \Auth\Controller\Admin\UsersController($app);

        $app->get('', $controller('index'))->setName('admin_users');
        // $app->get('/{id:[0-9]+}', $controller('show'))->setName('admin_users_show');
        // $app->get('/create', $controller('create'))->setName('admin_users_create');
        $app->get('/{id:[0-9]+}/edit', $controller('edit'))->setName('admin_users_edit');

        // $app->post('', $controller('post'))->setName('admin_users_post');
        $app->put('/{id:[0-9]+}', $controller('update'))->setName('admin_users_update');
        $app->delete('/{id:[0-9]+}', $controller('delete'))->setName('admin_users_delete');
    });
})
// ->add( new \Auth\Middleware\AdminOnly( $container['auth'] ) ) // user must be admin
->add( new \Auth\Middleware\Auth( $container['auth'] ) ); // user must be authenticated
