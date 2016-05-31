<?php

// Models
$container['model.account'] = function ($c) {
    return new \App\Modules\Auth\Model\Account();
};

$container['model.meta'] = function ($c) {
    return new \App\Modules\Auth\Model\Meta();
};

$container['model.auth_token'] = function ($c) {
    return new \App\Modules\Auth\Model\AuthToken();
};

$container['model.recovery_token'] = function ($c) {
    return new \App\Modules\Auth\Model\RecoveryToken();
};
