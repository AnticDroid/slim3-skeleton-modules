## Installation ##

During bootstrap (e.g. App's routes.php file)

```
$modules = new \MartynBiz\Slim3Modules\Loader($app);

$app->group('/auth', function () use ($modules) {
    $modules->load('auth');
}
```

composer.json

Ensure the following to your composer file:

```
"require": {
    .
    .
    .
    "martynbiz/slim3-controller": "dev-master",
    "martynbiz/slim3-view": "dev-master",
    "illuminate/view": "^5.2",
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
