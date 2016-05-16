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
    "martynbiz/slim3-view": "dev-master",
    "illuminate/view": "^5.2"
},
"autoload": {
    "psr-4": {
        .
        .
        .
        "App\\Modules\\Auth\\": "app/modules/home/library/"
    }
}
```

Layouts

Defined outside the module, e.g. index.php

```
.
.
.
$container = $app->getContainer();
$engine = $container['renderer']->getEngine();
if (isset($settings['settings']['renderer']['layout_path'])) {
    $engine->addLocation($settings['settings']['renderer']['layout_path']);
}
```
