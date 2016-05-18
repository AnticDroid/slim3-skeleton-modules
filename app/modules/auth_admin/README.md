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
    "league/plates": "^3.1",
    "robmorgan/phinx": "^0.5.1"
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

Migrations

```
$ cp -r app/modules/auth/db/* db/
$ vendor/bin/phinx migrate

Layouts

The following is required to share layouts with other modules:

```
.
.
.
$container['renderer']->addFolder('shared', APPLICATION_PATH . '/views/', true);
```
