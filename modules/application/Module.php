<?php
/**
 * this file should be included when loading the module
 * it will have access to $app which can be passed on to the
 * following files too
 */

namespace Application;

use MartynBiz\Slim3Module\AbstractModule;

class Module extends AbstractModule implements InitClassLoaderInterface, InitDependenciesInterface, InitMiddlewareInterface
{
    /**
     *
     */
    public static function initClassLoader()
    {
        $this->classLoader->setPsr4("Application\\", __DIR__ . "/src");
    }

    /**
     *
     */
    public static function initDependencies()
    {
        $container = $this->container;

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

            // This function will handle out translations
            $template->registerFunction('translate', function ($string) use ($c) {
                return $c['i18n']->translate($string);
            });

            return $template;
        };

        // $container['auth'] = function ($c) {
        //     $settings = $c->get('settings')['auth'];
        //     $authAdapter = new \App\Modules\Auth\Adapter\Mongo( $c['model.user'] );
        //     return new \App\Modules\Auth\Auth($authAdapter, $settings);
        // };

        // locale - required by a few services, so easier to put in container
        $container['locale'] = function($c) use ($app) {
            $settings = $c->get('settings')['i18n'];
            $locale = $c['request']->getCookie('language', $settings['default_locale']);

            return $locale;
        };

        // i18n
        $container['i18n'] = function($c) {
            $settings = $c->get('settings')['i18n'];
            $translator = new \Zend\I18n\Translator\Translator();

            // get the language code from the cookie, then get the language file
            // if no language file, or no cookie even, get default language.
            $locale = $c['locale'];
            $type = $settings['type'];
            $filePath = $settings['file_path'];
            $pattern = '/%s.php';
            $textDomain = 'default';

            $translator->addTranslationFilePattern($type, $filePath, $pattern, $textDomain);
            $translator->setLocale($locale);
            $translator->setFallbackLocale($settings['default_locale']);

            return $translator;
        };

        // mail
        $container['mail_manager'] = function ($c) {
            $settings = $c->get('settings')['mail'];

            // if not in production, we will write to file
            if (APPLICATION_ENV == 'production') {
                $transport = new Zend\Mail\Transport\Sendmail();
            } else {
                $transport = new \Zend\Mail\Transport\File();
                $options   = new \Zend\Mail\Transport\FileOptions(array(
                    'path' => realpath($settings['file_path']),
                    'callback' => function (\Zend\Mail\Transport\File $transport) {
                        return 'Message_' . microtime(true) . '_' . mt_rand() . '.txt';
                    },
                ));
                $transport->setOptions($options);
            }

            $locale = $c['locale'];
            $defaultLocale = @$c->get('settings')['i18n']['default_locale'];

            return new \Application\Mail($transport, $c['renderer'], $c['i18n'], $locale, $defaultLocale, $c['i18n']);
        };

        // flash
        $container['flash'] = function ($c) {
            return new \MartynBiz\FlashMessage\Flash();
        };
    }

    /**
     *
     */
    public static function initMiddleware()
    {

    }

    /**
     *
     */
    public static function load()
    {
        $app = $this->app;

        $app->group('', function () use ($app) {

            $controller = new Application\Controller\IndexController($app);

            $app->get('/', $controller('index'))->setName('application_index');
            $app->get('/portfolio', $controller('portfolio'))->setName('application_portfolio');

            $app->get('/contact', $controller('contact'))->setName('application_contact');
            $app->post('/contact', $controller('contact'))->setName('application_contact');
        });

        // admin routes -- invokes auth middleware
        $app->group('/admin', function () use ($app) {

            // admin/users routes
            $app->group('', function () use ($app) {

                $controller = new \Application\Controller\Admin\IndexController($app);

                $app->get('', $controller('index'))->setName('application_admin_index');
            });
        })
        // ->add( new \Auth\Middleware\AdminOnly( $container['auth'] ) ) // user must be admin
        ->add( new \Auth\Middleware\Auth( $container['auth'] ) ); // user must be authenticated
    }
}

// merge config of this with app
$moduleSettings = require 'config/module.config.php';
$container['settings']->__construct($moduleSettings);

require 'dependencies.php';
require 'routes.php';

// // add home module's views dir
// $templatePath = $settings['renderer']['template_path'];
// $container['renderer']->addFolder('application', $templatePath, true);
