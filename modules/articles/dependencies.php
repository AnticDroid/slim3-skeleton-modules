<?php

$container = $app->getContainer();

// have seperated renderer and view so that we can use renderer also for emails

// view renderer. the simple task of compiling a template with data
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    $renderer = new \Windwalker\Renderer\BladeRenderer(array(
        $settings['template_path'],
    ), array(
        'cache_path' => $settings['cache_path'],
    ));

    return $renderer;
};

// bind the compiled template with the response object
$container['view'] = function ($c) {
    return new \MartynBiz\Slim3View\Renderer($c['renderer']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new \Monolog\Logger($settings['name']);
    $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
    $logger->pushHandler(new \Monolog\Handler\StreamHandler($settings['path'], \Monolog\Logger::DEBUG));
    return $logger;
};
