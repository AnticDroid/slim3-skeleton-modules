<?php
/**
 * this file should be included when loading the module
 * it will have access to $app which can be passed on to the
 * following files too
 */

namespace Application;

use Composer\Autoload\ClassLoader;
use Slim\App;
use Slim\Container;
use MartynBiz\Slim3Module\AbstractModule;

class Module extends AbstractModule
{
    /**
     * Get config array for this module
     * @return array
     */
    public function getModuleConfig()
    {
        return [
            'renderer' => [
                'folders' => [
                    // APPLICATION_PATH . '/views',
                    APPLICATION_PATH . '/modules/Application/views',
                ],
                'ext' => 'phtml',
                // 'autoescape' => true,
            ],

            'i18n' => [

                // when the target locale is missing a translation/ template this the
                // fallback locale to use (probably "en")
                'default_locale' => 'en',

                // this is the type of the translation files using by zend-i18n
                'type' => 'phparray',

                // where the translation files are stored
                'file_path' => APPLICATION_PATH . '/modules/Application/language/',
            ],

            'mail' => [

                // directory where suppressed email files are written to in non-prod env
                'file_path' => APPLICATION_PATH . '/data/mail/',
            ],

            'logger' => [
                'name' => 'slim3-module-app',
                'path' => APPLICATION_PATH . '/data/logs/app.log',
            ],

            'session' => [
                'namespace' => 'slim3__',
            ],
        ];
    }

    // /**
    //  * Set class maps for class loader to autoload classes for this module
    //  * @param ClassLoader $classLoader
    //  * @return void
    //  */
    // public function initClassLoader(ClassLoader $classLoader)
    // {
    //     $classLoader->setPsr4("Application\\", __DIR__ . "/src");
    // }

    /**
     * Set class maps for class loader to autoload classes for this module
     * @param Container $container
     * @return void
     */
    public function initDependencies(Container $container)
    {
        $settings = self::getModuleConfig();

        // replace request with our own
        $container['request'] = function($c) use ($settings) {
            return \Application\Http\Request::createFromEnvironment($c->get('environment'));
        };

        // replace reponse with our own
        $container['response'] = function($c) use ($settings) {
            $headers = new \Slim\Http\Headers(['Content-Type' => 'text/html; charset=UTF-8']);
            $response = new \Application\Http\Response(200, $headers);
            return $response->withProtocolVersion($c->get('settings')['httpVersion']);
        };

        // view renderer. the simple task of compiling a template with data
        $container['renderer'] = function($c) use ($settings) {
            $engine = \Foil\engine($settings['renderer']);
            $engine->registerFunction('translate', new \Application\View\Helper\Translate($c) );
            $engine->registerFunction('pathFor', new \Application\View\Helper\PathFor($c) );
            $engine->registerFunction('generateSortQuery', new \Application\View\Helper\GenerateSortQuery($c) );
            return $engine;
        };

        // monolog
        $container['logger'] = function($c) use ($settings) {
            $logger = new \Monolog\Logger($settings['logger']['name']);
            $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
            $logger->pushHandler(new \Monolog\Handler\StreamHandler($settings['logger']['path'], \Monolog\Logger::DEBUG));
            return $logger;
        };

        // locale - required by a few services, so easier to put in container
        $container['locale'] = function($c) use ($settings) {
            $locale = $c['request']->getCookie('language', $settings['i18n']['default_locale']);
            return $locale;
        };

        // i18n
        $container['i18n'] = function($c) use ($settings) {
            $translator = new \Zend\I18n\Translator\Translator();
            $translator->addTranslationFilePattern($settings['i18n']['type'], $settings['i18n']['file_path'], '/%s.php', 'default');
            $translator->setLocale($c['locale']);
            $translator->setFallbackLocale($settings['i18n']['default_locale']);
            return $translator;
        };

        // mail
        $container['mail_manager'] = function($c) use ($settings) {
            // if not in production, we will write to file
            if (APPLICATION_ENV == 'production') {
                $transport = new Zend\Mail\Transport\Sendmail();
            } else {
                $transport = new \Zend\Mail\Transport\File();
                $options   = new \Zend\Mail\Transport\FileOptions(array(
                    'path' => realpath($settings['mail']['file_path']),
                    'callback' => function (\Zend\Mail\Transport\File $transport) {
                        return 'Message_' . microtime(true) . '_' . mt_rand() . '.txt';
                    },
                ));
                $transport->setOptions($options);
            }

            $locale = $c['locale'];
            $defaultLocale = @$settings['i18n']['default_locale'];

            return new \Application\Mail($transport, $c['renderer'], $c['i18n'], $locale, $defaultLocale, $c['i18n']);
        };

        // flash
        $container['flash'] = function($c) use ($settings) {
            return new \MartynBiz\FlashMessage\Flash();
        };

        $container['csrf'] = function ($c) {
            return new \Slim\Csrf\Guard;
        };

        $container['session'] = function ($c) use ($settings) {
            $session_factory = new \Aura\Session\SessionFactory;
            $session = $session_factory->newInstance($_COOKIE);

            return $session->getSegment($settings['session']['namespace']);
        };

        $container['cache'] = function ($c) {
            $backend = new \Predis\Client(null, array(
                'prefix' => 'martynbiz__', // TODO move this into settings
            ));
            $adapter = new \Desarrolla2\Cache\Adapter\Predis($backend);
            return new \Desarrolla2\Cache\Cache($adapter);
        };




        // // add template_path folder to $engine
        // $settings = self::getModuleConfig();
        // $container['renderer']->addFolder($settings["renderer"]["template_path"]);
    }

    /**
     * Initiate app middleware (route middleware should go in initRoutes)
     * @param App $app
     * @return void
     */
    public function initMiddleware(App $app)
    {
        $container = $app->getContainer();

        // If you are implementing per-route checks you must not add this
        // $app->add($container->get('csrf'));
    }

    /**
     * Load is run last, when config, dependencies, etc have been initiated
     * Routes ought to go here
     * @param App $app
     * @return void
     */
    public function initRoutes(App $app)
    {
        $container = $app->getContainer();

        $app->group('', function () use ($app) {
            $app->get('/',
                '\Application\Controller\IndexController:index')->setName('home');
            $app->get('/portfolio',
                '\Application\Controller\IndexController:portfolio')->setName('portfolio');
            $app->get('/contact',
                '\Application\Controller\IndexController:contact')->setName('contact');
            $app->post('/contact',
                '\Application\Controller\IndexController:contact')->setName('contact');
        });

        $app->group('/admin', function () use ($app) {
            $app->group('', function () use ($app) {
                $app->get('',
                    '\Application\Controller\Admin\IndexController:index')->setName('admin');
            });
        })
        // ->add( new \Auth\Middleware\AdminOnly( $container['auth'] ) ) // user must be admin
        ->add( new \Auth\Middleware\Auth( $container['auth'] ) ); // user must be authenticated
    }
}
