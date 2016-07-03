<?php
/**
 * this file should be included when loading the module
 * it will have access to $app which can be passed on to the
 * following files too
 */

namespace Auth;

use Composer\Autoload\ClassLoader;
use Slim\App;
use Slim\Container;
use MartynBiz\Slim3Module\AbstractModule;
use MartynBiz\Mongo\Connection;

use Auth\Model\User;

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
                'template_path' => APPLICATION_PATH . '/modules/Auth/views',
            ],

            'auth' => [

                // this is the session namespace. apps that want to authenticate
                // using this auth app must configure their mwauth-client to match
                'namespace' => 'slim3__auth__',

                // remember me cookie settings
                'auth_token' => [
                    'cookie_name' => 'auth_token',
                    'expire' => strtotime("+3 months", time()), // seconds from now
                    'path' => '/',
                ],

                // remember me cookie settings
                'recovery_token' => [
                    'expire' => strtotime("+1 hour", time()), // seconds from now
                ],

                // these are attributes that will be written to session
                'valid_attributes' => [
                    'id',
                    'first_name',
                    'last_name',
                    'name',
                    'email',
                    'facebook_id',
                ],
            ],

            'mongo' => [
                'classmap' => [
                    'users' => '\\Auth\\Model\\User',
                ],
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
    //     $classLoader->setPsr4("Auth\\", __DIR__ . "/src");
    // }

    /**
     * Set class maps for class loader to autoload classes for this module
     * @param Container $container
     * @return void
     */
    public function initDependencies(Container $container)
    {
        $settings = self::getModuleConfig();

        // Models
        $container['auth.model.user'] = function ($c) {
            return new \Auth\Model\User();
        };

        $container['auth'] = function ($c) {
            $settings = $c->get('settings')['modules']['Auth']['auth'];
            $authAdapter = new \Auth\Adapter\Mongo( $c['auth.model.user'] );
            return new \Auth\Auth($authAdapter, $settings);
        };

        // add template_path folder to $engine
        $container['renderer']->addFolder($settings["renderer"]["template_path"]);

        // add models to mongo
        Connection::getInstance()->appendClassMap($settings['mongo']['classmap']);
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

        $app->group('/auth', function () use ($app) {

            $app->post('',
                '\Auth\Controller\SessionController:post')->setName('auth_session_post');
            $app->delete('',
                '\Auth\Controller\SessionController:delete')->setName('auth_session_delete');
            $app->get('/login',
                '\Auth\Controller\SessionController:index')->setName('auth_session_login');
            $app->get('/logout',
                '\Auth\Controller\SessionController:index')->setName('auth_session_logout');

            $app->get('/register',
                '\Auth\Controller\UsersController:create')->setName('auth_users_create');
            $app->post('/register',
                '\Auth\Controller\UsersController:post')->setName('auth_users_post');
            $app->get('/resetpassword',
                '\Auth\Controller\UsersController:resetpassword')->setName('auth_users_reset_password');
            $app->post('/resetpassword',
                '\Auth\Controller\UsersController:resetpassword')->setName('auth_users_reset_password_post');
        });

        // admin routes -- invokes auth middleware
        $app->group('/admin', function () use ($app, $container) {

            // admin/users routes
            $app->group('/users', function () use ($app, $container) {

                $app->get('',
                    '\Auth\Controller\Admin\UsersController:index')->setName('admin_users');
                $app->get('/{id:[0-9]+}',
                    '\Auth\Controller\Admin\UsersController:show')->setName('admin_users_show');
                $app->get('/create',
                    '\Auth\Controller\Admin\UsersController:create')->setName('admin_users_create');
                $app->get('/{id:[0-9]+}/edit',
                    '\Auth\Controller\Admin\UsersController:edit')->setName('admin_users_edit');

                $app->put('/{id:[0-9]+}',
                    '\Auth\Controller\Admin\UsersController:update')->setName('admin_users_update');
                $app->delete('/{id:[0-9]+}',
                    '\Auth\Controller\Admin\UsersController:delete')->setName('admin_users_delete');

            })->add( new \Auth\Middleware\RoleAccess($container, [ User::ROLE_ADMIN ]) );

        })->add( new \Auth\Middleware\Auth( $container['auth'] ) );
    }
}
