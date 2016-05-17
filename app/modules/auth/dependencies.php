<?php

// view renderer. the simple task of compiling a template with data
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];

    // instantiate the Plates template engine
    $template = new League\Plates\Engine($settings['template_path']);

    // Sets the default file extension to ".phtml" after engine instantiation
    $template->setFileExtension('phtml');

    return $template;
};

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
