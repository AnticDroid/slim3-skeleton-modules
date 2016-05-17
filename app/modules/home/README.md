## Installation ##

During bootstrap (e.g. App's routes.php file)

```
$modules = new \MartynBiz\Slim3Modules\Loader($app);

$modules->load('home');
```

composer.json

Ensure the following to your composer file:

```
"require": {
    .
    .
    .
    "martynbiz/slim3-controller": "dev-master",
    "league/plates": "^3.1"
},
"autoload": {
    "psr-4": {
        .
        .
        .
        "App\\Modules\\Home\\": "app/modules/home/library/"
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
