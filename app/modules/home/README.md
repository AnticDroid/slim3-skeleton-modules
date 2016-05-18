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

Dependencies

Add the following to the app's dependencies:

```php
// view renderer. the simple task of compiling a template with data
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];

    // instantiate the Plates template engine
    $template = new League\Plates\Engine($settings['template_path']);

    // Sets the default file extension to ".phtml" after engine instantiation
    $template->setFileExtension('phtml');

    return $template;
};
```

Layouts

The following is required to share layouts with other modules:

```
.
.
.
$container['renderer']->addFolder('shared', APPLICATION_PATH . '/views/', true);
```

TODO add layout to the app's shared dir
