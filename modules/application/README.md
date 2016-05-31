# Index module #

## Introduction ##

Mostly the static home files for this app

## Installation ##

During bootstrap (e.g. App's routes.php file)

```
$modules = new \MartynBiz\Slim3Module\Loader($app);

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

$container['flash'] = function ($c) {
    return new \MartynBiz\FlashMessage\Flash();
};
```
