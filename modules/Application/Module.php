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
    public static function getModuleConfig()
    {
        return [
            'modules' => [
                'Application' => [
                    'renderer' => [
                        'template_path' => APPLICATION_PATH . '/modules/Application/views',
                    ],
                ],
            ],
        ];
    }

    /**
     * Set class maps for class loader to autoload classes for this module
     * @param ClassLoader $classLoader
     * @return void
     */
    public static function initClassLoader(ClassLoader $classLoader)
    {
        $classLoader->setPsr4("Application\\", __DIR__ . "/src");
    }

    /**
     * Set class maps for class loader to autoload classes for this module
     * @param Container $container
     * @return void
     */
    public static function initDependencies(Container $container)
    {
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
            $engine = \Foil\engine([
                'ext' => 'phtml'
            ]);

            return $engine;

            // $settings = $c->get('settings')['renderer'];
            // $template = new \League\Plates\Engine($settings['template_path']);
            // $template->setFileExtension('phtml');
            //
            // // TODO put helpers into invokable classes so we can test them
            //
            // // This helper will handle out translations
            // $template->registerFunction('translate', function ($string) use ($c) {
            //     return $c['i18n']->translate($string);
            // });
            //
            // // This helper will allow us to use named links - $this->pathFor('application_index')
            // $template->registerFunction('pathFor', function ($name, $args=array()) use ($c) {
            //     return $c['router']->pathFor($name, $args);
            // });
            //
            // return $template;
        };

        // locale - required by a few services, so easier to put in container
        $container['locale'] = function($c) {
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


        // add folder to $engine
        $settings = self::getModuleConfig();
        $templatePath = $settings["modules"]["Application"]["renderer"]["template_path"];
        $container['renderer']->addFolder($templatePath);
    }

    /**
     * Load is run last, when config, dependencies, etc have been initiated
     * Routes ought to go here
     * @param App $app
     * @return void
     */
    public static function initRoutes(App $app)
    {
        $container = $app->getContainer();

        $app->group('', function () use ($app) {

            $controller = new \Application\Controller\IndexController($app);

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
