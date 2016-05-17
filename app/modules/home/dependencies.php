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
