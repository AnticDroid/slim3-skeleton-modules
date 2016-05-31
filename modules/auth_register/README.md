# auth_users module #

## Introduction ##

Allows public users to register accounts. Requires "auth" module to also be installed.

## Installation ##

During bootstrap (e.g. App's routes.php file)

```
$modules = new \MartynBiz\Slim3Module\Loader($app);

$modules->load('auth');
$modules->load('auth_register');
```

composer.json

Ensure the following to your composer file:

```
"require": {
    .
    .
    .
    "martynbiz/slim3-controller": "dev-master",
    "martynbiz/php-mongo": "dev-master",
    "league/plates": "^3.1"
},
"autoload": {
    "psr-4": {
        .
        .
        .
        "App\\Modules\\AuthRegister\\": "app/modules/auth_register/library/"
    }
}
```

Layouts

The following is required to share layouts with other modules:

```
.
.
.
$container['renderer']->addFolder('shared', APPLICATION_PATH . '/views/', true);
```

Dependencies

Add the following to the app's dependencies:

```php
$container['renderer'] = function ($c) {
    $template = new League\Plates\Engine();
    $template->setFileExtension('phtml');
    return $template;
};

$container['auth'] = function ($c) {
    $settings = $c->get('settings')['auth'];
    $authAdapter = new \App\Modules\Auth\Adapter\Eloquent( $c['model.account'] );
    return new \App\Modules\Auth\Auth($authAdapter, $settings);
};

$container['flash'] = function ($c) {
    return new \MartynBiz\FlashMessage\Flash();
};

$container['mail_manager'] = ...
```
