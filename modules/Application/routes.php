<?php

$app->group('', function () use ($app) {

    $controller = new Application\Controller\IndexController($app);

    $app->get('/', $controller('index'))->setName('application_index');
    $app->get('/portfolio', $controller('portfolio'))->setName('application_portfolio');

    $app->get('/contact', $controller('contact'))->setName('application_contact');
    $app->post('/contact', $controller('contact'))->setName('application_contact');
});

// admin routes -- invokes auth middleware
$app->group('/admin', function () use ($app) {

    // admin/users routes
    $app->group('', function () use ($app) {

        $controller = new \Application\Controller\Admin\IndexController($app);

        $app->get('', $controller('index'))->setName('application_admin_index');
    });
})
// ->add( new \Auth\Middleware\AdminOnly( $container['auth'] ) ) // user must be admin
->add( new \Auth\Middleware\Auth( $container['auth'] ) ); // user must be authenticated
