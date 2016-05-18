# Auth module #

## Introduction ##

Provides basic login/logout functionality. Requires auth_register to allow users
to sign up, or auth_admin, to create users.

## Installation ##

During bootstrap (e.g. App's routes.php file)

```
$modules = new \MartynBiz\Slim3Modules\Loader($app);

$modules->load('auth');
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
        "App\\Modules\\Auth\\": "app/modules/auth/library/"
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
```
