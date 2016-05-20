<?php

$container = $app->getContainer();

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new \Monolog\Logger($settings['name']);
    $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
    $logger->pushHandler(new \Monolog\Handler\StreamHandler($settings['path'], \Monolog\Logger::DEBUG));
    return $logger;
};

// replace request with our own
$container['request'] = function ($c) {
    return \MartynBiz\Slim3Controller\Http\Request::createFromEnvironment($c->get('environment'));
};

// replace reponse with our own
$container['response'] = function ($c) {
    $headers = new \Slim\Http\Headers(['Content-Type' => 'text/html; charset=UTF-8']);
    $response = new \MartynBiz\Slim3Controller\Http\Response(200, $headers);
    return $response->withProtocolVersion($c->get('settings')['httpVersion']);
};

// view renderer. the simple task of compiling a template with data
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    $template = new League\Plates\Engine($settings['template_path']);
    $template->setFileExtension('phtml');
    return $template;
};

$container['auth'] = function ($c) {
    $settings = $c->get('settings')['auth'];
    $authAdapter = new \App\Modules\Auth\Adapter\Mongo( $c['model.user'] );
    return new \App\Modules\Auth\Auth($authAdapter, $settings);
};

// // locale - required by a few services, so easier to put in container
// $container['locale'] = function($c) use ($app) {
//     $settings = $c->get('settings')['i18n'];
//     $locale = $c['request']->getCookie('language', $settings['default_locale']);
//
//     return $locale;
// };
//
// // i18n
// $container['i18n'] = function($c) {
//     $settings = $c->get('settings')['i18n'];
//     $translator = new \Zend\I18n\Translator\Translator();
//
//     // get the language code from the cookie, then get the language file
//     // if no language file, or no cookie even, get default language.
//     $locale = $c['locale'];
//     $type = $settings['type'];
//     $filePath = $settings['file_path'];
//     $pattern = '/%s.php';
//     $textDomain = 'default';
//
//     $translator->addTranslationFilePattern($type, $filePath, $pattern, $textDomain);
//     $translator->setLocale($locale);
//     $translator->setFallbackLocale($settings['default_locale']);
//
//     return $translator;
// };

// // mail
// $container['mail_manager'] = function ($c) {
//     $settings = $c->get('settings')['mail'];
//
//     // if not in production, we will write to file
//     if (APPLICATION_ENV == 'production') {
//         $transport = new Zend\Mail\Transport\Sendmail();
//     } else {
//         $transport = new \Zend\Mail\Transport\File();
//         $options   = new \Zend\Mail\Transport\FileOptions(array(
//             'path' => realpath($settings['file_path']),
//             'callback' => function (\Zend\Mail\Transport\File $transport) {
//                 return 'Message_' . microtime(true) . '_' . mt_rand() . '.txt';
//             },
//         ));
//         $transport->setOptions($options);
//     }
//
//     $locale = $c['locale'];
//     $defaultLocale = @$c->get('settings')['i18n']['default_locale'];
//
//     return new \App\Mail\Manager($transport, $c['renderer'], $locale, $defaultLocale, $c['i18n']);
// };

// flash
$container['flash'] = function ($c) {
    return new \MartynBiz\FlashMessage\Flash();
};

// // facebook
// $container['facebook'] = function ($c) {
//     $settings = $c->get('settings')['facebook'];
//
//     return new \Facebook\Facebook([
//         'app_id' => $settings['app_id'], // Replace {app-id} with your app id
//         'app_secret' => $settings['secret'],
//         'default_graph_version' => $settings['version'],
//      ]);
// };
