<?php

// Models
$container['model.user'] = function ($c) {
    return new \Auth\Model\User();
};

$container['auth'] = function ($c) {
    $settings = $c->get('settings')['auth'];
    $authAdapter = new \Auth\Adapter\Mongo( $c['model.user'] );
    return new \Auth\Auth($authAdapter, $settings);
};
