<?php

$app->group('/auth', function () use ($app) {

    // /auth/session
    $app->group('/facebook', function () use ($app) {

        $controller = new App\Modules\AuthFacebook\Controller\FacebookController($app);

        $app->post('', $controller('facebook'))->setName('auth_facebook');
        $app->get('/callback', $controller('facebookCallback'))->setName('auth_facebook_callback');
    });
});
